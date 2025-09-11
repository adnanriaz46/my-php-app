<?php

namespace App\Helper;

use App\Models\UserPdfFillerConfig;
use Illuminate\Support\Facades\Http;

class MlsOfferHelper
{
    private static $parentId = 2785579;
    public static function getTags()
    {
        return [
            'Property Full Street Address',
            'Property City Name',
            'Property State Name',
            'Property Zip',
            'Property County',
            'Property Tax ID',
            'Property Markert Price',
            'Property Entry Date',
            'Property Modified Date',
            'Property AVM',
            'Property ARV',
            'Property MLS Number',
            'Property Listing Agent Name',
            'Property Listing Agent First Name',
            'Property Listing Agent Last Name',
            'Property Listing Agent Email',
            'Property Listing Agent Office',
            'Property Zoning',
            'Property Revamp ID',
            'Property Closed Date',
            'Property Municipality',
            'Property Link',
            'Property School District Name',
        ];
    }

    public static function createFolder($folderName, $userId)
    {
        $userPdfFillerConfig = UserPdfFillerConfig::where('user_id', $userId)->first();
        if ($userPdfFillerConfig) {
            $folderId = $userPdfFillerConfig->folder_id;
            $folderName = $userPdfFillerConfig->folder_name;
        } else {
            $url = "https://api.pdffiller.com/v2/folders";
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.pdffiller.api_key'),
            ])->post($url, [
                        'name' => $folderName,
                        'parent_id' => self::$parentId,
                        'with_tags' => false,
                    ]);

            $response = $response->json();
            if ($response['folder_id']) {
                $userPdfFillerConfig = UserPdfFillerConfig::create([
                    'user_id' => $userId,
                    'folder_id' => $response['folder_id'],
                    'folder_name' => $folderName,
                    'folder_parent_id' => self::$parentId,
                    'folder_url' => $response['url'] ?? '',
                    'folder_status' => $response['status'] ?? '',
                ]);

                $folderId = $userPdfFillerConfig->folder_id;
                $folderName = $userPdfFillerConfig->folder_name;
            } else {
                $folderId = null;
                $folderName = null;
            }
        }

        return ['folder_id' => $folderId, 'folder_name' => $folderName];
    }


    public static function downloadTemplate($templateId)
    {
        $url = "https://api.pdffiller.com/v2/templates/{$templateId}/download";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.pdffiller.api_key'),
        ])->get($url);

        if ($response->successful()) {
            $filename = "pdffiller-template-{$templateId}.pdf";
            $path = storage_path("app/pdffiller/{$filename}");

            // Ensure directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $response->body());

            return $path; // Full path to file
        }

        return null;
    }

    public static function updateTemplateName($templateId, $templateName, $folderId)
    {
        $url = "https://api.pdffiller.com/v2/templates/" . $templateId;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.pdffiller.api_key'),
        ])->post($url, [
                    'name' => $templateName,
                    'folder_id' => $folderId,
                ]);

        return $response->json();
    }

    public static function addTemplate($file, $folderId, $templateName): int|string|null
    {
        $url = "https://api.pdffiller.com/v2/templates";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.pdffiller.api_key'),
        ])->post($url, [
                    'file' => $file,
                    'folder_id' => self::$parentId, // should be parent folder id
                    'with_tags' => false,
                ]);

        $response = $response->json();

        if ($response['id']) {
            $templateId = $response['id'];
            self::updateTemplateName($templateId, $templateName, $folderId);
            return $templateId;
        } else {
            return null;
        }
    }
}