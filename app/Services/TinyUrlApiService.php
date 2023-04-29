<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TinyUrlApiService
{
    public function shortenUrl(string $url): ?string
    {
        $response = Http::get('https://tinyurl.com/api-create.php', [
            'url' => $url
        ]);

        return $response->ok() ? $response->body() : null;
    }
}
