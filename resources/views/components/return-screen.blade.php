<div class="p-4 sm:p-8 max-w-6xl mx-auto min-h-screen bg-slate-50">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">↩️</span> إدارة المرتجعات
            </h1>
            <p class="text-slate-500 mt-2 font-bold">البحث عن الفواتير، استرداد الأصناف للمخزن، وتسوية الحسابات</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3 shadow-sm">
            <span class="text-xl">✅</span> 
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3 shadow-sm">
            <span class="text-xl">⚠️</span> 
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-indigo-500 opacity-80"></div>
        
        <form wire:submit.prevent="searchInvoice" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full relative">
                <label class="block text-sm font-black text-slate-600 mb-2 uppercase tracking-wide">رقم الفاتورة أو الباركود</label>
                <div class="relative focus-within:text-blue-600 text-slate-400 transition-colors">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <input type="text" wire:model="receipt_number" class="w-full border-2 border-slate-200 bg-slate-50 p-4 pr-12 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none text-xl font-black text-slate-800 transition-all placeholder-slate-300" placeholder="مثال: INV-1710..." autofocus dir="ltr">
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2 text-lg">
                <span>بحث الفاتورة</span>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    @if($sale)
    <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] overflow-hidden border border-slate-100 animate-slide-in">
        
        <div class="bg-slate-900 p-6 sm:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-white">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest">تفاصيل الفاتورة</span>
                    <h2 class="text-2xl font-black font-mono tracking-wider" dir="ltr">{{ $sale->receipt_number }}</h2>
                </div>
                <p class="text-slate-400 mt-2 text-sm flex items-center gap-2">
                    <span>👤 العميل: <span class="font-bold text-slate-200">{{ $sale->customer ? $sale->customer->name : 'نقدي عام' }}</span></span>
                    <span class="text-slate-600">|</span>
                    <span>🕒 التاريخ: <span class="font-bold text-slate-200">{{ $sale->created_at->format('Y-m-d h:i A') }}</span></span>
                </p>
            </div>
            
            <div class="bg-slate-800 border border-slate-700 p-4 rounded-2xl text-left min-w-[180px]">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">الإجمالي الحالي</p>
                <div class="flex items-baseline justify-end gap-1">
                    <span class="text-3xl font-black text-emerald-400" dir="ltr">{{ number_format($sale->total_amount, 0) }}</span>
                    <span class="text-sm font-bold text-slate-500">SDG</span>
                </div>
            </div>
        </div>

        @if($sale->items->count() > 0)
        <div class="overflow-x-auto p-4 sm:p-6">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-black uppercase tracking-wider">
                        <th class="p-4 rounded-r-xl">الصنف (الوحدة)</th>
                        <th class="p-4 text-center">الكمية المشتراة</th>
                        <th class="p-4 text-center">سعر الوحدة</th>
                        <th class="p-4 text-center">الإجمالي</th>
                        <th class="p-4 text-center text-rose-500 rounded-l-xl">إجراء المرتجع ⚠️</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->items as $item)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="p-4">
                            <p class="font-black text-slate-800 text-base">{{ $item->product->name }}</p>
                            <span class="inline-block mt-1 bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-slate-200">
                                {{ $item->unit->name }}
                            </span>
                        </td>
                        <td class="p-4 text-center font-black text-lg text-slate-700">{{ (float) $item->quantity }}</td>
                        <td class="p-4 text-center font-bold text-slate-500">{{ number_format($item->unit_price, 0) }}</td>
                        <td class="p-4 text-center font-black text-blue-600 text-lg">{{ number_format($item->subtotal, 0) }}</td>
                        
                        <td class="p-4 bg-rose-50/50 group-hover:bg-rose-50 rounded-xl transition-colors">
                            <div class="flex items-center justify-center gap-2">
                                <input type="number" step="any" min="0" max="{{ $item->quantity }}" wire:model="return_quantities.{{ $item->id }}" class="w-24 border-2 border-rose-200 bg-white p-2.5 rounded-xl text-center focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none font-black text-rose-700 shadow-inner transition-all" placeholder="الكمية">
                                
                                <button wire:click="processReturn({{ $item->id }})" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2.5 rounded-xl text-sm font-black shadow-md shadow-rose-200 transition-all transform hover:-translate-y-0.5 flex items-center gap-1">
                                    <span>إرجاع</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center bg-slate-50">
            <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl grayscale opacity-50 filter">🗑️</span>
            </div>
            <h3 class="text-xl font-black text-slate-700 mb-2">الفاتورة ملغية (مفرغة)</h3>
            <p class="text-slate-500 font-bold">تم إرجاع جميع أصناف هذه الفاتورة مسبقاً، لا يوجد شيء لإرجاعه.</p>
        </div>
        @endif

    </div>
    @endif

    @if($isRefundModalOpen)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 transition-opacity">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-md overflow-hidden transform transition-all animate-fade-in-up border border-slate-200">
            
            <div class="bg-rose-50 p-5 flex justify-between items-center text-rose-800 border-b border-rose-100">
                <h3 class="text-lg font-black flex items-center gap-2">
                    <span>💵</span> تسوية مبالغ المرتجع
                </h3>
                <button wire:click="$set('isRefundModalOpen', false)" class="text-rose-400 hover:text-rose-800 bg-white hover:bg-rose-200 w-8 h-8 rounded-full flex items-center justify-center transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50 space-y-4">
                
                @if (session()->has('modal_error'))
                    <div class="bg-rose-100 text-rose-700 p-3 rounded-xl text-xs font-bold text-center border border-rose-200">
                        {{ session('modal_error') }}
                    </div>
                @endif

                <div class="bg-white border-2 border-slate-100 p-4 rounded-2xl shadow-sm space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold text-slate-500">إجمالي قيمة المرتجع:</span>
                        <span class="font-black text-slate-800" dir="ltr">{{ number_format($total_refund_amount, 0) }} SDG</span>
                    </div>
                    
                    @if($debt_to_deduct > 0)
                    <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-2">
                        <span class="font-bold text-emerald-600">خصم من ديون العميل:</span>
                        <span class="font-black text-emerald-600" dir="ltr">- {{ number_format($debt_to_deduct, 0) }} SDG</span>
                    </div>
                    @endif
                </div>

                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 text-center">
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">المبلغ المطلوب تسليمه للعميل الآن</p>
                    <p class="text-3xl font-black text-blue-700" dir="ltr">{{ number_format($amount_to_pay_customer, 0) }} SDG</p>
                </div>

                @if($amount_to_pay_customer > 0)
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="relative">
                        <label class="absolute -top-2 left-2 bg-slate-50 px-1 text-[9px] font-black text-emerald-600 z-10">إرجاع من الدرج (كاش)</label>
                        <input type="number" step="any" wire:model="refund_cash" onclick="this.select()" class="w-full border-2 border-emerald-200 rounded-xl p-3 text-lg focus:ring-2 focus:ring-emerald-500 outline-none font-black text-center text-emerald-700 bg-white">
                    </div>
                    
                    <div class="relative">
                        <label class="absolute -top-2 left-2 bg-slate-50 px-1 text-[9px] font-black text-indigo-600 z-10">إرجاع تحويل (بنكك)</label>
                        <input type="number" step="any" wire:model="refund_bankak" onclick="this.select()" class="w-full border-2 border-indigo-200 rounded-xl p-3 text-lg focus:ring-2 focus:ring-indigo-500 outline-none font-black text-center text-indigo-700 bg-white">
                    </div>
                </div>
                @endif

                <div class="pt-4 mt-2 border-t border-slate-200">
                    <button wire:click="confirmReturn" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-4 rounded-xl shadow-lg shadow-rose-200 transition-all transform hover:-translate-y-0.5 text-lg">
                        تأكيد سحب المبلغ والإرجاع
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>

<style>
/* حركة الظهور الانسيابية */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>