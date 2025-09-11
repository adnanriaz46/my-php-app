<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AirtablePodcastService
{
    public function list(int $pageSize = 20, ?string $offset = null): array
    {
        $base  = config('services.airtable.base_id');
        $table = config('services.airtable.podcast_table');
        $view  = config('services.airtable.podcast_view');

        $url = "https://api.airtable.com/v0/{$base}/" . rawurlencode($table);

        // Common query (keep attachments!)
        $common = array_filter([
            'pageSize'           => $pageSize,
            'offset'             => $offset,
            'view'               => $view ?: null,
            'sort[0][field]'     => 'Date Posted',
            'sort[0][direction]' => 'desc',
            'userLocale'         => 'en-US',
            'timeZone'           => 'America/New_York',
        ]);

        // 1) Full record fetch so attachments (thumbnails) are intact
        $full = Http::withToken(config('services.airtable.token'))
            ->get($url, $common)
            ->json();

        // 2) Names-only fetch with cellFormat=string so linked-record fields return readable text
        $names = Http::withToken(config('services.airtable.token'))
        ->get($url, $common + [
            'cellFormat' => 'string',
            // IMPORTANT: pass fields as an array so BOTH come back as strings
            'fields'     => ['Guest Name', 'Host Name'],
        ])
        ->json();
    

        // Build a map of recordId => ['guest' => '...', 'host' => '...']
        $nameMap = [];
        foreach (($names['records'] ?? []) as $rec) {
            $nf = $rec['fields'] ?? [];
            $nameMap[$rec['id']] = [
                'guest' => is_array($nf['Guest Name'] ?? null)
                    ? implode(', ', $nf['Guest Name'])
                    : ($nf['Guest Name'] ?? null),
                'host' => is_array($nf['Host Name'] ?? null)
                    ? implode(', ', $nf['Host Name'])
                    : ($nf['Host Name'] ?? null),
            ];
        }

        // Map Airtable -> UI
        $episodes = collect($full['records'] ?? [])->map(function ($r) use ($nameMap) {
            $f = $r['fields'] ?? [];

            // Robust thumbnail picker
            $thumb = data_get($f, 'Thumbnail.0.thumbnails.large.url')
                ?? data_get($f, 'Thumbnail.0.url')
                ?? data_get($f, 'YouTube Thumbnail.0.thumbnails.large.url')
                ?? data_get($f, 'YouTube Thumbnail.0.url')
                ?? data_get($f, 'Image.0.thumbnails.large.url')
                ?? data_get($f, 'Image.0.url');

            // Prefer readable names from the cellFormat=string request
            $guest = $nameMap[$r['id']]['guest']
                ?? (is_array($f['Guest Name'] ?? null) ? implode(', ', $f['Guest Name']) : ($f['Guest Name'] ?? null));

            $host = $nameMap[$r['id']]['host']
                ?? (is_array($f['Host Name'] ?? null) ? implode(', ', $f['Host Name']) : ($f['Host Name'] ?? null));

            // Best-effort YouTube/Video URL field
            $videoUrl = $f['URL to Youtube Video']      ?? $f['URL to YouTube Video']
                     ?? $f['URL to Finished Video']     ?? $f['URL to finished Video']
                     ?? $f['YouTube URL']               ?? $f['Video URL']
                     ?? $f['Watch on YouTube']          ?? null;

            return [
                'id'        => $r['id'],
                'title'     => $f['Youtube Title'] ?? $f['Title'] ?? '',
                'episode'   => $f['Episode Number'] ?? null,
                'date'      => $f['Date Posted'] ?? $f['Date Recorded'] ?? null,
                'host'      => $host,
                'guest'     => $guest,
                'summary'   => $f['Youtube Description & Timestamps'] ?? $f['Description'] ?? $f['Notes'] ?? '',
                'thumb'     => $thumb,
                'audio_url' => $f['URL to finished Audio'] ?? null,
                'video_url' => $videoUrl,
            ];
        })->all();

        return [
            'episodes'   => $episodes,
            'nextOffset' => $full['offset'] ?? null,
        ];
    }
}
