app.blade

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'نظام يَسير لإدارة المبيعات' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body, input, select, textarea, button, table {
            font-family: 'Cairo', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
        }
       
        .font-mono, [dir="ltr"] {
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 selection:bg-blue-200 selection:text-blue-900">

    @auth
    <nav x-data="{ mobileMenuOpen: false }" class="bg-slate-900 text-slate-200 shadow-xl border-b border-slate-800 sticky top-0 z-50 print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
               
                <div class="flex items-center gap-8">
                   
                    <div class="flex items-center gap-3 cursor-default group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg transform group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-2xl tracking-tight text-white leading-none">يَسير</span>
                            <span class="text-[10px] text-blue-400 font-bold tracking-widest uppercase">POS & ERP</span>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <div class="flex items-baseline gap-2">
                            <a href="{{ route('pos') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('pos') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                🛒 الكاشير
                            </a>

                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 border border-slate-700 shadow-inner' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">المركز المالي</a>
                                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('customers.index') || request()->routeIs('customers.ledger') ? 'bg-slate-800 text-blue-400 border border-slate-700 shadow-inner' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">العملاء والديون</a>
                                <a href="{{ route('returns') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('returns') ? 'bg-slate-800 text-blue-400 border border-slate-700 shadow-inner' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">المرتجعات</a>
                               
                                <div class="relative group inline-block z-50">
                                    <button class="px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all duration-200 {{ request()->routeIs(['sales.history', 'purchases.history', 'expenses', 'treasury', 'suppliers.index', 'reports.daily-profit']) ? 'bg-slate-800 text-blue-400 border border-slate-700 shadow-inner' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>العمليات المالية 💼</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                   
                                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-slate-100 overflow-hidden transform origin-top group-hover:scale-100 scale-95">
                                        <div class="py-2">
                                            <a href="{{ route('sales.history') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('sales.history') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">سجل المبيعات والفواتير 📈</a>
                                            <a href="{{ route('purchases.history') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('purchases.history') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">سجل المشتريات 📚</a>
                                            <a href="{{ route('suppliers.index') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('suppliers.index') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">إدارة الموردين 🚛</a>
                                            <div class="border-t border-slate-100 mx-3 my-1"></div>
                                            <a href="{{ route('expenses') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-red-50 text-red-600 {{ request()->routeIs('expenses') ? 'bg-red-50/80' : '' }}">المصروفات اليومية 💸</a>
                                            <div class="border-t border-slate-100 mx-3 my-1"></div>
                                            <a href="{{ route('treasury') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-emerald-50 text-emerald-700 {{ request()->routeIs('treasury') ? 'bg-emerald-50/80' : '' }}">الخزينة وحسابات البنوك 🏦</a>
                                           
                                            <div class="border-t border-slate-100 mx-3 my-1"></div>
                                            <a href="{{ route('reports.daily-profit') }}" class="flex justify-between items-center px-5 py-3 text-sm font-bold transition-colors hover:bg-indigo-50 text-indigo-700 {{ request()->routeIs('reports.daily-profit') ? 'bg-indigo-50/80' : '' }}">
                                                تقرير الأرباح اليومية 📉
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative group inline-block z-50">
                                    <button class="px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all duration-200 {{ request()->routeIs(['products.manager', 'prices.update', 'inventory', 'expiry.radar', 'inventory.movements']) ? 'bg-slate-800 text-blue-400 border border-slate-700 shadow-inner' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>المخازن 📦</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                   
                                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-slate-100 overflow-hidden transform origin-top group-hover:scale-100 scale-95">
                                        <div class="py-2">
                                            <a href="{{ route('products.manager') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('products.manager') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">تعريف المنتجات 🏷️</a>
                                            <a href="{{ route('prices.update') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('prices.update') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">تحديث الأسعار 💰</a>
                                            <a href="{{ route('inventory') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('inventory') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">الجرد والمخزون 📋</a>
                                            <a href="{{ route('inventory.movements') }}" class="block px-5 py-3 text-sm font-bold transition-colors hover:bg-slate-50 {{ request()->routeIs('inventory.movements') ? 'text-blue-600 bg-blue-50/50' : 'text-slate-700' }}">
                                                سجل حركات المخزن 🕵️‍♂️
                                            </a>
                                           
                                            <div class="border-t border-slate-100 mx-3 my-1"></div>
                                            <a href="{{ route('expiry.radar') }}" class="flex justify-between items-center px-5 py-3 text-sm font-bold transition-colors hover:bg-rose-50 text-rose-600 {{ request()->routeIs('expiry.radar') ? 'bg-rose-50/80' : '' }}">
                                                رادار الصلاحية
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                   
                    <div class="relative group hidden lg:inline-block z-50">
                        <button class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-2.5 rounded-xl flex items-center gap-3 border border-slate-700 shadow-inner transition-colors">
                            <div class="w-7 h-7 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-black shadow-sm">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-bold leading-none mb-1">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase leading-none">{{ auth()->user()->role === 'admin' ? 'مدير النظام' : 'كاشير' }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-slate-100 overflow-hidden transform origin-top group-hover:scale-100 scale-95">
                            <div class="p-4 bg-slate-50 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-500 mb-1">تسجيل الدخول كـ</p>
                                <p class="text-sm font-black text-slate-800 truncate" dir="ltr">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('settings') }}" class="flex items-center gap-3 px-5 py-3 text-sm font-bold transition-colors hover:bg-amber-50 text-slate-700 hover:text-amber-700">
                                    <span class="text-lg">🔐</span>
                                    <span>إعدادات المتجر</span>
                                </a>
                                <div class="border-t border-slate-100 my-1 mx-3"></div>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-5 py-3 text-sm font-bold transition-colors hover:bg-rose-50 text-rose-600 text-right">
                                        <span class="text-lg">🚪</span>
                                        <span>تسجيل الخروج</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden bg-slate-800 p-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition.opacity class="lg:hidden bg-slate-800 border-t border-slate-700 absolute w-full left-0 shadow-2xl">
            <div class="px-4 pt-2 pb-6 space-y-1">
               
                <div class="p-4 bg-slate-900 rounded-xl mb-4 border border-slate-700 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center font-black">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400 font-mono" dir="ltr">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <a href="{{ route('pos') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('pos') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    🛒 الكاشير السريع
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📊 المركز المالي (الداشبورد)
                    </a>
                   
                    <a href="{{ route('reports.daily-profit') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('reports.daily-profit') ? 'bg-indigo-700 text-indigo-200' : 'text-slate-300 hover:bg-slate-700 hover:text-indigo-300' }}">
                        📉 تقرير الأرباح اليومية
                    </a>
                   
                    <a href="{{ route('customers.index') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('customers.index') ? 'bg-slate-700 text-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        👥 سجل العملاء والديون
                    </a>

                    <a href="{{ route('sales.history') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('sales.history') ? 'bg-slate-700 text-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📈 سجل المبيعات والفواتير
                    </a>

                    <a href="{{ route('purchases.history') }}" class="block px-4 py-3 rounded-xl text-base font-bold {{ request()->routeIs('purchases.history') ? 'bg-slate-700 text-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📚 سجل المشتريات (الموردين)
                    </a>
                   
                    <a href="{{ route('expenses') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-rose-400 hover:bg-slate-700 hover:text-rose-300">
                        💸 إدارة المصروفات
                    </a>

                    <a href="{{ route('products.manager') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-white">
                        📦 المخزن وتعريف المنتجات
                    </a>
                   
                    <a href="{{ route('settings') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-amber-400 hover:bg-slate-700 hover:text-amber-300 border-t border-slate-700 mt-2 pt-4">
                        ⚙️ إعدادات النظام
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-right block px-4 py-3 rounded-xl text-base font-bold bg-rose-600/20 text-rose-500 hover:bg-rose-600 hover:text-white transition-colors">
                        🚪 تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="pb-10">
        {{ $slot }}
    </main>

</body>
</html>
