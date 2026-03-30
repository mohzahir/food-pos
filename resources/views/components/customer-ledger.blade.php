<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-slate-400 mb-2">
                <a href="{{ route('customers.index') }}" class="hover:text-blue-600 transition-colors font-bold text-sm">سجل العملاء</a>
                <span>/</span>
                <span class="text-slate-800 font-bold text-sm">كشف الحساب</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">📄</span> كشف حساب: {{ $customer->name }}
            </h1>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('print.ledger', $customer->id) }}" target="_blank" class="bg-slate-800 text-white hover:bg-slate-700 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>طباعة (A4)</span>
            </a>

            <a href="{{ route('customers.index') }}" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden group">
                <div class="absolute top-[-20px] left-[-20px] w-32 h-32 bg-rose-500 rounded-full mix-blend-screen filter blur-[40px] opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <div class="relative z-10">
                    <p class="text-slate-400 font-bold text-sm mb-2 uppercase tracking-widest">إجمالي الديون المتراكمة</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl font-black text-white tracking-tighter" dir="ltr">{{ number_format($customer->balance, 0) }}</h3>
                        <span class="text-rose-400 font-bold">SDG</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-rose-300 text-xs font-bold bg-rose-500/10 w-fit px-3 py-1 rounded-full border border-rose-500/20">
                        <span class="animate-pulse">⚠️</span> مديونية معلقة
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-emerald-500 rounded-full"></span> استلام دفعة جديدة
                </h3>
                
                <form wire:submit.prevent="addPayment" class="space-y-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase">المبلغ المدفوع</label>
                        <input type="number" step="any" wire:model="amount" class="w-full border-2 border-slate-100 bg-slate-50 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-xl text-slate-800 transition-all shadow-inner" placeholder="0.00">
                        @error('amount') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="$set('payment_method', 'cash')" class="py-3 rounded-xl font-bold border-2 transition-all flex items-center justify-center gap-2 {{ $payment_method == 'cash' ? 'bg-blue-50 border-blue-600 text-blue-700' : 'bg-white border-slate-100 text-slate-400 hover:bg-slate-50' }}">
                            <span>💵 كاش</span>
                        </button>
                        <button type="button" wire:click="$set('payment_method', 'bankak')" class="py-3 rounded-xl font-bold border-2 transition-all flex items-center justify-center gap-2 {{ $payment_method == 'bankak' ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-white border-slate-100 text-slate-400 hover:bg-slate-50' }}">
                            <span>📱 بنكك</span>
                        </button>
                    </div>

                    @if($payment_method == 'bankak')
                    <div class="animate-slide-in">
                        <label class="block text-xs font-black text-indigo-600 mb-2 uppercase tracking-wide">رقم الإشعار / المرجع</label>
                        <input type="text" wire:model="transaction_number" class="w-full border-2 border-indigo-100 p-4 rounded-2xl bg-indigo-50/30 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 outline-none font-bold text-indigo-800" placeholder="أدخل رقم العملية...">
                        @error('transaction_number') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase">ملاحظات (اختياري)</label>
                        <input type="text" wire:model="notes" class="w-full border-2 border-slate-100 rounded-2xl p-4 focus:ring-4 focus:ring-slate-100 focus:border-slate-400 outline-none font-bold text-slate-700 bg-slate-50 transition-all text-sm" placeholder="مثال: دفعة حساب شهر مارس">
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2 text-lg">
                        <span>حفظ السند وتحديث الحساب</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-8">
            
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                        <span class="text-blue-600">🏛️</span> سجل الدفعات (سندات القبض)
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-black uppercase tracking-wider">
                                <th class="p-4">التاريخ والوقت</th>
                                <th class="p-4 text-center">المبلغ</th>
                                <th class="p-4">طريقة التحصيل</th>
                                <th class="p-4">الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <div class="text-slate-800 font-bold text-sm">{{ $payment->created_at->format('Y-m-d') }}</div>
                                    <div class="text-slate-400 text-[10px] font-bold">{{ $payment->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-emerald-600 font-black text-lg" dir="ltr">+{{ number_format($payment->amount, 0) }}</span>
                                </td>
                                <td class="p-4">
                                    @if($payment->payment_method == 'bankak')
                                        <div class="flex flex-col">
                                            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black w-fit">📱 بنكك</span>
                                            <span class="text-[10px] text-slate-400 font-mono mt-1" dir="ltr">#{{ $payment->transaction_number }}</span>
                                        </div>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-black w-fit">💵 كاش</span>
                                    @endif
                                </td>
                                <td class="p-4 text-xs font-bold text-slate-500 max-w-[150px] truncate" title="{{ $payment->notes }}">
                                    {{ $payment->notes ?? '---' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center">
                                    <div class="text-4xl mb-3 opacity-20">📭</div>
                                    <p class="text-slate-400 font-bold">لا توجد دفعات مسجلة لهذا العميل بعد.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] border border-slate-100 p-6">
                <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span class="text-rose-500">🚨</span> الفواتير الآجلة المعلقة (لم تسدد بالكامل)
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($unpaidSales as $sale)
                        <div class="group relative bg-white border border-rose-100 rounded-2xl p-5 hover:border-rose-300 transition-all hover:shadow-md">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-xs font-black text-slate-400 uppercase">رقم الفاتورة</span>
                                    <h5 class="text-lg font-black text-slate-800 tracking-tight">#{{ $sale->receipt_number }}</h5>
                                </div>
                                <span class="bg-slate-50 text-slate-500 px-3 py-1 rounded-lg text-[10px] font-black">{{ $sale->created_at->format('Y-m-d') }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center bg-rose-50/50 p-3 rounded-xl border border-rose-100/50">
                                <div>
                                    <p class="text-[10px] text-rose-400 font-black uppercase">المتبقي المطلوب سداده</p>
                                    <p class="text-xl font-black text-rose-600" dir="ltr">{{ number_format($sale->remaining_amount, 0) }}</p>
                                </div>
                                <div class="text-left">
                                    <p class="text-[10px] text-slate-400 font-bold">من أصل</p>
                                    <p class="text-sm font-bold text-slate-500">{{ number_format($sale->total_amount, 0) }}</p>
                                </div>
                            </div>

                            <a href="/receipt/{{ $sale->id }}" target="_blank" class="absolute top-4 left-4 opacity-0 group-hover:opacity-100 transition-opacity text-slate-400 hover:text-blue-600" title="عرض الفاتورة الأصلية">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-10 flex flex-col items-center justify-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <span class="text-3xl mb-2 grayscale opacity-30">✨</span>
                            <p class="text-slate-400 font-bold">هذا العميل لا يمتلك أي فواتير معلقة حالياً.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

<style>
/* حركة الظهور الناعمة */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>