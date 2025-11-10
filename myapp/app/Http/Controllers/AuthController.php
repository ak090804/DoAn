<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản.'])->withInput();
        }

        $passwordMatches = false;
        if (Hash::check($data['password'], $user->password)) {
            $passwordMatches = true;
        } else {
            // If stored passwords are plain text (legacy), allow direct compare as fallback
            if ($data['password'] === $user->password) {
                $passwordMatches = true;
            }
        }

        if ($passwordMatches) {
            session(['user_id' => $user->id, 'user_name' => $user->name]);
            return redirect('/')->with('success', 'Đăng nhập thành công.');
        }

        return back()->withErrors(['password' => 'Sai email hoặc mật khẩu'])->withInput();
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        // Nếu email đã tồn tại -> trả lỗi
        $existing = User::where('email', $data['email'])->first();
        if ($existing) {
            return back()->withErrors(['email' => 'Email đã tồn tại. Vui lòng dùng email khác.'])->withInput();
        }

        // Tạo user (lưu email + mật khẩu)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        // Tạo customer (lưu thông tin còn lại) và liên kết với user
        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'user_id' => $user->id,
        ]);

        // Đăng nhập bằng session đơn giản
        session(['user_id' => $user->id, 'user_name' => $user->name]);
        return redirect('/')->with('success', 'Đăng ký thành công. Đã đăng nhập.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['user_id', 'user_name']);
        return redirect('/')->with('success', 'Đã đăng xuất.');
    }

    // Show account page (if logged in)
    public function account(Request $request)
    {
        $userId = $request->session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        $customer = null;
        if ($user) {
            $customer = $user->customer()->first();
        }

        return view('auth.account', ['user' => $user, 'customer' => $customer]);
    }
}
