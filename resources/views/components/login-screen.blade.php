<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden py-10 selection:bg-blue-200">
    
    <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-pulse"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-md w-full bg-white/95 backdrop-blur-xl p-8 sm:p-10 rounded-[2rem] shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] border border-white relative z-10 mx-4">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-600 mb-5 shadow-inner border border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
            </div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">تسجيل الدخول</h2>
            <p class="text-gray-500 mt-2 font-bold text-sm">نظام إدارة المبيعات والمخزون (ERP)</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-6">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <input type="email" wire:model="email" class="w-full pl-4 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all text-left font-bold text-gray-800" dir="ltr" placeholder="admin@admin.com" autofocus>
                </div>
                @error('email') <span class="text-red-500 text-xs font-bold mt-2 block bg-red-50 p-1.5 rounded">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <input type="password" wire:model="password" class="w-full pl-4 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all text-left font-bold tracking-widest text-gray-800" dir="ltr" placeholder="••••••••">
                </div>
                @error('password') <span class="text-red-500 text-xs font-bold mt-2 block bg-red-50 p-1.5 rounded">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model="remember" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 transition-colors cursor-pointer">
                    </div>
                    <span class="ml-2 mr-3 text-sm font-bold text-gray-600 group-hover:text-blue-600 transition-colors">تذكرني في المرات القادمة</span>
                </label>
            </div>

            <button type="submit" class="relative w-full flex justify-center items-center gap-3 py-4 px-4 border border-transparent text-lg font-black rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 shadow-[0_8px_20px_rgb(79,70,229,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 overflow-hidden group mt-4">
                
                <div class="absolute inset-0 w-full h-full bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                
                <span class="drop-shadow-sm">تسجيل الدخول</span>
                
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-x-1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>

                <svg wire:loading wire:target="login" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white absolute left-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
        
        <div class="mt-8 bg-blue-50/80 border border-blue-100 p-4 rounded-xl text-sm text-gray-700 shadow-sm relative overflow-hidden">
            <div class="absolute left-0 top-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center gap-2 mb-3 border-b border-blue-200/60 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <span class="font-black text-blue-900 text-sm">بيانات حسابات التجربة</span>
            </div>
            <div class="space-y-2 font-medium">
                <div class="flex justify-between items-center bg-white/50 p-2 rounded">
                    <span class="font-bold text-gray-800">👨‍💼 المدير:</span>
                    <span class="font-mono text-blue-700 text-xs" dir="ltr">admin@pos.com (123456)</span>
                </div>
                <div class="flex justify-between items-center bg-white/50 p-2 rounded">
                    <span class="font-bold text-gray-800">🛒 الكاشير:</span>
                    <span class="font-mono text-blue-700 text-xs" dir="ltr">cashier@pos.com (123456)</span>
                </div>
            </div>
        </div>

    </div>
</div>