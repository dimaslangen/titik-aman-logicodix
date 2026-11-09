<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Services\AiPhotoCheckService;
use App\Services\StubAiPhotoCheckService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        // Gemini: STUB sampai Rafa pasang implementasi asli. Ganti binding ini saja.
        $this->app->bind(AiPhotoCheckService::class, StubAiPhotoCheckService::class);
    }
    public function boot(): void {
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('sos', fn (Request $request) => Limit::perMinute(3)->by($request->user()?->id ?: $request->ip()));
    }
}
