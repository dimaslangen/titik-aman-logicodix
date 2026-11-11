<?php

namespace Tests\Unit;

use App\Services\GeminiAiPhotoCheckService;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeminiAiPhotoCheckServiceTest extends TestCase
{
    public function test_it_returns_the_required_fallback_when_the_api_key_is_missing(): void
    {
        Config::set('services.gemini.api_key', null);

        $result = (new GeminiAiPhotoCheckService)->check(null, ['category' => 'area_sepi']);

        $this->assertSame([
            'is_valid' => false,
            'category' => 'area_sepi',
            'darkness' => 'unknown',
            'has_face' => false,
            'has_plate' => false,
            'reason' => 'Gemini API Error/Timeout: Fallback ke moderasi manual.',
            'raw' => null,
            'source' => 'gemini_error',
        ], $result);
    }

    public function test_it_maps_gemini_structured_json_to_the_service_contract(): void
    {
        Config::set('services.gemini.api_key', 'test-key');
        Config::set('services.gemini.model', 'gemini-test');
        Storage::fake('public');
        Storage::disk('public')->put('reports/photo.jpg', 'image-bytes');
        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'is_valid' => true,
                                'category' => 'hambatan_fisik',
                                'darkness' => 'terang',
                                'has_face' => false,
                                'has_plate' => false,
                                'reason' => 'Trotoar terhalang.',
                            ]),
                        ]],
                    ],
                ]],
            ]),
        ]);

        $result = (new GeminiAiPhotoCheckService)->check('reports/photo.jpg', [
            'category' => 'penerangan_padam',
        ]);

        $this->assertTrue($result['is_valid']);
        $this->assertSame('hambatan_fisik', $result['category']);
        $this->assertSame('gemini', $result['source']);
        $this->assertSame('Trotoar terhalang.', $result['reason']);
        Http::assertSent(function (ClientRequest $request): bool {
            return str_contains($request->url(), '/gemini-test:generateContent?key=test-key')
                && $request->data()['generationConfig']['responseMimeType'] === 'application/json'
                && isset($request->data()['contents'][0]['parts'][1]['inline_data']['data']);
        });
    }

    public function test_it_falls_back_when_gemini_request_fails(): void
    {
        Config::set('services.gemini.api_key', 'test-key');
        Http::fake([
            '*' => Http::response(['error' => ['message' => 'Unavailable']], 503),
        ]);

        $result = (new GeminiAiPhotoCheckService)->check(null);

        $this->assertSame('gemini_error', $result['source']);
        $this->assertFalse($result['is_valid']);
        $this->assertSame('unknown', $result['darkness']);
    }
}
