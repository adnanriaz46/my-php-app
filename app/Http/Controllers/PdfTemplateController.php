<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdfTemplate;
use Illuminate\Support\Facades\Storage;

class PdfTemplateController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);
        $file = $request->file('pdf');
        $path = 'pdf-templates/';
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . $filename;

        // Upload with public visibility
        Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

        // Get the public URL
        $url = Storage::disk('s3')->url($fullPath);

        $template = PdfTemplate::create([
            'user_id' => $request->user()?->id,
            'pdf_path' => $fullPath,
        ]);
        // Optionally, return the URL as well
        return response()->json(['template' => $template, 'url' => $url], 201);
    }

    public function saveFields(Request $request, $id)
    {
        $template = PdfTemplate::where('id', $id)
            ->when($request->user(), function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->firstOrFail();
        $template->fields_json = $request->input('fields_json');
        $template->save();
        return response()->json($template);
    }

    public function index(Request $request)
    {
        $templates = PdfTemplate::when($request->user(), function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->get();
        return response()->json($templates);
    }
} 