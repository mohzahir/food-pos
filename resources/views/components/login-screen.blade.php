<div class="min-h-screen flex items-center justify-center bg-gray-200">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-2xl border-t-8 border-blue-600">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-gray-800">تسجيل الدخول</h2>
            <p class="text-gray-500 mt-2 font-bold">نظام إدارة المبيعات والمخزون</p>
        </div>

        <form wire:submit.prevent="login">
            <div class="mb-5">
                <label class="block text-gray-700 font-bold mb-2">البريد الإلكتروني</label>
                <input type="email" wire:model="email" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left" dir="ltr" placeholder="admin@pos.com" autofocus>
                @error('email') <span class="text-red-500 text-sm font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">كلمة المرور</label>
                <input type="password" wire:model="password" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left" dir="ltr" placeholder="••••••••">
                @error('password') <span class="text-red-500 text-sm font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" wire:model="remember" id="remember" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 ml-2">
                <label for="remember" class="text-gray-700 font-bold">تذكرني في المرات القادمة</label>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition-all text-xl flex justify-center items-center gap-2">
                <span>دخول للنظام</span>
            </button>
        </form>
        
        <div class="mt-8 bg-blue-50 p-4 rounded-lg text-sm text-gray-700 border border-blue-200">
            <p class="font-bold text-blue-800 mb-2 border-b border-blue-200 pb-1">بيانات حسابات التجربة:</p>
            <p class="mb-1"><strong>المدير:</strong> admin@pos.com (الرقم السري: 123456)</p>
            <p><strong>الكاشير:</strong> cashier@pos.com (الرقم السري: 123456)</p>
        </div>

    </div>
</div>