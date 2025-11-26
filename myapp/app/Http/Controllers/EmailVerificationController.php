<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\EmailVerificationCode;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Send verification code to email
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
        ]);

        $email = $request->email;

        // Xóa code cũ nếu có
        EmailVerificationCode::where('email', $email)->delete();

        // Tạo code 6 chữ số
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu code vào database (hết hạn sau 15 phút)
        EmailVerificationCode::create([
            'email' => $email,
            'code' => $code,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Gửi email
        try {
            Mail::raw("Mã xác thực của bạn là: $code\n\nMã này sẽ hết hạn sau 15 phút.", function ($message) use ($email) {
                $message->to($email)->subject('Mã Xác Thực Đăng Ký');
            });

            return response()->json([
                'success' => true,
                'message' => 'Mã xác thực đã được gửi đến email của bạn',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi email. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Verify code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $code = $request->code;

        // Tìm record
        $verification = EmailVerificationCode::where('email', $email)->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Email chưa được yêu cầu xác thực',
            ], 404);
        }

        // Kiểm tra hết hạn
        if (Carbon::now()->isAfter($verification->expires_at)) {
            $verification->delete();
            return response()->json([
                'success' => false,
                'message' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu lại.',
            ], 400);
        }

        // Kiểm tra số lần nhập sai (tối đa 5 lần)
        if ($verification->attempts >= 5) {
            $verification->delete();
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng yêu cầu mã mới.',
            ], 400);
        }

        // Kiểm tra code
        if ($verification->code !== $code) {
            $verification->increment('attempts');
            return response()->json([
                'success' => false,
                'message' => 'Mã xác thực không đúng. Vui lòng thử lại.',
            ], 400);
        }

        // Xóa code sau khi xác thực thành công
        $verification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xác thực email thành công!',
        ]);
    }

    /**
     * Complete registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        // Kiểm tra email đã verified
        $verification = EmailVerificationCode::where('email', $request->email)->first();
        if ($verification) {
            return back()->withErrors(['email' => 'Email chưa được xác thực.'])->withInput();
        }

        // Tạo user
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'customer',
        ]);

        // Tạo customer
        \App\Models\Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address ?? null,
            'user_id' => $user->id,
        ]);

        // Đăng nhập
        session(['user_id' => $user->id, 'user_name' => $user->name]);
        return redirect('/')->with('success', 'Đăng ký thành công!');
    }
}
