<div class="p-6 sm:p-8 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">👋</span> أهلاً بك في يَسير
            </h1>
            <p class="text-slate-500 mt-2 font-bold">المركز المالي وملخص النشاط التجاري ليوم <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">{{ date('Y-m-d') }}</span></p>
        </div>
        <div class="bg-white px-5 py-2.5 rounded-xl shadow-sm border border-slate-200 font-bold text-slate-600 text-sm flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            النظام متصل ويعمل بكفاءة
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        
        <div class="relative bg-slate-900 p-6 rounded-3xl shadow-xl text-white flex flex-col justify-between overflow-hidden group">
            <div class="absolute top-[-20px] left-[-20px] w-32 h-32 bg-emerald-500 rounded-full mix-blend-screen filter blur-[40px] opacity-40 group-hover:opacity-60 transition-opacity"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-bold text-slate-300 mb-2">الربح الصافي (تقديري)</p>
                    <h3 class="text-4xl font-black tracking-tight" dir="ltr"><span class="text-emerald-400 text-2xl pr-1 font-sans">SDG</span>{{ number_format($netProfit, 0) }}</h3>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl text-2xl border border-white/10 shadow-inner">📈</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex flex-col justify-between group hover:border-blue-100 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-bold text-slate-500 mb-2">إجمالي المبيعات</p>
                    <h3 class="text-3xl font-black text-blue-700 tracking-tight" dir="ltr">{{ number_format($todaySales, 0) }}</h3>
                </div>
                <div class="bg-blue-50 text-blue-600 p-3 rounded-2xl text-xl transition-transform group-hover:scale-110">🛒</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex flex-col justify-between group hover:border-rose-100 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-bold text-slate-500 mb-2">المصروفات اليومية</p>
                    <h3 class="text-3xl font-black text-rose-600 tracking-tight" dir="ltr">{{ number_format($todayExpenses, 0) }}</h3>
                </div>
                <div class="bg-rose-50 text-rose-500 p-3 rounded-2xl text-xl transition-transform group-hover:scale-110">🔻</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex flex-col justify-center gap-3">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">السيولة النقدية اليوم</p>
            
            <div class="flex justify-between items-center bg-emerald-50/50 p-3 rounded-xl border border-emerald-100/50">
                <span class="text-xs font-bold text-slate-600 flex items-center gap-2"><span class="text-emerald-500">💵</span> الدرج:</span>
                <span class="text-lg font-black text-emerald-700" dir="ltr">{{ number_format($actualCashInDrawer, 0) }}</span>
            </div>
            
            <div class="flex justify-between items-center bg-indigo-50/50 p-3 rounded-xl border border-indigo-100/50">
                <span class="text-xs font-bold text-slate-600 flex items-center gap-2"><span class="text-indigo-500">📱</span> بنكك:</span>
                <span class="text-lg font-black text-indigo-700" dir="ltr">{{ number_format($totalBankakReceived, 0) }}</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        
        <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-5 hover:-translate-y-1 transition-transform">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shadow-inner">📦</div>
            <div>
                <p class="text-sm text-slate-500 font-bold">مشتريات البضاعة اليوم</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($todayPurchases, 0) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-5 hover:-translate-y-1 transition-transform">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-inner">🧾</div>
            <div>
                <p class="text-sm text-slate-500 font-bold">ديون العملاء (أصول)</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($totalDebts, 0) }}</p>
            </div>
        </div>

        <a href="{{ route('expiry.radar') }}" class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-5 transition-all group {{ $expiringCount > 0 ? 'hover:bg-rose-50 hover:border-rose-200 cursor-pointer' : '' }}">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-inner transition-colors {{ $expiringCount > 0 ? 'bg-rose-100 text-rose-600 group-hover:bg-rose-200 animate-pulse' : 'bg-emerald-50 text-emerald-500' }}">
                📡
            </div>
            <div>
                <p class="text-sm text-slate-500 font-bold">حالة رادار الصلاحية</p>
                <p class="text-2xl font-black {{ $expiringCount > 0 ? 'text-rose-600' : 'text-emerald-500' }}">
                    {{ $expiringCount > 0 ? $expiringCount . ' تنبيهات!' : 'الوضع آمن' }}
                </p>
            </div>
        </a>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-blue-500 rounded-full"></span> آخر الفواتير المُصدرة
                </h2>
                <span class="text-xs font-bold bg-blue-50 border border-blue-100 text-blue-600 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span> مباشر
                </span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-sm">
                            <th class="p-4 font-bold">الفاتورة</th>
                            <th class="p-4 font-bold">العميل</th>
                            <th class="p-4 font-bold">الإجمالي</th>
                            <th class="p-4 font-bold">الحالة</th>
                            <th class="p-4 font-bold">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 font-black text-blue-600">
                                <a href="/receipt/{{ $sale->id }}" target="_blank" class="hover:underline flex items-center gap-1">
                                    {{ $sale->receipt_number }}
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px]">👤</div>
                                    {{ $sale->customer ? $sale->customer->name : 'زبون نقدي عام' }}
                                </div>
                            </td>
                            <td class="p-4 font-black text-slate-800 text-lg">{{ number_format($sale->total_amount, 0) }}</td>
                            <td class="p-4">
                                @if($sale->payment_status == 'paid') 
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-bold">خالص</span>
                                @elseif($sale->payment_status == 'partial') 
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-bold">جزئي</span>
                                @else 
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full text-xs font-bold">آجل</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-slate-400 font-bold whitespace-nowrap">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="text-4xl mb-3 opacity-20">🧾</div>
                                <p class="text-slate-500 font-bold text-lg">لم يتم إصدار أي فواتير اليوم بعد.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-white flex justify-between items-center">
                <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-rose-500 rounded-full"></span> نواقص الرفوف
                </h2>
            </div>
            <div class="p-2 flex-1 overflow-y-auto max-h-[400px]">
                <ul class="divide-y divide-slate-50">
                    @forelse($lowStockProducts as $product)
                        <li class="p-4 flex justify-between items-center hover:bg-slate-50 rounded-2xl transition-colors mb-1">
                            <div>
                                <p class="font-black text-slate-800 text-sm leading-tight">{{ $product->name }}</p>
                                <p class="text-[11px] text-slate-500 font-bold mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded-md">الوحدة: {{ $product->baseUnit ? $product->baseUnit->name : '' }}</p>
                            </div>
                            <div class="bg-rose-50 text-rose-700 border border-rose-100 font-black px-4 py-1.5 rounded-xl text-sm shadow-sm flex items-center gap-1">
                                <span>{{ (float)$product->current_stock }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="p-12 flex flex-col items-center justify-center h-full">
                            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-3xl mb-3">✨</div>
                            <p class="text-emerald-600 font-black text-lg">المخزون ممتاز!</p>
                            <p class="text-slate-400 text-xs font-bold mt-1">لا توجد نواقص على الرفوف حالياً.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
            @if(count($lowStockProducts) > 0)
            <div class="p-4 bg-slate-50 text-center border-t border-slate-100">
                <a href="{{ route('inventory') }}" class="text-sm font-black text-blue-600 hover:text-blue-800 transition-colors flex justify-center items-center gap-1">
                    <span>إدارة المخزون بالكامل</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
            </div>
            @endif
        </div>

    </div>

</div>