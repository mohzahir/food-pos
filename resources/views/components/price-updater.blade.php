<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50" x-data="{ showToast: false }" @price-saved.window="showToast = true; setTimeout(() => showToast = false, 2000)">
    
    <div x-show="showToast" x-transition.opacity class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-slate-800 text-emerald-400 font-black px-6 py-2 rounded-full shadow-lg z-50 flex items-center gap-2" style="display: none;">
        <span>✅</span> تم الحفظ تلقائياً
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">⚡</span> التحديث السريع للأسعار
            </h1>
            <p class="text-slate-500 mt-2 font-bold">نظام الإدخال السريع: اكتب السعر واضغط (Tab) للحفظ التلقائي الفوري.</p>
        </div>
        
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-slate-400">🔍</span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-2 border-slate-200 bg-white p-3 pr-10 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-700 transition-all shadow-sm" placeholder="ابحث عن منتج لتسعيره...">
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-slate-100">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-200 text-xs font-black uppercase tracking-widest">
                        <th class="p-4 border-b border-slate-800 w-1/3">المنتج والوحدة الأساسية</th>
                        <th class="p-4 border-b border-slate-800 text-center w-1/6">التكلفة (قطاعي)</th>
                        <th class="p-4 border-b border-slate-800 text-center w-1/6">سعر البيع (قطاعي)</th>
                        <th class="p-4 border-b border-slate-800 bg-purple-900/50 w-1/3">تسعيرات وحدات الجملة المربوطة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        
                        <td class="p-4 align-top">
                            <div class="font-black text-slate-800 text-base mb-1">{{ $product->name }}</div>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md border border-slate-200">
                                أساسي: {{ $product->baseUnit ? $product->baseUnit->name : 'وحدة' }}
                            </span>
                        </td>
                        
                        <td class="p-4 align-top">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-[9px] font-black text-slate-300 pointer-events-none">SDG</span>
                                <input type="number" step="any" 
                                    wire:change="updateCostPrice({{ $product->id }}, $event.target.value)" 
                                    value="{{ (float) $product->current_cost_price }}" 
                                    onclick="this.select()"
                                    class="w-full border-2 border-slate-200 bg-white p-2.5 rounded-lg focus:border-rose-400 focus:ring-4 focus:ring-rose-100 outline-none text-center font-black text-slate-700 transition-all">
                            </div>
                        </td>

                        <td class="p-4 align-top">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-[9px] font-black text-emerald-300 pointer-events-none">SDG</span>
                                <input type="number" step="any" 
                                    wire:change="updateRetailPrice({{ $product->id }}, $event.target.value)" 
                                    value="{{ (float) $product->current_selling_price }}" 
                                    onclick="this.select()"
                                    class="w-full border-2 border-emerald-300 bg-emerald-50 p-2.5 rounded-lg focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none text-center font-black text-emerald-800 transition-all shadow-inner">
                            </div>
                        </td>

                        <td class="p-4 align-top bg-purple-50/30 border-r border-slate-100">
                            @if(count($product->productUnits) > 0)
                                <div class="flex flex-col gap-2">
                                    @foreach($product->productUnits as $pu)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black text-purple-700 bg-purple-100 px-2 py-1.5 rounded-md w-20 text-center truncate border border-purple-200" title="{{ $pu->unit->name }}">
                                            {{ $pu->unit->name }}
                                        </span>
                                        <div class="relative flex-1">
                                            <input type="number" step="any" 
                                                wire:change="updateWholesalePrice({{ $pu->id }}, $event.target.value)" 
                                                value="{{ (float) $pu->specific_selling_price }}" 
                                                onclick="this.select()"
                                                class="w-full border-2 border-purple-200 bg-white p-1.5 rounded-md focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none text-center font-bold text-purple-800 transition-all text-sm"
                                                placeholder="تلقائي: {{ number_format($product->current_selling_price * $pu->unit->conversion_rate, 0) }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-[10px] font-bold text-slate-400 text-center py-2 border border-dashed border-slate-200 rounded-lg">
                                    يباع قطاعي فقط
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center">
                            <div class="text-5xl mb-3 opacity-30 grayscale">📭</div>
                            <p class="font-black text-slate-500 text-lg">لم يتم العثور على منتجات</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($products->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<style>
.custom-scrollbar::-webkit-scrollbar { height: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>