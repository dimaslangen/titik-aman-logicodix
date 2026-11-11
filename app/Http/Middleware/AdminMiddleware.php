<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        $u = $request->user();
        if (!$u || $u->role !== 'admin' || $u->is_blocked) return response()->json(['success'=>false,'message'=>'Akses admin saja.'],403);
        return $next($request);
    }
}
