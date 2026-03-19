<div class="p-6 bg-gray-100 min-h-screen">
    
    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 shadow">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4 shadow">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-12 gap-6">
        
        <div class="col-span-12 md:col-span-4 bg-white rounded-lg shadow p-4 flex flex-col justify-between h-[85vh]">
            <div>
                <h2 class="text-xl font-bold mb-4 border-b pb-2">الفاتورة الحالية</h2>
                
                <div class="overflow-y-auto max-h-[50vh] pr-1 custom-scrollbar">
                    @forelse ($cart as $key => $item)
                        <div wire:key="cart-item-{{ $key }}-{{ $item['quantity'] }}" class="bg-white border border-gray-200 shadow-sm rounded-lg p-3 mb-3 hover:border-blue-300 transition-colors">
                            
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-black text-gray-800 text-sm leading-tight">{{ $item['name'] }}</h4>
                                    <span class="inline-block mt-1 bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                        الوحدة: {{ $item['unit_name'] }}
                                    </span>
                                </div>
                                <button wire:click="removeFromCart('{{ $key }}')" class="text-red-500 bg-red-50 hover:bg-red-500 hover:text-white p-1.5 rounded-md transition-colors" title="حذف الصنف">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 items-end bg-gray-50 p-2 rounded border border-gray-100">
                                
                                <div class="flex flex-col">
                                    <label class="text-[10px] font-bold text-gray-500 mb-1 text-center">سعر الوحدة</label>
                                    <input type="number" step="any" min="0"
                                        wire:change="updatePrice('{{ $key }}', $event.target.value)"
                                        value="{{ (float) $item['unit_price'] }}"
                                        class="w-full text-center font-bold text-sm p-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-700 bg-white"
                                        onclick="this.select()" title="تعديل سعر الوحدة">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-[10px] font-bold text-gray-500 mb-1 text-center">الكمية/الوزن</label>
                                    <input type="number" step="any" min="0.001"
                                        wire:change="updateQuantity('{{ $key }}', $event.target.value)"
                                        value="{{ $item['quantity'] }}"
                                        class="w-full text-center font-black text-sm p-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-green-700 bg-white shadow-inner"
                                        onclick="this.select()" title="تعديل الكمية">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-[10px] font-black text-blue-600 mb-1 text-center">الإجمالي (بـ)</label>
                                    <input type="number" step="1" min="0"
                                        wire:change="updateSubtotal('{{ $key }}', $event.target.value)"
                                        value="{{ round($item['unit_price'] * $item['quantity'], 0) }}"
                                        class="w-full text-center font-black text-sm p-1.5 border-2 border-blue-300 rounded bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-blue-800 shadow-inner transition-colors"
                                        onclick="this.select()" title="البيع بالقيمة (أدخل المبلغ هنا)">
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <span class="text-4xl mb-3 opacity-50">🛒</span>
                            <p class="font-bold text-sm">السلة فارغة، ابدأ بإضافة المنتجات</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4 bg-gray-50 p-3 rounded shadow-inner">
                
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 font-bold mb-1">العميل (للديون)</label>
                    <div class="flex gap-2">
                        <select wire:model="customer_id" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                            <option value="">-- زبون نقدي عام --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="openCustomerModal" class="bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-2 rounded border border-blue-300 transition-colors" title="إضافة عميل جديد بسرعة">
                            ➕
                        </button>
                    </div>
                    @if (session()->has('success_customer'))
                        <span class="text-green-600 text-[10px] font-bold mt-1 block">{{ session('success_customer') }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3 p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <label class="block text-[11px] text-gray-500 font-bold mb-1">💵 المدفوع (كاش)</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_cash" onclick="this.select()" class="w-full border-2 border-green-300 rounded p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none font-black text-center text-green-700 bg-green-50">
                    </div>
                    
                    <div>
                        <label class="block text-[11px] text-gray-500 font-bold mb-1">📱 المدفوع (بنكك)</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_bankak" onclick="this.select()" class="w-full border-2 border-blue-300 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-black text-center text-blue-700 bg-blue-50">
                    </div>
                </div>

                @if($paid_bankak > 0)
                <div class="mb-3 animate-fade-in-up">
                    <input type="text" wire:model="transaction_number" class="w-full border border-blue-400 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50" placeholder="أدخل رقم إشعار بنكك هنا...">
                </div>
                @endif

                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-bold text-gray-600">الإجمالي:</span>
                    <span class="text-2xl font-black text-gray-800">{{ number_format($total, 0) }}</span>
                </div>
                
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200">
                    <span class="text-sm font-bold text-red-500">المتبقي (الآجل):</span>
                    <span class="text-lg font-black text-red-600" dir="ltr">
                        {{ number_format($total - ((float)$paid_cash + (float)$paid_bankak), 0) }}
                    </span>
                </div>

                <button wire:click="checkout" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-lg shadow-lg transition-transform transform hover:scale-[1.02] flex justify-center items-center gap-2 text-lg" @if(empty($cart)) disabled @endif>
                    <span>دفع وحفظ الفاتورة</span> <span>💰</span>
                </button>
            </div>
        </div>

        <div class="col-span-12 md:col-span-8 bg-white rounded-lg shadow p-4">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">|||||</span>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="barcode" autofocus 
                           class="w-full border-2 border-blue-400 rounded-lg p-3 pr-10 text-lg focus:outline-none focus:ring-2 focus:ring-blue-600 font-bold bg-blue-50" 
                           placeholder="امسح الباركود هنا...">
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">🔍</span>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="w-full border-2 border-gray-300 rounded-lg p-3 pr-10 text-lg focus:outline-none focus:ring-2 focus:ring-blue-600 font-bold placeholder-gray-400" 
                           placeholder="أو ابحث عن منتج بالاسم...">
                </div>

            </div>

            <div class="flex gap-2 overflow-x-auto mb-4 pb-2 custom-scrollbar">
                <button wire:click="selectCategory(null)" class="whitespace-nowrap px-6 py-2 rounded-full font-bold shadow-sm transition-colors {{ $selected_category === null ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    الكل 🌟
                </button>
                @foreach($categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})" class="whitespace-nowrap px-6 py-2 rounded-full font-bold shadow-sm transition-colors {{ $selected_category == $category->id ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 h-[60vh] overflow-y-auto pr-2 custom-scrollbar p-1 align-content-start">
                @forelse($quickItems as $item)
                    <button wire:click="addToCart({{ $item['product_id'] }}, {{ $item['unit_id'] }})" 
                            class="relative bg-white border-2 hover:border-blue-400 rounded-xl p-3 shadow-sm hover:shadow-md transition-all flex flex-col items-center justify-center text-center group {{ $item['is_wholesale'] ? 'border-purple-200 bg-purple-50' : 'border-gray-100' }}">
                        
                        @if($item['is_wholesale'])
                            <span class="absolute top-0 right-0 bg-purple-500 text-white text-[10px] px-2 py-0.5 rounded-bl-lg rounded-tr-lg font-bold">جملة</span>
                        @endif

                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 transition-colors {{ $item['is_wholesale'] ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600 group-hover:bg-blue-100' }}">
                            <span class="text-xl">{{ $item['is_wholesale'] ? '📦' : '🛍️' }}</span>
                        </div>
                        
                        <h4 class="text-sm font-black text-gray-800 leading-tight mb-1">{{ $item['name'] }}</h4>
                        
                        <span class="text-xs font-bold text-gray-500 mb-1 bg-gray-200 px-2 rounded-full">{{ $item['unit_name'] }}</span>
                        
                        <span class="text-sm font-black {{ $item['is_wholesale'] ? 'text-purple-700' : 'text-green-600' }}">
                            {{ number_format($item['price'], 2) }}
                        </span>
                    </button>
                @empty
                    <div class="col-span-full h-full flex flex-col items-center justify-center text-gray-400 mt-10">
                        <span class="text-4xl mb-2">📭</span>
                        <p class="font-bold">لا توجد منتجات في هذه الفئة</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

    @if($isCustomerModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm animate-fade-in-up">
            
            <div class="bg-blue-600 p-4 rounded-t-xl flex justify-between items-center text-white">
                <h3 class="text-lg font-black">➕ إضافة عميل سريع</h3>
                <button wire:click="closeCustomerModal" class="text-blue-200 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <div class="p-5">
                <form wire:submit.prevent="saveNewCustomer">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم العميل (إلزامي)</label>
                        <input type="text" wire:model="newCustomerName" autofocus class="w-full border-2 border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        @error('newCustomerName') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="newCustomerPhone" dir="ltr" class="w-full border-2 border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold text-left">
                        @error('newCustomerPhone') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 shadow transition-colors">
                        💾 حفظ العميل واختياره
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* تجميل شريط التمرير (Scrollbar) */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
</style>