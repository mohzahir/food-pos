

<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50">
   
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-emerald-500">📈</span> سجل المبيعات والفواتير
            </h1>
            <p class="text-slate-500 mt-2 font-bold">إدارة فواتير الزبائن، مراجعتها، تعديلها وإلغاؤها.</p>
        </div>
        <a href="{{ route('pos') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 px-6 rounded-2xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 flex items-center gap-2 text-sm">
            <span>🛒 شاشة الكاشير (POS)</span>
        </a>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3 shadow-sm text-lg">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-slate-100">
       
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span>🔍</span> تصفية وبحث
        </h3>
       
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">رقم الفاتورة أو اسم العميل</label>
                <input type="text" wire:model.live.debounce.300ms="search_receipt" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 rounded-xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none font-bold text-slate-700 transition-all" placeholder="بحث...">
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">من تاريخ</label>
                <input type="date" wire:model.live="date_from" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 rounded-xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none font-bold text-slate-700 transition-all">
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 mb-1.5 uppercase">إلى تاريخ</label>
                <input type="date" wire:model.live="date_to" class="w-full border-2 border-slate-200 bg-slate-50 p-2.5 rounded-xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none font-bold text-slate-700 transition-all">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.03)] overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-200">
                        <th class="p-4 font-black text-xs uppercase tracking-wider border-b border-slate-800">رقم الفاتورة</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider border-b border-slate-800">العميل</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">التاريخ</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">الإجمالي</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider border-b border-slate-800 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-600 font-mono font-bold px-3 py-1.5 rounded-lg text-sm" dir="ltr">{{ $sale->receipt_number }}</span>
                            </td>
                            <td class="p-4 font-black text-slate-800 text-sm">
                                {{ $sale->customer ? $sale->customer->name : 'زبون نقدي عام' }}
                            </td>
                            <td class="p-4 text-center text-slate-500 font-bold text-sm" dir="ltr">
                                {{ $sale->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-black text-emerald-600 text-lg" dir="ltr">{{ number_format($sale->total_amount, 0) }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="viewDetails({{ $sale->id }})" class="bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-xl font-bold text-xs transition-colors shadow-sm">
                                        👁️ عرض
                                    </button>
                                    <button wire:click="editSale({{ $sale->id }})" wire:confirm="هل تريد تعديل هذه الفاتورة؟ سيتم إلغاؤها وإرجاع أصنافها لشاشة الكاشير لتتمكن من تعديلها." class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white px-3 py-2 rounded-xl font-bold text-xs transition-colors shadow-sm">
                                        ✏️ تعديل
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center">
                                <div class="text-4xl mb-4 grayscale opacity-30">📭</div>
                                <p class="text-xl font-black text-slate-400">لا توجد مبيعات مطابقة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
           
            @if($sales->hasPages())
                <div class="p-5 bg-slate-50 border-t border-slate-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($isModalOpen && $selectedSale)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-4xl max-h-[90vh] flex flex-col border border-slate-200 overflow-hidden">
           
            <div class="bg-slate-900 p-6 flex justify-between items-center text-white border-b border-slate-800 shrink-0">
                <h3 class="text-lg font-black flex items-center gap-2">
                    <span class="text-emerald-400">📄</span> تفاصيل فاتورة <span class="font-mono text-slate-300">{{ $selectedSale->receipt_number }}</span>
                </h3>
                <button wire:click="closeModal" class="w-8 h-8 bg-slate-800 rounded-full flex items-center justify-center hover:bg-rose-500 transition-colors">&times;</button>
            </div>

            <div class="p-0 overflow-y-auto flex-1 custom-scrollbar bg-slate-50">
                <table class="w-full text-right border-collapse text-sm">
                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                        <tr>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200">المنتج</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الوحدة</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الكمية</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">سعر البيع</th>
                            <th class="p-4 font-black text-slate-500 text-[11px] uppercase border-b border-slate-200 text-center">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($saleItems as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-black text-slate-800">{{ $item->product ? $item->product->name : 'محذوف' }}</td>
                            <td class="p-4 text-center text-slate-500 font-bold bg-slate-50/50">{{ $item->unit ? $item->unit->name : '-' }}</td>
                            <td class="p-4 text-center font-black text-blue-600 text-base">{{ (float) $item->quantity }}</td>
                            <td class="p-4 text-center font-bold text-slate-600">{{ number_format($item->unit_price, 0) }}</td>
                            <td class="p-4 text-center font-black text-slate-800 bg-slate-50/50">{{ number_format($item->subtotal, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-200 bg-white shrink-0 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <button wire:click="closeModal" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-xl transition-colors">إغلاق</button>
                   
                    <a href="{{ route('receipt.show', $selectedSale->id) }}" target="_blank" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl transition-colors flex items-center gap-2">
                        <span>🖨️ طباعة</span>
                    </a>

                    <button wire:click="deleteSale({{ $selectedSale->id }})" wire:confirm="⚠️ تحذير: سيتم إلغاء الفاتورة نهائياً وإرجاع البضاعة للمخزن ولن تستطيع استعادتها!" class="px-6 py-2 bg-rose-100 text-rose-700 hover:bg-rose-600 hover:text-white font-black rounded-xl transition-colors flex items-center gap-2">
                        <span>🗑️ إلغاء نهائي</span>
                    </button>
                </div>
               
                <div class="bg-emerald-50 px-6 py-2 rounded-xl border border-emerald-200 flex items-center gap-3">
                    <span class="font-bold text-emerald-600 text-sm">الإجمالي:</span>
                    <span class="font-black text-emerald-700 text-xl" dir="ltr">{{ number_format($selectedSale->total_amount, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
.animate-slide-in { animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes slideIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>
