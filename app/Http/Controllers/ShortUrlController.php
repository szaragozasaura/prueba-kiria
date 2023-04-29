<?php

namespace App\Http\Controllers;

use App\Services\TinyUrlApiService;
use App\Services\TokenValidator;
use App\Services\UrlValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ShortUrlController extends Controller
{
    private TokenValidator $tokenValidator;
    private UrlValidator $urlValidator;
    private TinyUrlApiService $tinyUrlApiService;

    public function __construct(
        TokenValidator $tokenValidator,
        UrlValidator $urlValidator,
        TinyUrlApiService $tinyUrlApiService)
    {
        $this->tokenValidator = $tokenValidator;
        $this->urlValidator = $urlValidator;
        $this->tinyUrlApiService = $tinyUrlApiService;
    }

    public function create(Request $request): JsonResponse
    {
        // Validar los paréntesis del token
        $token = $request->bearerToken();
        if (!$this->tokenValidator->validateToken($token)) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Validar la URL recibida en el body
        $url = $request->input('url');
        if (!$this->urlValidator->validateUrl($url)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        // Acortar la URL utilizando la API de TinyURL
        $shortUrl = $this->tinyUrlApiService->shortenUrl($url);
        if (!$shortUrl) {
            return response()->json(['error' => 'Failed to shorten URL'], 500);
        }

        // Devolver la URL acortada
        return response()->json(['url' => $shortUrl], 200);
    }

}
