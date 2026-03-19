<div class="p-6 max-w-7xl mx-auto min-h-screen bg-gray-50">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 flex items-center gap-2">
                <span>📊</span> لوحة القيادة (المركز المالي)
            </h1>
            <p class="text-gray-500 mt-1 font-bold">ملخص النشاط التجاري ليوم {{ date('Y-m-d') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-gradient-to-l from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white flex items-center justify-between transform transition-transform hover:scale-105">
            <div>
                <p class="text-sm font-bold mb-1 opacity-90">الربح الصافي (تقديري اليوم)</p>
                <h3 class="text-3xl font-black" dir="ltr">{{ number_format($netProfit, 2) }}</h3>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full text-3xl">💰</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-bold mb-1">إجمالي المبيعات</p>
                <h3 class="text-2xl font-black text-blue-700">{{ number_format($todaySales, 2) }}</h3>
            </div>
            <div class="bg-blue-50 p-3 rounded-full text-blue-500 text-2xl">🛒</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-red-500 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-bold mb-1">المصروفات اليومية</p>
                <h3 class="text-2xl font-black text-red-600">{{ number_format($todayExpenses, 2) }}</h3>
            </div>
            <div class="bg-red-50 p-3 rounded-full text-red-500 text-2xl">🔻</div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border-r-4 border-yellow-500 flex flex-col justify-between">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2 mb-2">
                <p class="text-sm text-gray-500 font-bold">المقبوضات والسيولة</p>
                <span class="text-lg">🏦</span>
            </div>
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold text-gray-600 flex items-center gap-1">💵 كاش الخزنة (الدرج):</span>
                <span class="text-lg font-black text-green-600" dir="ltr" title="بعد خصم المصروفات">{{ number_format($actualCashInDrawer, 0) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-600 flex items-center gap-1">📱 إشعارات بنكك:</span>
                <span class="text-lg font-black text-blue-600" dir="ltr">{{ number_format($totalBankakReceived, 0) }}</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-purple-100 p-3 rounded-lg text-purple-600 text-xl">📦</div>
            <div>
                <p class="text-sm text-gray-500 font-bold">بضاعة مشتراة اليوم</p>
                <p class="text-xl font-black text-gray-800">{{ number_format($todayPurchases, 2) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600 text-xl">⚠️</div>
            <div>
                <p class="text-sm text-gray-500 font-bold">إجمالي ديون العملاء بالسوق</p>
                <p class="text-xl font-black text-gray-800">{{ number_format($totalDebts, 2) }}</p>
            </div>
        </div>

        <a href="{{ route('expiry.radar') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:bg-red-50 transition-colors group cursor-pointer">
            <div class="{{ $expiringCount > 0 ? 'bg-red-100 text-red-600 animate-pulse' : 'bg-green-100 text-green-600' }} p-3 rounded-lg text-xl">
                📡
            </div>
            <div>
                <p class="text-sm text-gray-500 font-bold group-hover:text-red-600 transition-colors">تنبيهات رادار الصلاحية</p>
                <p class="text-xl font-black {{ $expiringCount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $expiringCount > 0 ? $expiringCount . ' دفعات مهددة!' : 'الوضع آمن' }}
                </p>
            </div>
        </a>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">آخر فواتير المبيعات</h2>
                <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded-full">مباشر</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-white border-b">
                        <tr>
                            <th class="p-3 text-sm font-bold text-gray-500">رقم الفاتورة</th>
                            <th class="p-3 text-sm font-bold text-gray-500">العميل</th>
                            <th class="p-3 text-sm font-bold text-gray-500">الإجمالي</th>
                            <th class="p-3 text-sm font-bold text-gray-500">الدفع</th>
                            <th class="p-3 text-sm font-bold text-gray-500">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-3 font-bold text-blue-600"><a href="/receipt/{{ $sale->id }}" target="_blank">{{ $sale->receipt_number }}</a></td>
                            <td class="p-3 text-sm font-bold">{{ $sale->customer ? $sale->customer->name : 'زبون عام' }}</td>
                            <td class="p-3 font-black text-gray-800">{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="p-3">
                                @if($sale->payment_status == 'paid') <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">خالص</span>
                                @elseif($sale->payment_status == 'partial') <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">جزئي</span>
                                @else <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">آجل</span>
                                @endif
                            </td>
                            <td class="p-3 text-xs text-gray-500 font-bold">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500 font-bold">لا توجد مبيعات اليوم.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-red-800">🚨 نواقص المخزن</h2>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-gray-100">
                    @forelse($lowStockProducts as $product)
                        <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->baseUnit ? $product->baseUnit->name : '' }}</p>
                            </div>
                            <div class="bg-red-100 text-red-700 font-black px-3 py-1 rounded-lg text-sm">
                                {{ (float)$product->current_stock }}
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-green-600 font-bold text-sm">المخزون بحالة ممتازة ✅</li>
                    @endforelse
                </ul>
            </div>
            @if(count($lowStockProducts) > 0)
            <div class="p-3 bg-gray-50 text-center border-t">
                <a href="{{ route('inventory') }}" class="text-sm font-bold text-blue-600 hover:underline">عرض كل المخزون &larr;</a>
            </div>
            @endif
        </div>

    </div>

</div>