<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'نظام إدارة المبيعات' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    @auth
    <nav class="bg-gray-800 text-white shadow-lg print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center gap-6">
                    <span class="font-black text-xl text-blue-400">سوبر ماركت الإخوة</span>

                    <div class="hidden md:block">
                        <div class="flex items-baseline gap-2">
                            <a href="{{ route('pos') }}" class="px-3 py-2 rounded-md text-sm font-bold transition-colors {{ request()->routeIs('pos') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                الكاشير
                            </a>

                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-bold transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">اللوحة</a>
                                <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-md text-sm font-bold transition-colors {{ request()->routeIs('customers.index') || request()->routeIs('customers.ledger') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">العملاء والديون</a>
                                <a href="{{ route('returns') }}" class="px-3 py-2 rounded-md text-sm font-bold transition-colors {{ request()->routeIs('returns') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">المرتجعات</a>
                                
                                <div class="relative group inline-block z-50">
                                    <button class="px-3 py-2 rounded-md text-sm font-bold text-gray-300 hover:bg-gray-700 hover:text-white flex items-center gap-1 transition-colors {{ request()->routeIs(['purchases.history', 'expenses']) ? 'bg-blue-600 text-white' : '' }}">
                                        <span>المشتريات والمصروفات 💼</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div class="absolute right-0 mt-0 w-48 bg-white rounded-md shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-hover:mt-2 transition-all duration-300 border border-gray-100 overflow-hidden">
                                        <div class="py-1">
                                            <a href="{{ route('purchases.history') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('purchases.history') ? 'bg-blue-50 text-blue-600' : '' }}">
                                                سجل المشتريات 📚
                                            </a>
                                            <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('suppliers.index') ? 'bg-blue-50 text-blue-600' : '' }}">
                                                إدارة الموردين 
                                            </a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a href="{{ route('expenses') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('expenses') ? 'bg-red-50 text-red-700' : '' }}">
                                                المصروفات اليومية 💸
                                            </a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a href="{{ route('treasury') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('expenses') ? 'bg-red-50 text-red-700' : '' }}">
                                                الخزينة المركزية وحسابات البنوك 💸
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative group inline-block z-50">
                                    <button class="px-3 py-2 rounded-md text-sm font-bold text-gray-300 hover:bg-gray-700 hover:text-white flex items-center gap-1 transition-colors {{ request()->routeIs(['products.manager', 'prices.update', 'inventory', 'expiry.radar']) ? 'bg-blue-600 text-white' : '' }}">
                                        <span>إدارة المخزون 📦</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div class="absolute right-0 mt-0 w-48 bg-white rounded-md shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-hover:mt-2 transition-all duration-300 border border-gray-100 overflow-hidden">
                                        <div class="py-1">
                                            <a href="{{ route('products.manager') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('products.manager') ? 'bg-blue-50 text-blue-600' : '' }}">تعريف المنتجات</a>
                                            <a href="{{ route('prices.update') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('prices.update') ? 'bg-blue-50 text-blue-600' : '' }}">تحديث الأسعار</a>
                                            <a href="{{ route('inventory') }}" class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('inventory') ? 'bg-blue-50 text-blue-600' : '' }}">الجرد والمخزون</a>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a href="{{ route('expiry.radar') }}" class="block px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 flex justify-between items-center {{ request()->routeIs('expiry.radar') ? 'bg-red-50' : '' }}">
                                                رادار الصلاحية 
                                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full animate-pulse">جديد</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-sm font-bold bg-gray-700 px-3 py-1.5 rounded-full flex items-center gap-2">
                        <span>👤</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-md text-sm font-bold transition-colors shadow flex items-center gap-1">
                            <span>خروج</span>
                            <span>🚪</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </nav>
    @endauth

    <main>
        {{ $slot }}
    </main>

</body>
</html>