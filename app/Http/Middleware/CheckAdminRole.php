<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التحقق مما إذا كان المستخدم مسجلاً للدخول
        if (!Auth::check()) {
            return redirect('/login'); // تحويله لصفحة تسجيل الدخول إذا لم يكن مسجلاً
        }

        // 2. التحقق مما إذا كانت صلاحيته "كاشير"
        if (Auth::user()->role !== 'admin') {
            // إذا كان كاشير، نمنعه من الدخول ونعيده لشاشة البيع بقوة
            return redirect('/pos')->with('error', 'عفواً، ليس لديك صلاحية للدخول إلى هذه الشاشة!');
        }

        // 3. إذا كان مديراً، نسمح له بالمرور
        return $next($request);
    }
}