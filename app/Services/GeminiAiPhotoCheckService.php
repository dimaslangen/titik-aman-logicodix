<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeminiAiPhotoCheckService implements AiPhotoCheckService
{
    private const CATEGORIES = [
        'penerangan_padam',
        'area_sepi',
        'hambatan_fisik',
    ];

    private const DARKNESS_LEVELS = [
        'gelap_total',
        'gelap_sebagian',
        'terang',
        'unknown',
    ];

    public function check(?string $photoPath, array $context = []): array
    {
        $category = $this->categoryFromContext($context);
        $apiKey = (string) (config('services.gemini.api_key') ?: env('GEMINI_API_KEY', ''));

        if ($apiKey === '') {
            return $this->fallback($category);
        }

        try {
            $parts = [
                [
                    'text' => $this->prompt($category),
                ],
            ];

            if ($photoPath !== null && $photoPath !== '') {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $this->mimeType($photoPath),
                        'data' => $this->photoAsBase64($photoPath),
                    ],
                ];
            }

            $model = (string) (config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.6-flash'));
            $baseUrl = rtrim((string) (config('services.gemini.url') ?: env(
                'GEMINI_API_URL',
                'https://generativelanguage.googleapis.com/v1beta/models'
            )), '/');
            $url = sprintf('%s/%s:generateContent?key=%s', $baseUrl, rawurlencode($model), rawurlencode($apiKey));

            $response = Http::timeout(8)
                ->acceptJson()
                ->post($url, [
                    'contents' => [
                        ['parts' => $parts],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            $response->throw();
            $raw = $response->json();
            $result = $this->decodeResult($raw);

            $hasFace = (bool) ($result['has_face'] ?? false);
            $hasPlate = (bool) ($result['has_plate'] ?? false);

            return [
                'is_valid' => (bool) ($result['is_valid'] ?? false) && ! $hasFace && ! $hasPlate,
                'category' => $this->normalizeCategory($result['category'] ?? null, $category),
                'darkness' => $this->normalizeDarkness($result['darkness'] ?? null),
                'has_face' => $hasFace,
                'has_plate' => $hasPlate,
                'reason' => (string) ($result['reason'] ?? 'Hasil verifikasi Gemini tidak memberikan alasan.'),
                'raw' => $raw,
                'source' => 'gemini',
            ];
        } catch (Throwable) {
            return $this->fallback($category);
        }
    }

    private function prompt(string $category): string
    {
        return <<<PROMPT
Analisis foto laporan warga ini sebagai penyaring awal keselamatan publik. Balas hanya JSON
valid sesuai schema berikut, tanpa markdown atau teks tambahan:
{
  "is_valid": boolean,
  "category": "penerangan_padam|area_sepi|hambatan_fisik",
  "darkness": "gelap_total|gelap_sebagian|terang|unknown",
  "has_face": boolean,
  "has_plate": boolean,
  "reason": "penjelasan singkat dalam bahasa Indonesia"
}

Aturan wajib:
1. Foto harus relevan dengan lokasi luar ruang: jalan, trotoar, gang, atau fasilitas publik.
   Selfie, foto di dalam ruangan, foto tidak jelas, dan spam adalah tidak valid.
2. Klasifikasikan masalah utama sebagai penerangan_padam (lampu mati/kegelapan),
   area_sepi (area publik yang sepi), atau hambatan_fisik (jalan terhalang/rusak/genangan).
   Jika kegelapan tidak dapat dipastikan, gunakan darkness "unknown".
3. Set has_face=true jika ada wajah manusia yang terlihat jelas dan has_plate=true jika ada
   plat nomor kendaraan yang terlihat jelas. Jika salah satunya true, is_valid wajib false.
4. Kategori laporan dari aplikasi adalah "{$category}", tetapi ubah category hanya jika bukti
   foto lebih kuat mendukung kategori lain.
PROMPT;
    }

    private function photoAsBase64(string $photoPath): string
    {
        if (is_file($photoPath)) {
            $contents = file_get_contents($photoPath);
        } else {
            $contents = Storage::disk('public')->get($photoPath);
        }

        if ($contents === false || $contents === '') {
            throw new \RuntimeException('Foto laporan tidak dapat dibaca.');
        }

        return base64_encode($contents);
    }

    private function mimeType(string $photoPath): string
    {
        if (is_file($photoPath)) {
            return (string) (mime_content_type($photoPath) ?: 'image/jpeg');
        }

        return (string) (Storage::disk('public')->mimeType($photoPath) ?: 'image/jpeg');
    }

    private function decodeResult(mixed $raw): array
    {
        if (! is_array($raw)) {
            throw new \UnexpectedValueException('Respons Gemini tidak valid.');
        }

        $text = $raw['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            throw new \UnexpectedValueException('Gemini tidak mengembalikan JSON hasil analisis.');
        }

        $decoded = json_decode(trim($text), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new \UnexpectedValueException('Format hasil Gemini tidak valid.');
        }

        return $decoded;
    }

    private function categoryFromContext(array $context): string
    {
        return $this->normalizeCategory($context['category'] ?? null, 'penerangan_padam');
    }

    private function normalizeCategory(mixed $category, string $fallback): string
    {
        return is_string($category) && in_array($category, self::CATEGORIES, true)
            ? $category
            : $fallback;
    }

    private function normalizeDarkness(mixed $darkness): string
    {
        return is_string($darkness) && in_array($darkness, self::DARKNESS_LEVELS, true)
            ? $darkness
            : 'unknown';
    }

    private function fallback(string $category): array
    {
        return [
            'is_valid' => false,
            'category' => $category,
            'darkness' => 'unknown',
            'has_face' => false,
            'has_plate' => false,
            'reason' => 'Gemini API Error/Timeout: Fallback ke moderasi manual.',
            'raw' => null,
            'source' => 'gemini_error',
        ];
    }
}
