<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">📚</span> سجل المشتريات والفواتير
            </h1>
            <p class="text-slate-500 mt-2 font-bold">مراجعة البضائع المستلمة، ديون الموردين، وإدارة السداد</p>
        </div>
        <a href="{{ route('purchases') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-6 rounded-2xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 flex items-center gap-2 text-sm">
            <span>➕ تسجيل فاتورة جديدة</span>
        </a>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-slate-100 relative overflow-hidden group">


        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">✅</span> 
                    <span class="text-lg">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <span class="text-lg">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif


        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-400 opacity-50"></div>

        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span>🔍</span> تصفية وبحث الفواتير
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">اسم المورد</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-slate-400 text-sm">👤</span>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search_supplier" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 pr-10 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-700 transition-all placeholder-slate-400 text-sm" placeholder="اكتب للبحث...">
                </div>
            </div>
            
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">من تاريخ</label>
                <div class="relative">
                    <input type="date" wire:model.live="date_from" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-700 transition-all text-sm">
                </div>
            </div>
            
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">إلى تاريخ</label>
                <div class="relative">
                    <input type="date" wire:model.live="date_to" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-700 transition-all text-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-200">
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800">المرجع</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800">المورد</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">التاريخ</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">الإجمالي</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">حالة السداد</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="p-5">
                                <span class="bg-slate-100 text-slate-500 font-mono font-bold px-3 py-1.5 rounded-lg text-sm" dir="ltr">#{{ $purchase->id }}</span>
                            </td>
                            <td class="p-5 font-black text-slate-800 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs">🚛</div>
                                    {{ $purchase->supplier_name ?: 'مورد عام (غير محدد)' }}
                                </div>
                            </td>
                            <td class="p-5 text-center text-slate-500 font-bold text-sm">
                                {{ date('Y/m/d', strtotime($purchase->purchase_date)) }}
                            </td>
                            <td class="p-5 text-center">
                                <span class="font-black text-slate-800 text-lg" dir="ltr">{{ number_format($purchase->total_amount, 0) }}</span>
                            </td>
                            <td class="p-5 text-center">
                                @if($purchase->payment_status == 'paid')
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-1.5 rounded-xl text-xs font-black shadow-sm">
                                        <span>خالصة</span> <span>✅</span>
                                    </span>
                                @else
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="inline-flex flex-col items-center bg-rose-50 text-rose-700 border border-rose-200 px-4 py-1.5 rounded-xl shadow-sm">
                                            <span class="text-[10px] font-bold uppercase tracking-wider">متبقي للدفع</span>
                                            <span class="font-black text-sm" dir="ltr">{{ number_format($purchase->remaining_amount, 0) }}</span>
                                        </span>
                                        <button wire:click="openPaymentModal({{ $purchase->id }})" class="text-[10px] bg-rose-600 text-white font-black px-4 py-1.5 rounded-lg hover:bg-rose-700 transition-colors shadow-sm flex items-center gap-1 w-full justify-center">
                                            <span>سداد دفعة</span> <span>💸</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="p-5 text-center">
                                <button wire:click="viewDetails({{ $purchase->id }})" class="bg-white border-2 border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm flex items-center gap-2 mx-auto group-hover:border-slate-300">
                                    <span>👁️ التفاصيل</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 grayscale opacity-30 shadow-inner">📭</div>
                                    <p class="text-xl font-black text-slate-400">لا توجد فواتير مطابقة</p>
                                    <p class="text-xs font-bold text-slate-300 mt-1">تأكد من فلاتر البحث أو أضف فاتورة جديدة.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($purchases->hasPages())
                <div class="p-5 bg-slate-50 border-t border-slate-100">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($isModalOpen && $selectedPurchase)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-5xl max-h-[90vh] flex flex-col animate-slide-in border border-slate-200 overflow-hidden">
            
            <div class="bg-slate-900 p-6 flex justify-between items-center text-white border-b border-slate-800 shrink-0">
                <div>
                    <h3 class="text-xl font-black flex items-center gap-2">
                        <span class="text-blue-400">📄</span> تفاصيل فاتورة مشتريات <span class="font-mono text-slate-300">#{{ $selectedPurchase->id }}</span>
                    </h3>
                    <p class="text-xs text-slate-400 font-bold mt-2 flex items-center gap-2">
                        <span>المورد: <span class="text-blue-300">{{ $selectedPurchase->supplier_name ?: 'مورد عام' }}</span></span>
                        <span class="text-slate-600">|</span>
                        <span>التاريخ: {{ date('Y-m-d', strtotime($selectedPurchase->purchase_date)) }}</span>
                    </p>
                </div>
                <button wire:click="closeModal" class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center hover:bg-rose-500 transition-colors shadow-inner">&times;</button>
            </div>

            <div class="p-0 overflow-y-auto flex-1 custom-scrollbar bg-slate-50">
                <table class="w-full text-right border-collapse text-sm">
                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                        <tr>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200">المنتج</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الوحدة</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الكمية</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">التكلفة للوحدة</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الإجمالي الفرعي</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">صلاحية</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($purchaseItems as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-black text-slate-800">{{ $item->product ? $item->product->name : 'منتج محذوف' }}</td>
                            <td class="p-4 text-center text-slate-500 font-bold bg-slate-50/50">{{ $item->unit ? $item->unit->name : '-' }}</td>
                            <td class="p-4 text-center font-black text-blue-600 text-base">{{ (float) $item->quantity }}</td>
                            <td class="p-4 text-center font-bold text-slate-600">{{ number_format($item->unit_cost_price, 0) }}</td>
                            <td class="p-4 text-center font-black text-slate-800 bg-slate-50/50">{{ number_format($item->quantity * $item->unit_cost_price, 0) }}</td>
                            <td class="p-4 text-center text-rose-500 text-[10px] font-bold">{{ $item->expiry_date ? date('Y-m-d', strtotime($item->expiry_date)) : '---' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-200 bg-white shrink-0 flex flex-col sm:flex-row justify-between items-center gap-4">
                
                <div class="flex gap-3 w-full sm:w-auto">
                    <button wire:click="closeModal" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-xl transition-colors w-full sm:w-auto">إغلاق النافذة</button>
                    
                    <a href="{{ route('print.purchase', $selectedPurchase->id) }}" target="_blank" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 w-full sm:w-auto transform hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>طباعة الفاتورة (A4)</span>
                    </a>
                </div>
                
                <div class="bg-slate-900 px-6 py-3 rounded-xl shadow-inner border border-slate-800 flex items-center gap-3 w-full sm:w-auto justify-center">
                    <span class="font-bold text-slate-400 text-sm">إجمالي الفاتورة:</span>
                    <span class="font-black text-emerald-400 text-xl" dir="ltr">{{ number_format($selectedPurchase->total_amount, 0) }}</span>
                </div>
            </div>

        </div>
    </div>
    @endif

    @if($isPaymentModalOpen && $purchaseToPay)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-md overflow-hidden animate-slide-in border border-slate-200">
            
            <div class="bg-rose-50 p-6 flex justify-between items-center border-b border-rose-100">
                <h3 class="text-xl font-black text-rose-700 flex items-center gap-2">
                    <span>💸</span> سداد مديونية مورد
                </h3>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="bg-white border-2 border-slate-100 p-4 rounded-2xl shadow-sm text-center">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">المورد</p>
                    <p class="text-lg font-black text-blue-600 mb-3">{{ $purchaseToPay->supplier_name ?: 'عام' }}</p>
                    
                    <div class="bg-rose-50 p-3 rounded-xl border border-rose-100">
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mb-1">المبلغ المتبقي</p>
                        <p class="text-2xl font-black text-rose-700" dir="ltr">{{ number_format($purchaseToPay->remaining_amount, 0) }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-600 mb-2 uppercase">المبلغ المراد سداده الآن</label>
                    <input type="number" step="1" wire:model="pay_amount" max="{{ $purchaseToPay->remaining_amount }}" class="w-full border-2 border-slate-200 bg-slate-50 p-4 rounded-2xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none text-center font-black text-2xl text-slate-800 transition-all shadow-inner" placeholder="0">
                    @error('pay_amount') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-600 mb-2 uppercase">طريقة السحب (من أين ستدفع؟)</label>
                    <select wire:model="pay_method" class="w-full border-2 border-slate-200 bg-white p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-slate-700 transition-all cursor-pointer appearance-none shadow-sm">
                        <option value="cash">💵 نقداً (من درج الكاشير)</option>
                        <option value="bankak">📱 تحويل (من تطبيق بنكك)</option>
                    </select>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button wire:click="submitPayment" class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                        <span>تأكيد السداد</span> <span>✅</span>
                    </button>
                    <button wire:click="$set('isPaymentModalOpen', false)" class="w-full bg-slate-100 text-slate-500 font-bold py-3 rounded-2xl hover:bg-slate-200 transition-all">إلغاء العملية</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
/* تجميل شريط التمرير */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* حركة الظهور الانسيابية */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>