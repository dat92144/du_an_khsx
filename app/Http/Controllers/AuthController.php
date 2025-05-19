<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    // Đăng ký tài khoản
    public function register(Request $request)
    {
        // $request->validate([
        //     'username' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:6',
        //     'verify_code' => 'required'
        // ]);

        // $cachedCode = Cache::get('verify_code_' . $request->email);

        // if ($cachedCode != $request->verify_code) {
        //     return response()->json(['message' => 'Mã xác nhận không đúng'], 422);
        // }

        $user = User::create([
            'id' => (string) Str::uuid(),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Cache::forget('verify_code_' . $request->email);

        return response()->json(['message' => 'Đăng ký thành công!'], 201);
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $code = rand(100000, 999999);
        Cache::put('verify_code_' . $request->email, $code, 600); // lưu 10 phút

        Mail::raw("Mã xác nhận đăng ký của bạn là: $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Mã xác nhận đăng ký');
        });

        return response()->json(['message' => 'Mã xác thực đã được gửi tới email!']);
    }
    // Đăng nhập và tạo token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Tạo token
        $token = $user->createToken('auth_token')->plainTextToken;
        $userroles = UserRole::where('user_id', $user->id)->get();
        $roleIds = $userroles->pluck('role_id'); // Lấy tất cả role_id từ user_roles
        $roles = Role::whereIn('id', $roleIds)->pluck('name'); // Lấy danh sách name của roles

        return response()->json([
            'user' => $user,
            'role' => $roles,
            'token' => $token
        ]);
    }

    // Đăng xuất (Xóa token)
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Lấy thông tin người dùng
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}

