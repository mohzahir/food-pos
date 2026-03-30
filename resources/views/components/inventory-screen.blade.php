<div class="p-6 max-w-7xl mx-auto min-h-screen relative">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-black text-gray-800 flex items-center gap-3">
            <span class="text-blue-600">📋</span> جرد المخزون وتقييم الأصول
        </h1>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl mb-6 font-bold flex items-center gap-2 shadow-sm">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-rose-500">
            <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-wider">إجمالي رأس المال (بالتكلفة)</p>
            <h3 class="text-3xl font-black text-rose-600" dir="ltr">{{ number_format($totalInventoryCost, 0) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-emerald-500">
            <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-wider">القيمة السوقية (بسعر البيع)</p>
            <h3 class="text-3xl font-black text-emerald-600" dir="ltr">{{ number_format($totalExpectedRevenue, 0) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-blue-500">
            <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-wider">الأرباح المتوقعة عند البيع</p>
            <h3 class="text-3xl font-black text-blue-600" dir="ltr">{{ number_format($expectedProfit, 0) }}</h3>
        </div>
    </div>

    <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="relative">
            <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">🔍</span>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full bg-gray-50 border-2 border-gray-200 p-4 pr-12 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-gray-700 transition-all" placeholder="ابحث عن منتج بالاسم أو الباركود لجردة...">
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100">المنتج (الباركود)</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100 text-center">النظام (الدفتر)</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100 text-center">تكلفة الوحدة</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100 text-center">سعر البيع</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100 text-center">الحالة</th>
                        <th class="p-5 font-black text-xs uppercase tracking-wider border-b border-gray-100 text-center">تسوية (جرد)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="p-5">
                            <div class="font-black text-gray-800 text-sm">{{ $product->name }}</div>
                            <div class="text-[10px] text-gray-400 font-bold tracking-widest uppercase mt-1">SKU: {{ $product->sku }}</div>
                        </td>
                        
                        <td class="p-5 text-center">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <span class="font-black text-xl text-blue-600" dir="ltr">
                                    {{ (float) $product->current_stock }}
                                </span>
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md font-bold">{{ $product->baseUnit ? $product->baseUnit->name : 'وحدة' }}</span>
                            </div>
                        </td>
                        
                        <td class="p-5 text-center font-bold text-rose-500">{{ number_format($product->current_cost_price, 0) }}</td>
                        <td class="p-5 text-center font-black text-emerald-600">{{ number_format($product->current_selling_price, 0) }}</td>
                        
                        <td class="p-5 text-center">
                            @if($product->current_stock <= 0)
                                <span class="bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">نفد تماماً</span>
                            @elseif($product->current_stock < 10) 
                                <span class="bg-amber-50 text-amber-600 border border-amber-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">وشك النفاذ</span>
                            @else
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">متوفر</span>
                            @endif
                        </td>

                        <td class="p-5 text-center">
                            <button wire:click="openReconciliation({{ $product->id }})" class="bg-white border-2 border-gray-200 text-gray-600 hover:bg-blue-600 hover:border-blue-600 hover:text-white px-4 py-2 rounded-xl font-black text-xs transition-all shadow-sm flex items-center justify-center gap-2 mx-auto">
                                <span>⚖️ تسوية</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-gray-400 font-bold text-lg">لم يتم العثور على منتجات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isReconciliationModalOpen && $editingProduct)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-md overflow-hidden transform transition-all">
            
            <div class="bg-slate-900 p-6 flex justify-between items-center text-white border-b border-slate-800">
                <h3 class="text-lg font-black flex items-center gap-2">
                    <span class="text-blue-400">⚖️</span> تسوية وجرد الصنف
                </h3>
                <button wire:click="closeModal" class="w-8 h-8 bg-slate-800 rounded-full flex items-center justify-center hover:bg-rose-500 transition-colors">&times;</button>
            </div>

            <div class="p-8 space-y-6">
                <div class="bg-gray-50 border-2 border-gray-100 p-4 rounded-2xl text-center">
                    <p class="text-sm font-bold text-gray-500 mb-1">المنتج المراد تسويته</p>
                    <p class="text-xl font-black text-blue-700">{{ $editingProduct->name }}</p>
                    
                    <div class="flex justify-between items-center mt-4 bg-white p-3 rounded-xl border border-gray-200">
                        <div class="text-center w-1/2 border-l border-gray-100">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">الرصيد الدفتري الحالي</span>
                            <span class="text-2xl font-black text-gray-700">{{ (float) $editingProduct->current_stock }}</span>
                        </div>
                        <div class="text-center w-1/2">
                            <span class="block text-[10px] font-black text-emerald-500 uppercase tracking-wider">الرصيد الفعلي (الجديد)</span>
                            <span class="text-2xl font-black text-emerald-600">{{ $actual_stock !== '' ? (float) $actual_stock : '؟' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-wider">الكمية الفعلية الجردية (على الرف)</label>
                    <div class="relative">
                        <input type="number" step="any" wire:model.live="actual_stock" onclick="this.select()" autofocus class="w-full border-2 border-emerald-200 bg-emerald-50/50 p-4 rounded-2xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none text-2xl font-black text-center text-emerald-700 transition-all shadow-inner">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-bold text-sm">{{ $editingProduct->baseUnit ? $editingProduct->baseUnit->name : '' }}</span>
                        </div>
                    </div>
                    @error('actual_stock') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-wider">سبب التسوية / التعديل</label>
                    <select wire:model="reconciliation_reason" class="w-full border-2 border-gray-200 bg-white p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-gray-700 transition-all cursor-pointer">
                        <option value="جرد دوري">📋 جرد دوري طبيعي</option>
                        <option value="بضاعة تالفة / منتهية">🗑️ بضاعة تالفة / منتهية</option>
                        <option value="سرقة / عجز مجهول">⚠️ سرقة / عجز مجهول</option>
                        <option value="إدخال خاطئ سابق">✏️ تصحيح إدخال خاطئ سابق</option>
                        <option value="إدخال رصيد افتتاحي">📦 إدخال رصيد افتتاحي (أول مدة)</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button wire:click="saveReconciliation" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all transform hover:-translate-y-0.5 text-lg flex items-center justify-center gap-2">
                        <span>حفظ واعتماد الرصيد الفعلي</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>