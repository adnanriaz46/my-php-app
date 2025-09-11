<?php

namespace App\Http\Controllers;

use App\Models\AwsS3History;
use App\Models\MlsOfferPdfTemplate;
use App\Models\UserPdfFillerConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Models\MlsOfferEmailTemplate;
use App\Helper\MlsOfferHelper;
use App\Models\MlsOfferGeneratedLog;

class MlsOfferController extends Controller
{
    // List all PDF templates
    public function index(Request $request)
    {
        $templates = MlsOfferPdfTemplate::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('mls-offers/PdfTemplates', [
            'templates' => $templates,
        ]);
    }

    // Store a new PDF template
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255|min:6',
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'additional_requests' => 'nullable|string',
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('pdf_file');
        $filename = Str::random(16) . '_' . $file->getClientOriginalName();


        $path = "mls_offer_templates/" . $request->user()->id . '/';
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . $filename;

        Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

        $url = Storage::disk('s3')->url($fullPath);
        AwsS3History::addHistory('mls_offer_templates', $fullPath, $url, $request->user()->id);

        $pdfFoldername = 'user-' . $request->user()->email . '/';
        $pdfFoldername = preg_replace('/[^a-zA-Z0-9\-_]+/', '', $pdfFoldername);


        $folder = MlsOfferHelper::createFolder($pdfFoldername, $request->user()->id);

        $templateId = MlsOfferHelper::addTemplate($url, $folder['folder_id'], $validated['template_name']);
        if ($templateId) {
            $template = MlsOfferPdfTemplate::create([
                'template_name' => $validated['template_name'],
                'template_id' => $templateId, // Placeholder, replace with real ID if using API
                'user_id' => $request->user()->id,
                'additional_requests' => $validated['additional_requests'] ?? null,
                'fillable' => false, // Default, update after processing
                'folder_id' => $folder['folder_id'], // Set if using folders
                'folder_name' => $folder['folder_name'], // Set if using folders
            ]);

            return redirect()->back()->with('success', 'Template uploaded successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to upload template.');
        }
    }


    public function downloadTemplate(Request $request, $id)
    {
        $template = MlsOfferPdfTemplate::where('user_id', $request->user()->id)
            ->findOrFail($id);
        $filePath = MlsOfferHelper::downloadTemplate($template->template_id);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'Failed to download the template.');
        }

        return response()->download($filePath, $template->template_name . '.pdf');
    }

    // Update a PDF template name
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255|min:6',
        ]);

        $template = MlsOfferPdfTemplate::where('user_id', $request->user()->id)->findOrFail($id);
        MlsOfferHelper::updateTemplateName($template->template_id, $validated['template_name'], $template->folder_id);
        $template->update([
            'template_name' => $validated['template_name'],
        ]);

        return redirect()->back()->with('success', 'Template name updated successfully.');
    }

    // Delete a template
    public function destroy(Request $request, $id)
    {
        $template = MlsOfferPdfTemplate::where('user_id', $request->user()->id)->findOrFail($id);
        // Optionally, delete file from storage if tracked
        // Storage::delete($template->file_path);
        $template->delete();
        return redirect()->back()->with('success', 'Template deleted successfully.');
    }

    // Download a template (stub)
    public function download(Request $request, $id)
    {
        // Implement file download logic here
        abort(501, 'Not implemented');
    }

    // List fillable fields (stub)
    public function listFields(Request $request, $id)
    {
        // Implement PDF field extraction logic here
        abort(501, 'Not implemented');
    }

    // List all email templates
    public function emailTemplates(Request $request)
    {
        $templates = MlsOfferEmailTemplate::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('mls-offers/EmailTemplates', [
            'templates' => $templates,
            'tags' => MlsOfferHelper::getTags(),
        ]);
    }

    // Store a new email template
    public function storeEmailTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        MlsOfferEmailTemplate::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
        ]);
        return redirect()->back()->with('success', 'Email template created successfully.');
    }

    // Update an email template
    public function updateEmailTemplate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $template = MlsOfferEmailTemplate::where('user_id', $request->user()->id)->findOrFail($id);
        $template->update($validated);
        return redirect()->back()->with('success', 'Email template updated successfully.');
    }

    // Delete an email template
    public function destroyEmailTemplate(Request $request, $id)
    {
        $template = MlsOfferEmailTemplate::where('user_id', $request->user()->id)->findOrFail($id);
        $template->delete();
        return redirect()->back()->with('success', 'Email template deleted successfully.');
    }

    // List all generated logs
    public function generatedLogs(Request $request)
    {
        $logs = MlsOfferGeneratedLog::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('mls-offers/GeneratedLogs', [
            'logs' => $logs,
        ]);
    }
}
