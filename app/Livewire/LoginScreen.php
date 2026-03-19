<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginScreen extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function login()
    {
        // 1. التحقق من المدخلات
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        // 2. محاولة تسجيل الدخول
        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            // 3. التوجيه الذكي حسب الصلاحية
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/'); // المدير يذهب للوحة التحكم
            } else {
                return redirect()->intended('/pos'); // الكاشير يذهب لشاشة البيع
            }
        }

        // في حال كانت البيانات خاطئة
        $this->addError('email', 'بيانات الدخول غير صحيحة، تأكد من الإيميل وكلمة المرور.');
    }

    public function render()
    {
        return view('components.login-screen')->layout('layouts.guest');
    }
}