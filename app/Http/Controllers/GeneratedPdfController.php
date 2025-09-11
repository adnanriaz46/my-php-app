<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneratedPdf;
use App\Models\PdfTemplate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class GeneratedPdfController extends Controller
{
    public function generate(Request $request, $id)
    {
        $template = PdfTemplate::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->input('data');

        // Download the template PDF from S3 to local storage
        $localTemplatePath = storage_path('app/tmp/' . basename($template->pdf_path));
        Storage::disk('s3')->getDriver()->getAdapter()->getClient()->getObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $template->pdf_path,
            'SaveAs' => $localTemplatePath,
        ]);

        // Call Node.js script to generate filled PDF
        $outputPath = storage_path('app/tmp/generated_' . uniqid() . '.pdf');
        $process = new Process([
            'node',
            base_path('pdf-generator/generate.js'),
            $localTemplatePath,
            json_encode($template->fields_json),
            json_encode($data),
            $outputPath
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => 'PDF generation failed'], 500);
        }

        // Upload generated PDF to S3
        $s3Path = 'generated-pdfs/' . basename($outputPath);
        Storage::disk('s3')->put($s3Path, file_get_contents($outputPath));
        $pdfUrl = Storage::disk('s3')->url($s3Path);

        // Save record
        $generated = GeneratedPdf::create([
            'user_id' => $request->user()->id,
            'template_id' => $template->id,
            'data_json' => $data,
            'pdf_url' => $pdfUrl,
        ]);

        // Clean up local files
        @unlink($localTemplatePath);
        @unlink($outputPath);

        return response()->json($generated, 201);
    }

    public function index(Request $request)
    {
        $pdfs = GeneratedPdf::where('user_id', $request->user()->id)->get();
        return response()->json($pdfs);
    }
} 