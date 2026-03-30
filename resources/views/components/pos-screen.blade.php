<div class="p-4 sm:p-6 bg-slate-50 min-h-screen">
    
    @if (session()->has('success'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-emerald-500 text-white px-6 py-3 rounded-full shadow-lg font-bold animate-fade-in-down flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-rose-500 text-white px-6 py-3 rounded-full shadow-lg font-bold animate-fade-in-down flex items-center gap-2">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8rem)]">
        
        <div class="col-span-1 lg:col-span-4 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-4 flex flex-col h-[85vh] lg:h-[calc(100vh-6rem)] overflow-y-auto custom-scrollbar">
            
            <div class="flex justify-between items-center mb-3 pb-3 border-b border-slate-100 shrink-0">
                <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <span class="text-blue-600">🧾</span> الفاتورة الحالية
                </h2>
                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full border border-blue-100 shadow-sm">
                    {{ count($cart) }} أصناف
                </span>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-3 mb-3 min-h-[250px]">
                @forelse ($cart as $key => $item)
                    <div wire:key="cart-item-{{ $key }}-{{ $item['quantity'] }}" class="bg-white border border-slate-200 shadow-sm rounded-2xl p-3 hover:border-blue-300 transition-colors group relative overflow-hidden">
                        
                        <button wire:click="removeFromCart('{{ $key }}')" class="absolute top-2 left-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition-colors z-10" title="إزالة من الفاتورة">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div class="pr-1 mb-2">
                            <h4 class="font-black text-slate-800 text-sm leading-tight w-10/12 truncate">{{ $item['name'] }}</h4>
                            <span class="inline-block mt-1 bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-slate-200">
                                وحدة: {{ $item['unit_name'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 items-end bg-slate-50/80 p-2 rounded-xl border border-slate-100">
                            <div class="flex flex-col">
                                <label class="text-[9px] font-bold text-slate-500 mb-1 text-center">سعر الوحدة</label>
                                <input type="number" step="any" min="0" wire:change="updatePrice('{{ $key }}', $event.target.value)" value="{{ (float) $item['unit_price'] }}" class="w-full text-center font-bold text-xs p-1.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" onclick="this.select()">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[9px] font-bold text-slate-500 mb-1 text-center">الكمية/الوزن</label>
                                <input type="number" step="any" min="0.001" wire:change="updateQuantity('{{ $key }}', $event.target.value)" value="{{ $item['quantity'] }}" class="w-full text-center font-black text-sm p-1.5 border border-emerald-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-emerald-700 bg-emerald-50" onclick="this.select()">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[9px] font-black text-blue-600 mb-1 text-center">الإجمالي (بـ)</label>
                                <input type="number" step="1" min="0" wire:change="updateSubtotal('{{ $key }}', $event.target.value)" value="{{ round($item['unit_price'] * $item['quantity'], 0) }}" class="w-full text-center font-black text-xs p-1.5 border-2 border-blue-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-blue-700" onclick="this.select()">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-slate-400 py-8">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-3 shadow-inner">
                            <span class="text-4xl opacity-50 grayscale filter">🛒</span>
                        </div>
                        <p class="font-black text-base text-slate-500">الفاتورة فارغة</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">ابدأ بمسح الباركود أو اختيار المنتجات</p>
                    </div>
                @endforelse
            </div>

            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200 shadow-inner shrink-0">
                
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[10px] font-black text-slate-600 uppercase">العميل (للديون)</label>
                    </div>
                    <div class="flex gap-2">
                        <select wire:model="customer_id" class="w-full border border-slate-300 rounded-xl p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-bold bg-white">
                            <option value="">-- 👤 زبون نقدي عام --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="openCustomerModal" class="bg-slate-800 text-white hover:bg-blue-600 px-3 rounded-xl shadow-md transition-colors flex items-center justify-center font-bold text-lg shrink-0" title="إضافة عميل جديد">
                            +
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="relative">
                        <label class="absolute -top-2 left-2 bg-slate-50 px-1 text-[9px] font-black text-emerald-600 z-10">💵 كاش</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_cash" onclick="this.select()" class="w-full border-2 border-emerald-300 rounded-xl p-2 text-base focus:ring-2 focus:ring-emerald-500 outline-none font-black text-center text-emerald-700 bg-white">
                    </div>
                    
                    <div class="relative">
                        <label class="absolute -top-2 left-2 bg-slate-50 px-1 text-[9px] font-black text-indigo-600 z-10">📱 بنكك</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_bankak" onclick="this.select()" class="w-full border-2 border-indigo-300 rounded-xl p-2 text-base focus:ring-2 focus:ring-indigo-500 outline-none font-black text-center text-indigo-700 bg-white">
                    </div>
                </div>

                @if($paid_bankak > 0)
                <div class="mb-3 animate-slide-in">
                    <input type="text" wire:model="transaction_number" class="w-full border-2 border-indigo-200 rounded-xl p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-indigo-50/50 text-indigo-800 font-bold placeholder-indigo-300" placeholder="رقم إشعار التحويل البنكي...">
                </div>
                @endif

                <div class="bg-white rounded-xl p-3 border border-slate-100 shadow-sm mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-slate-500">الإجمالي:</span>
                        <span class="text-2xl font-black text-slate-800 tracking-tight" dir="ltr">{{ number_format($total, 0) }}</span>
                    </div>
                    
                    @php
                        $remaining = $total - ((float)$paid_cash + (float)$paid_bankak);
                    @endphp
                    
                    <div class="flex justify-between items-center pt-1 border-t border-slate-100 border-dashed">
                        <span class="text-[10px] font-bold {{ $remaining > 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                            {{ $remaining > 0 ? 'المتبقي (آجل):' : 'الباقي للعميل:' }}
                        </span>
                        <span class="text-lg font-black {{ $remaining > 0 ? 'text-rose-600' : 'text-emerald-600' }}" dir="ltr">
                            {{ number_format(abs($remaining), 0) }}
                        </span>
                    </div>
                </div>

                <button wire:click="checkout" class="relative w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent text-lg font-black rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg transition-all transform hover:-translate-y-0.5 disabled:opacity-50" @if(empty($cart)) disabled @endif>
                    <span>تأكيد الدفع</span>
                    <span class="text-xl">💰</span>
                    <span wire:loading wire:target="checkout" class="absolute left-4 animate-spin">⏳</span>
                </button>
            </div>
        </div>

        <div class="col-span-1 lg:col-span-8 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-5 flex flex-col h-full overflow-hidden">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 shrink-0">
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <span class="text-2xl opacity-50 group-focus-within:opacity-100 transition-opacity">📸</span>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="barcode" autofocus class="w-full border-2 border-blue-200 rounded-2xl p-4 pr-12 text-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-black bg-blue-50/30 text-blue-900 placeholder-blue-300 transition-all shadow-inner" placeholder="امسح الباركود هنا (جاهز)...">
                </div>

                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <span class="text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-2 border-slate-200 rounded-2xl p-4 pr-12 text-lg focus:outline-none focus:ring-4 focus:ring-slate-100 focus:border-slate-400 font-bold placeholder-slate-400 text-slate-700 bg-slate-50 transition-all" placeholder="أو ابحث عن منتج بالاسم...">
                </div>
            </div>

            <div class="flex gap-3 overflow-x-auto mb-6 pb-2 custom-scrollbar shrink-0 px-1">
                <button wire:click="selectCategory(null)" class="whitespace-nowrap px-6 py-2.5 rounded-xl font-black text-sm shadow-sm transition-all duration-200 transform hover:scale-105 {{ $selected_category === null ? 'bg-slate-800 text-white ring-2 ring-slate-800 ring-offset-2' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    🌟 عرض الكل
                </button>
                @foreach($categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})" class="whitespace-nowrap px-6 py-2.5 rounded-xl font-black text-sm shadow-sm transition-all duration-200 transform hover:scale-105 {{ $selected_category == $category->id ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2' : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar p-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-4 align-content-start pb-10">
                    @forelse($quickItems as $item)
                        <button wire:click="addToCart({{ $item['product_id'] }}, {{ $item['unit_id'] }})" class="relative bg-white border-2 hover:border-blue-400 rounded-2xl p-4 shadow-sm hover:shadow-[0_8px_20px_rgba(37,99,235,0.15)] transition-all duration-200 flex flex-col items-center justify-center text-center group transform hover:-translate-y-1 {{ $item['is_wholesale'] ? 'border-purple-200 bg-purple-50/30' : 'border-slate-100' }}">
                            
                            @if($item['is_wholesale'])
                                <span class="absolute top-0 right-0 bg-gradient-to-l from-purple-600 to-fuchsia-500 text-white text-[10px] px-3 py-1 rounded-bl-xl rounded-tr-xl font-black shadow-sm">جملة</span>
                            @endif

                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-3 transition-colors shadow-inner {{ $item['is_wholesale'] ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-600 group-hover:bg-blue-100 group-hover:text-blue-600' }}">
                                <span class="text-3xl filter drop-shadow-sm">{{ $item['is_wholesale'] ? '📦' : '🛍️' }}</span>
                            </div>
                            
                            <h4 class="text-sm font-black text-slate-800 leading-tight mb-2 line-clamp-2">{{ $item['name'] }}</h4>
                            
                            <span class="text-[11px] font-bold text-slate-500 mb-2 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-lg">{{ $item['unit_name'] }}</span>
                            
                            <span class="text-lg font-black {{ $item['is_wholesale'] ? 'text-purple-700' : 'text-emerald-600' }} mt-auto tracking-tight" dir="ltr">
                                {{ number_format($item['price'], 0) }}
                            </span>
                        </button>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                                <span class="text-5xl opacity-50 grayscale filter">📭</span>
                            </div>
                            <p class="font-black text-xl text-slate-500 mb-1">لا توجد منتجات</p>
                            <p class="text-sm font-bold">حاول تغيير الفئة أو مصطلحات البحث.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    @if($isCustomerModalOpen)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 transition-opacity">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-sm overflow-hidden transform transition-all animate-fade-in-up border border-slate-200">
            
            <div class="bg-slate-900 p-5 flex justify-between items-center text-white border-b border-slate-800">
                <h3 class="text-lg font-black flex items-center gap-2">
                    <span class="text-blue-400">👤</span> إضافة عميل سريع
                </h3>
                <button wire:click="closeCustomerModal" class="text-slate-400 hover:text-white bg-slate-800 hover:bg-rose-500 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50">
                <form wire:submit.prevent="saveNewCustomer" class="space-y-5">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">اسم العميل (إلزامي)</label>
                        <input type="text" wire:model="newCustomerName" autofocus class="w-full border-2 border-slate-200 bg-white p-3.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-800 shadow-sm transition-all" placeholder="مثال: أحمد محمد">
                        @error('newCustomerName') <span class="text-rose-500 text-xs font-bold block mt-2 bg-rose-50 px-2 py-1 rounded">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="newCustomerPhone" dir="ltr" class="w-full border-2 border-slate-200 bg-white p-3.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-left tracking-widest text-slate-800 shadow-sm transition-all" placeholder="0123456789">
                        @error('newCustomerPhone') <span class="text-rose-500 text-xs font-bold block mt-2 bg-rose-50 px-2 py-1 rounded">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-xl shadow-[0_8px_20px_rgba(5,150,105,0.25)] transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2 text-lg">
                            <span>حفظ واختيار العميل</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
/* تجميل شريط التمرير (Scrollbar) بأسلوب Mac OS */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* حركة الظهور الناعمة */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px) translateX(-50%); }
    to { opacity: 1; transform: translateY(0) translateX(-50%); }
}
.animate-fade-in-down {
    animation: fadeInUp 0.4s ease-out forwards;
}
</style>