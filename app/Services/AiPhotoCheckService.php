<?php
namespace App\Services;
/**
 * KONTRAK GEMINI UNTUK RAFA (AI Engineer) - lihat API.md bagian "Kontrak Gemini".
 * Service ini sengaja STUB: tidak memanggil Gemini sungguhan.
 * Rafa tinggal mengganti isi method check() dengan panggilan Gemini 2.5 Flash
 * Multimodal (structured JSON), tanpa mengubah controller / response shape.
 * Kontrak output: [is_valid, category, darkness, has_face, has_plate, reason, raw, source]
 * PRD FR-02.3/FR-02.7: foto berwajah/plat jelas => is_valid=false.
 * Kalau Gemini mati/timeout atau key kosong => laporan jadi PENDING_MODERATION.
 */
interface AiPhotoCheckService {
    public function check(?string $photoPath, array $context = []): array;
}
class StubAiPhotoCheckService implements AiPhotoCheckService {
    public function check(?string $photoPath, array $context = []): array {
        $key = (string) env('GEMINI_API_KEY', '');
        if ($key === '') {
            return [
                'is_valid' => true, 'category' => $context['category'] ?? null, 'darkness' => 'unknown',
                'has_face' => false, 'has_plate' => false,
                'reason' => 'STUB: GEMINI_API_KEY belum diisi. Foto belum dicek Gemini sungguhan; laporan diteruskan sebagai PENDING_MODERATION untuk ditinjau admin (FR-02.7). Rafa: isi key + implementasi Gemini 2.5 Flash di App\Services\GeminiAiPhotoCheckService.',
                'raw' => null, 'source' => 'stub',
            ];
        }
        return [
            'is_valid' => true, 'category' => $context['category'] ?? null, 'darkness' => 'unknown',
            'has_face' => false, 'has_plate' => false,
            'reason' => 'STUB: key terisi tapi implementasi Gemini belum dipasang. Ganti StubAiPhotoCheckService dengan GeminiAiPhotoCheckService (lihat API.md).',
            'raw' => null, 'source' => 'stub',
        ];
    }
}
