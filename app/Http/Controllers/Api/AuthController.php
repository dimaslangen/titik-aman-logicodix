<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
    public function register(Request $r) {
        $d = $r->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','password'=>'required|string|min:8|confirmed']);
        $u = User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>Hash::make($d['password']),'role'=>'user']);
        $token = $u->createToken('api')->plainTextToken;
        return response()->json(['success'=>true,'message'=>'Registrasi berhasil.','data'=>['user'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role],'token'=>$token]],201);
    }
    public function login(Request $r) {
        $d = $r->validate(['email'=>'required|email','password'=>'required|string']);
        $u = User::where('email',$d['email'])->first();
        if (!$u || !Hash::check($d['password'],$u->password)) throw ValidationException::withMessages(['email'=>['Email atau password salah.']]);
        if ($u->is_blocked) return response()->json(['success'=>false,'message'=>'Akun diblokir (hoax/spam). Hubungi admin.'],403);
        $token = $u->createToken('api')->plainTextToken;
        return response()->json(['success'=>true,'data'=>['user'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role],'token'=>$token]]);
    }
    public function adminLogin(Request $r) { // POST /api/v1/admin/login — sama seperti login, tapi role harus admin
        $d = $r->validate(['email'=>'required|email','password'=>'required|string']);
        $u = User::where('email',$d['email'])->where('role','admin')->first();
        if (!$u || !Hash::check($d['password'],$u->password)) throw ValidationException::withMessages(['email'=>['Email atau password admin salah.']]);
        $token = $u->createToken('admin')->plainTextToken;
        return response()->json(['success'=>true,'data'=>['user'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role],'token'=>$token]]);
    }
    public function me(Request $r){ $u=$r->user(); return response()->json(['success'=>true,'data'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role]]); }
    public function logout(Request $r){ $r->user()->currentAccessToken()->delete(); return response()->json(['success'=>true,'message'=>'Logout berhasil.']); }
}
