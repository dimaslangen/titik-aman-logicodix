<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,RegionController,SafetyHubController,ReportController,RouteController,SosController,SharedRouteController,SimulationController,AdminController};

Route::prefix('v1')->group(function () {
    Route::get('/login', fn()=>response()->json(['success'=>false,'message'=>'Login dulu (token Sanctum wajib).'],401))->name('login');
    Route::get('/health', fn()=>response()->json(['success'=>true,'app'=>'TitikAman','laravel'=>'13','status'=>'ok','time'=>now()->toISOString()]));
    // Publik (tanpa login) — FR-01/FR-02 lihat data, FR-03 bandingkan rute
    Route::get('/regions', [RegionController::class,'index']);
    Route::get('/regions/{slug}', [RegionController::class,'show']);
    Route::get('/safety-hubs', [SafetyHubController::class,'index']);
    Route::get('/reports', [ReportController::class,'index']);
    Route::get('/reports/{id}', [ReportController::class,'show']);
    Route::post('/routes/compare', [RouteController::class,'compare']);
    Route::get('/simulation', [SimulationController::class,'status']);
    Route::get('/shared-routes/{token}', [SharedRouteController::class,'show']);
    // Auth
    Route::post('/register', [AuthController::class,'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class,'login'])->middleware('throttle:auth');
    Route::post('/admin/login', [AuthController::class,'adminLogin'])->middleware('throttle:auth');
    Route::post('/sos', [SosController::class,'store'])->middleware('throttle:sos'); // SOS boleh tanpa login (darurat), user_id nullable
    // Login wajib
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/me', [AuthController::class,'me']);
        Route::post('/logout', [AuthController::class,'logout']);
        Route::post('/reports', [ReportController::class,'store'])->middleware('throttle:reports');
        Route::post('/reports/{id}/upvote', [ReportController::class,'upvote']);
        Route::post('/reports/{id}/ai-check', [ReportController::class,'aiCheck']);
        Route::post('/shared-routes', [SharedRouteController::class,'store']);
        // Admin
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->prefix('admin')->group(function(){
            Route::get('/dashboard', [AdminController::class,'dashboard']);
            Route::post('/simulation', [SimulationController::class,'toggle']);
            Route::post('/reports/{id}/verify', [AdminController::class,'verify']);
            Route::post('/reports/{id}/resolve', [AdminController::class,'resolve']);
            Route::post('/reports/{id}/hide', [AdminController::class,'hide']);
            Route::post('/reports/{id}/wa-followup', [AdminController::class,'waFollowup']);
            Route::get('/sos', [AdminController::class,'sosList']);
            Route::post('/sos/{id}/resolve', [AdminController::class,'sosResolve']);
            Route::post('/users/{id}/block', [AdminController::class,'blockUser']);
            Route::post('/users/{id}/unblock', [AdminController::class,'unblockUser']);
        });
    });
});
