<div class="p-6 max-w-7xl mx-auto min-h-screen font-sans" dir="rtl">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 mb-4 md:mb-0">
            <div class="bg-blue-100 p-3 rounded-full text-3xl">📊</div>
            <div>
                <h1 class="text-3xl font-black text-gray-800">تقرير الأرباح اليومية</h1>
                <p class="text-gray-500 font-bold mt-1">تحليل مفصل للمبيعات، التكاليف، والمصروفات</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <label class="font-bold text-gray-700">تاريخ التقرير:</label>
            <input type="date" wire:model.live="selectedDate" class="border-2 border-blue-200 bg-blue-50 text-blue-900 font-black p-3 rounded-xl outline-none focus:border-blue-500 transition-colors shadow-inner">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-t-4 border-indigo-500 relative overflow-hidden">
            <div class="absolute -left-4 -top-4 opacity-5 text-8xl">💰</div>
            <h3 class="text-gray-500 font-bold mb-1 text-lg">إجمالي المبيعات (الإيرادات)</h3>
            <p class="text-3xl font-black text-indigo-700 mb-2" dir="ltr">{{ number_format($totalSales, 0) }}</p>
            <p class="text-xs text-gray-400 font-bold">مجموع فواتير المبيعات لليوم</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-t-4 border-orange-500 relative overflow-hidden">
            <div class="absolute -left-4 -top-4 opacity-5 text-8xl">📦</div>
            <h3 class="text-gray-500 font-bold mb-1 text-lg">تكلفة البضاعة المباعة (COGS)</h3>
            <p class="text-3xl font-black text-orange-600 mb-2" dir="ltr">- {{ number_format($costOfGoodsSold, 0) }}</p>
            <p class="text-xs text-gray-400 font-bold">تكلفة الشراء للمنتجات التي تم بيعها</p>
        </div>

        <div class="bg-blue-600 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden transform hover:scale-105 transition-transform duration-300">
            <div class="absolute -left-4 -top-4 opacity-10 text-8xl">📈</div>
            <h3 class="text-blue-100 font-bold mb-1 text-lg">مجمل الربح (مبدئي)</h3>
            <p class="text-4xl font-black mb-2" dir="ltr">{{ number_format($grossProfit, 0) }}</p>
            <p class="text-xs text-blue-200 font-bold border-t border-blue-500 pt-2 mt-2">قبل خصم المصروفات والتوالف</p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-gray-600 text-sm">المصروفات اليومية 💸</h4>
                    <p class="text-xl font-black text-red-600 mt-1" dir="ltr">- {{ number_format($totalExpenses, 0) }}</p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg text-red-500">🔻</div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-gray-600 text-sm">تسويات المخزون (توالف/زيادات) ⚖️</h4>
                    <p class="text-xl font-black mt-1 {{ $inventoryAdjustments < 0 ? 'text-red-600' : ($inventoryAdjustments > 0 ? 'text-emerald-600' : 'text-gray-400') }}" dir="ltr">
                        {{ $inventoryAdjustments > 0 ? '+' : '' }}{{ number_format($inventoryAdjustments, 0) }}
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-gray-500">📦</div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b font-bold text-sm text-gray-700">تفاصيل المصروفات لليوم</div>
                <div class="max-h-48 overflow-y-auto">
                    @forelse($expensesList as $exp)
                        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                            <span class="text-sm font-bold text-gray-800">{{ $exp->notes ?? 'مصروف غير محدد' }}</span>
                            <span class="text-sm font-black text-red-600" dir="ltr">{{ number_format($exp->amount, 0) }}</span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-400 font-bold">لا توجد مصروفات مسجلة اليوم</div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="lg:col-span-2">
            <div class="{{ $netProfit >= 0 ? 'bg-gradient-to-br from-emerald-500 to-teal-700' : 'bg-gradient-to-br from-red-500 to-rose-700' }} rounded-3xl p-10 shadow-2xl text-white h-full flex flex-col justify-center relative overflow-hidden">
                
                <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute left-0 bottom-0 w-48 h-48 bg-black opacity-10 rounded-full blur-2xl transform -translate-x-1/2 translate-y-1/2"></div>

                <div class="relative z-10 text-center">
                    <h2 class="text-2xl font-bold mb-4 opacity-90 drop-shadow-sm">صافي الربح النهائي (الصافي لجيبك)</h2>
                    
                    <div class="flex justify-center items-baseline gap-2 mb-6">
                        <span class="text-7xl font-black tracking-tight drop-shadow-lg" dir="ltr">
                            {{ number_format(abs($netProfit), 0) }}
                        </span>
                        <span class="text-2xl font-bold opacity-80">SDG</span>
                    </div>

                    @if($netProfit > 0)
                        <div class="inline-flex items-center gap-2 bg-white bg-opacity-20 backdrop-blur-sm px-6 py-2 rounded-full font-bold text-lg shadow-inner">
                            <span>🎉 يوم رابح وممتاز!</span>
                        </div>
                    @elseif($netProfit < 0)
                        <div class="inline-flex items-center gap-2 bg-black bg-opacity-20 backdrop-blur-sm px-6 py-2 rounded-full font-bold text-lg shadow-inner">
                            <span>⚠️ يوم به خسارة مالية (راجع المصروفات)</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 bg-white bg-opacity-20 backdrop-blur-sm px-6 py-2 rounded-full font-bold text-lg shadow-inner">
                            <span>⚖️ نقطة التعادل (لا ربح ولا خسارة)</span>
                        </div>
                    @endif
                </div>
                
                <div class="relative z-10 mt-10 border-t border-white border-opacity-20 pt-6 text-center">
                    <p class="text-sm font-bold opacity-80">
                        كيف تم الحساب؟ (مجمل الربح) - (المصروفات) + (فروقات الجرد) = صافي الربح
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>