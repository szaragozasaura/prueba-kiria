<?php

namespace Tests\Feature;

use App\Http\Controllers\ShortUrlController;
use App\Services\TokenValidator;
use App\Services\UrlValidator;
use App\Services\TinyUrlApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShortUrlControllerTest extends TestCase
{
    public function testShortenUrl()
    {
        // Configurar las dependencias del controlador
        $tokenValidator = new TokenValidator();
        $urlValidator = new UrlValidator();
        $tinyUrlApiService = $this->createMock(TinyUrlApiService::class);
        $controller = new ShortUrlController($tokenValidator, $urlValidator, $tinyUrlApiService);

        // Escenario 1: El token es inválido
        $request = Request::create('/api/shorten', 'POST', ['url' => 'http://example.com']);
        $request->headers->set('Authorization', 'Bearer invalid(token)');
        $response = $controller->create($request);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Invalid token', json_decode($response->getContent())->error);

        // Escenario 2: La URL es inválida
        $request = Request::create('/api/shorten', 'POST', ['url' => 'invalid-url']);
        $request->headers->set('Authorization', 'Bearer (valid)');
        $response = $controller->create($request);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Invalid URL', json_decode($response->getContent())->error);

        // Escenario 3: La URL es válida y se acorta correctamente
        $url = 'http://example.com';
        $tinyUrl = 'http://tinyurl.com/abc123';
        $tinyUrlApiService->method('shortenUrl')->with($url)->willReturn($tinyUrl);
        Http::fake([
            'https://tinyurl.com/api-create.php?url='.$url => Http::response($tinyUrl, 200),
        ]);
        $request = Request::create('/api/shorten', 'POST', ['url' => $url]);
        $request->headers->set('Authorization', 'Bearer (valid)');
        $response = $controller->create($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($tinyUrl, json_decode($response->getContent())->url);
    }
}
