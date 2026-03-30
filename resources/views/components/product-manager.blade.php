<div class="p-6 max-w-7xl mx-auto min-h-screen relative">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إعدادات المنتجات والفئات والوحدات</h1>
    </div>

    <livewire:product-importer />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-blue-500">
            <h2 class="text-xl font-bold text-blue-700 border-b pb-2 mb-4">1. تعريف فئة جديدة</h2>
            @if(session('category_success')) <div class="text-green-600 font-bold mb-2 text-sm">{{ session('category_success') }}</div> @endif
            @if(session('category_error')) <div class="text-red-600 font-bold mb-2 text-sm">{{ session('category_error') }}</div> @endif
            
            <form wire:submit.prevent="addCategory" class="mb-6">
                <div class="flex gap-2">
                    <input type="text" wire:model="category_name" class="w-full border p-2 rounded outline-none focus:border-blue-500" placeholder="مثال: معلبات، مشروبات...">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 rounded shadow transition-colors">إضافة</button>
                </div>
                @error('category_name') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
            </form>

            <h2 class="text-xl font-bold text-blue-700 border-b pb-2 mb-4">2. تعريف وحدة جديدة</h2>
            @if(session('unit_success')) <div class="text-green-600 font-bold mb-2 text-sm">{{ session('unit_success') }}</div> @endif
            @if(session('unit_error')) <div class="text-red-600 font-bold mb-2 text-sm">{{ session('unit_error') }}</div> @endif
            
            <form wire:submit.prevent="addUnit">
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700">اسم الوحدة</label>
                    <input type="text" wire:model="unit_name" class="w-full border p-2 rounded mt-1 outline-none focus:border-blue-500">
                    @error('unit_name') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">النوع</label>
                        <select wire:model="unit_type" class="w-full border p-2 rounded mt-1 outline-none text-sm bg-gray-50">
                            <option value="quantity">كمية</option>
                            <option value="weight">وزن</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">التحويل</label>
                        <input type="number" step="any" wire:model="conversion_rate" class="w-full border p-2 rounded mt-1 outline-none text-sm focus:border-blue-500" placeholder="مثال: 12">
                        @error('conversion_rate') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow transition-colors">إضافة الوحدة</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-green-500">
            <h2 class="text-xl font-bold text-green-700 border-b pb-2 mb-4">3. تعريف المنتج (أساسي)</h2>
            @if(session('product_success')) <div class="text-green-600 font-bold mb-2 text-sm">{{ session('product_success') }}</div> @endif
            
            <form wire:submit.prevent="addProduct">
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700">اسم المنتج</label>
                    <input type="text" wire:model="product_name" class="w-full border p-2 rounded mt-1 outline-none focus:border-green-500">
                    @error('product_name') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">الباركود (SKU)</label>
                        <input type="text" wire:model="sku" class="w-full border p-2 rounded mt-1 outline-none focus:border-green-500" dir="ltr" placeholder="اختياري">
                        @error('sku') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">الفئة (للكاشير)</label>
                        
                        <div x-data="{
                            open: false, search: '', selectedId: @entangle('category_id'), options: [],
                            init() { this.options = Array.from(this.$refs.hiddenCats.options).map(o => ({ id: o.value, name: o.text })); },
                            get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                            get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- عام --'; }
                        }" class="relative w-full" @click.away="open = false">
                            <select x-ref="hiddenCats" class="hidden">
                                <option value="">-- عام --</option>
                                @foreach($categoriesList as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                            </select>
                            
                            <div @click="open = !open" class="w-full border p-2 rounded mt-1 bg-white cursor-pointer flex justify-between items-center focus:border-green-500">
                                <span x-text="selectedName" class="text-gray-700 truncate text-sm"></span>
                                <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                            </div>
                            <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-xl max-h-60 flex flex-col overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b">
                                    <input x-model="search" type="text" class="w-full border p-1.5 text-sm rounded outline-none focus:border-green-500" placeholder="🔍 بحث...">
                                </div>
                                <ul class="overflow-y-auto flex-1">
                                    <template x-for="item in filtered" :key="item.id || 'empty'">
                                        <li @click="selectedId = item.id; $wire.set('category_id', item.id); open = false; search = ''" class="p-2 text-sm hover:bg-green-50 cursor-pointer border-b" :class="{'bg-green-100 font-bold': selectedId == item.id}">
                                            <span x-text="item.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold text-red-600 mb-1">الوحدة الأساسية (مثال: حبة)</label>
                    
                    <div x-data="{
                        open: false, search: '', selectedId: @entangle('base_unit_id'), options: [],
                        init() { this.options = Array.from(this.$refs.hiddenBaseUnits.options).map(o => ({ id: o.value, name: o.text })); },
                        get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                        get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- اختر الوحدة --'; }
                    }" class="relative w-full" @click.away="open = false">
                        <select x-ref="hiddenBaseUnits" class="hidden">
                            <option value="">-- اختر الوحدة --</option>
                            @foreach($baseUnitsList as $unit) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endforeach
                        </select>
                        
                        <div @click="open = !open" class="w-full border border-red-200 bg-red-50 p-2 rounded mt-1 cursor-pointer flex justify-between items-center">
                            <span x-text="selectedName" class="text-gray-700 truncate font-bold text-sm"></span>
                            <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                        </div>
                        <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-red-200 rounded-md shadow-xl max-h-60 flex flex-col overflow-hidden">
                            <div class="p-2 bg-red-50 border-b">
                                <input x-model="search" type="text" class="w-full border p-1.5 text-sm rounded outline-none focus:border-red-500" placeholder="🔍 بحث عن وحدة...">
                            </div>
                            <ul class="overflow-y-auto flex-1">
                                <template x-for="item in filtered" :key="item.id || 'empty'">
                                    <li @click="selectedId = item.id; $wire.set('base_unit_id', item.id); open = false; search = ''" class="p-2 text-sm hover:bg-red-50 cursor-pointer border-b" :class="{'bg-red-100 font-bold text-red-700': selectedId == item.id}">
                                        <span x-text="item.name"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @error('base_unit_id') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror

                </div>

                <div class="grid grid-cols-2 gap-2 mb-3 border-t border-b py-3 my-3">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">تكلفة <span class="text-green-600 underline">القطعة</span></label>
                        <input type="number" step="any" wire:model="cost_price" class="w-full border p-2 rounded mt-1 focus:border-green-500 outline-none bg-gray-50">
                        @error('cost_price') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">سعر بيع <span class="text-green-600 underline">القطعة</span></label>
                        <input type="number" step="any" wire:model="selling_price" class="w-full border-2 border-green-300 p-2 rounded mt-1 bg-green-50 focus:border-green-500 outline-none">
                        @error('selling_price') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 bg-gray-50 p-3 rounded border">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">مخزون بوحدة:</label>
                        
                        <div x-data="{
                            open: false, search: '', selectedId: @entangle('stock_unit_id'), options: [],
                            init() { this.options = Array.from(this.$refs.hiddenStockUnits.options).map(o => ({ id: o.value, name: o.text })); },
                            get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                            get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- الوحدة --'; }
                        }" class="relative w-full" @click.away="open = false">
                            <select x-ref="hiddenStockUnits" class="hidden">
                                <option value="">-- الوحدة --</option>
                                @foreach($allUnitsList as $unit) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endforeach
                            </select>
                            
                            <div @click="open = !open" class="w-full border p-2 rounded bg-white cursor-pointer flex justify-between items-center focus:border-green-500">
                                <span x-text="selectedName" class="text-gray-700 truncate text-xs"></span>
                                <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                            </div>
                            <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-xl max-h-60 flex flex-col overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b">
                                    <input x-model="search" type="text" class="w-full border p-1.5 text-sm rounded outline-none focus:border-green-500" placeholder="🔍 بحث...">
                                </div>
                                <ul class="overflow-y-auto flex-1">
                                    <template x-for="item in filtered" :key="item.id || 'empty'">
                                        <li @click="selectedId = item.id; $wire.set('stock_unit_id', item.id); open = false; search = ''" class="p-2 text-sm hover:bg-green-50 cursor-pointer border-b" :class="{'bg-green-100 font-bold': selectedId == item.id}">
                                            <span x-text="item.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        @error('stock_unit_id') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الكمية الموجودة:</label>
                        <input type="number" step="any" wire:model="initial_stock" class="w-full border p-2 rounded outline-none focus:border-green-500 text-center font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-orange-600 mb-1">ينتهي بتاريخ:</label>
                        <input type="date" wire:model="expiry_date" class="w-full border border-orange-300 bg-orange-50 p-2 rounded outline-none focus:border-orange-500 text-center font-bold text-orange-800">
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transition-colors text-lg">💾 حفظ المنتج (قطاعي)</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-purple-500 relative">
            <h2 class="text-xl font-bold text-purple-700 border-b pb-2 mb-4">4. تسعير الجملة للوحدات الكبرى</h2>
            @if(session('barcode_success')) <div class="text-green-600 font-bold mb-2">{{ session('barcode_success') }}</div> @endif
            
            <form wire:submit.prevent="addBarcode">
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">اختر المنتج لتسعير الجملة له</label>
                    
                    <div x-data="{
                        open: false, search: '', selectedId: @entangle('selected_product'), options: [],
                        init() { this.options = Array.from(this.$refs.hiddenProducts.options).map(o => ({ id: o.value, name: o.text })); },
                        get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                        get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- اختر المنتج --'; }
                    }" class="relative w-full" @click.away="open = false">
                        <select x-ref="hiddenProducts" class="hidden">
                            <option value="">-- اختر المنتج --</option>
                            @foreach($allProductsSimple as $product) <option value="{{ $product->id }}">{{ $product->name }}</option> @endforeach
                        </select>
                        
                        <div @click="open = !open" class="w-full border-2 border-purple-200 p-2 rounded mt-1 bg-white cursor-pointer flex justify-between items-center focus:border-purple-500">
                            <span x-text="selectedName" class="text-gray-700 truncate font-bold text-sm"></span>
                            <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                        </div>
                        <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-purple-200 rounded-lg shadow-2xl max-h-60 flex flex-col overflow-hidden">
                            <div class="p-2 bg-purple-50 border-b border-purple-100">
                                <input x-model="search" type="text" class="w-full border border-purple-300 p-2 text-sm rounded outline-none focus:border-purple-500" placeholder="🔍 ابحث عن اسم المنتج...">
                            </div>
                            <ul class="overflow-y-auto flex-1">
                                <template x-for="item in filtered" :key="item.id || 'empty'">
                                    <li @click="selectedId = item.id; $wire.set('selected_product', item.id); open = false; search = ''" class="p-3 text-sm hover:bg-purple-50 cursor-pointer border-b" :class="{'bg-purple-100 font-bold text-purple-700': selectedId == item.id}">
                                        <span x-text="item.name"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @error('selected_product') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror

                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">الوحدة المراد بيعها كجملة</label>
                    
                    <div x-data="{
                        open: false, search: '', selectedId: @entangle('selected_unit'), options: [],
                        init() { this.options = Array.from(this.$refs.hiddenWholesaleUnits.options).map(o => ({ id: o.value, name: o.text })); },
                        get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                        get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- مثال: كرتونة --'; }
                    }" class="relative w-full" @click.away="open = false">
                        <select x-ref="hiddenWholesaleUnits" class="hidden">
                            <option value="">-- مثال: كرتونة --</option>
                            @foreach($wholesaleUnitsList as $unit) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endforeach
                        </select>
                        
                        <div @click="open = !open" class="w-full border p-2 rounded mt-1 bg-white cursor-pointer flex justify-between items-center focus:border-purple-500">
                            <span x-text="selectedName" class="text-gray-700 truncate text-sm"></span>
                            <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                        </div>
                        <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-xl max-h-60 flex flex-col overflow-hidden">
                            <div class="p-2 bg-gray-50 border-b">
                                <input x-model="search" type="text" class="w-full border p-1.5 text-sm rounded outline-none focus:border-purple-500" placeholder="🔍 بحث...">
                            </div>
                            <ul class="overflow-y-auto flex-1">
                                <template x-for="item in filtered" :key="item.id || 'empty'">
                                    <li @click="selectedId = item.id; $wire.set('selected_unit', item.id); open = false; search = ''" class="p-2 text-sm hover:bg-purple-50 cursor-pointer border-b" :class="{'bg-purple-100 font-bold text-purple-700': selectedId == item.id}">
                                        <span x-text="item.name"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @error('selected_unit') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror

                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-bold text-purple-700">سعر الجملة (الخاص)</label>
                    <input type="number" step="any" wire:model="specific_selling_price" class="w-full border-2 border-purple-300 bg-purple-50 p-2 rounded mt-1 outline-none focus:ring-2 focus:ring-purple-500" placeholder="أدخل السعر المخفض للكرتونة">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">الباركود لهذه الوحدة (اختياري)</label>
                    <input type="text" wire:model="barcode" class="w-full border p-2 rounded mt-1 outline-none focus:border-purple-500" dir="ltr" placeholder="اتركه فارغاً للتوليد التلقائي">
                    @error('barcode') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded shadow transition-colors">حفظ تسعيرة الجملة</button>
            </form>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-teal-600 text-white p-3 font-bold flex justify-between">
                    <span>الفئات المعرفة</span>
                    <span class="text-sm bg-teal-800 px-2 rounded">{{ count($categoriesList) }} فئة</span>
                </div>
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2 border-b">اسم الفئة</th><th class="p-2 border-b text-center">حذف</th></tr>
                    </thead>
                    <tbody>
                        @foreach($categoriesList as $cat)
                        <tr wire:key="cat-{{ $cat->id }}" class="hover:bg-gray-50">
                            <td class="p-2 border-b font-bold text-gray-700">{{ $cat->name }}</td>
                            <td class="p-2 border-b text-center">
                                <button type="button" wire:click.prevent="deleteCategory({{ $cat->id }})" onclick="confirm('متأكد من مسح الفئة؟') || event.stopImmediatePropagation()" class="text-red-500 hover:text-red-700">✖</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-blue-600 text-white p-3 font-bold flex justify-between">
                    <span>الوحدات المعرفة</span>
                    <span class="text-sm bg-blue-800 px-2 rounded">{{ count($allUnitsList) }} وحدة</span>
                </div>
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2 border-b">اسم الوحدة</th><th class="p-2 border-b text-center">حذف</th></tr>
                    </thead>
                    <tbody>
                        @foreach($allUnitsList as $unit)
                        <tr wire:key="unit-{{ $unit->id }}" class="hover:bg-gray-50">
                            <td class="p-2 border-b font-bold">{{ $unit->name }} <span class="text-xs text-gray-500">({{ $unit->conversion_rate }})</span></td>
                            <td class="p-2 border-b text-center">
                                <button type="button" wire:click.prevent="deleteUnit({{ $unit->id }})" onclick="confirm('متأكد من الحذف؟') || event.stopImmediatePropagation()" class="text-red-500 hover:text-red-700">✖</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden lg:col-span-2">
            <div class="bg-gray-800 text-white p-4 font-bold flex flex-col md:flex-row justify-between items-center gap-3">
                <span class="text-lg">📦 الإدارة الشاملة للمنتجات</span>
                <div class="relative w-full md:w-1/2">
                    <input type="text" wire:model.live.debounce.300ms="search_product" class="w-full text-gray-800 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="🔍 ابحث عن منتج بالاسم أو الباركود...">
                </div>
            </div>
            
            @if(session('product_error')) <div class="bg-red-100 text-red-700 p-3 font-bold text-center border-b border-red-200">{{ session('product_error') }}</div> @endif

            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 border-b">المنتج (الاسم / الباركود)</th>
                            <th class="p-3 border-b text-center">الفئة</th>
                            <th class="p-3 border-b text-center">التكلفة</th>
                            <th class="p-3 border-b text-center text-green-700">البيع</th>
                            <th class="p-3 border-b text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productsList as $product)
                        <tr wire:key="prod-{{ $product->id }}" class="hover:bg-gray-50 border-b border-gray-100 transition-colors {{ !$product->is_active ? 'bg-red-50 opacity-70' : '' }}">
                            <td class="p-3">
                                <div class="font-black text-gray-800 text-base">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 font-mono mt-1 flex items-center gap-1">
                                    <span>🔢</span> {{ $product->sku }}
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded-full shadow-sm">{{ $product->category->name ?? '---' }}</span>
                            </td>
                            <td class="p-3 text-center font-bold text-gray-600">{{ number_format($product->current_cost_price, 0) }}</td>
                            <td class="p-3 text-center font-black text-green-600 text-lg">{{ number_format($product->current_selling_price, 0) }}</td>
                            <td class="p-3 text-center flex justify-center gap-2 items-center h-full mt-2">
                                <button type="button" wire:click.prevent="editProduct({{ $product->id }})" class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-500 hover:text-white text-yellow-700 rounded shadow-sm text-xs font-bold transition-colors" title="تعديل">✏️</button>
                                <button type="button" wire:click.prevent="toggleProductStatus({{ $product->id }})" class="px-3 py-1.5 rounded shadow-sm text-xs font-bold transition-colors {{ $product->is_active ? 'bg-green-100 text-green-700 hover:bg-green-500 hover:text-white' : 'bg-red-200 text-red-800 hover:bg-red-500 hover:text-white' }}" title="{{ $product->is_active ? 'إيقاف البيع' : 'تفعيل البيع' }}">
                                    {{ $product->is_active ? '🟢' : '🔴' }}
                                </button>
                                <button type="button" wire:click.prevent="deleteProduct({{ $product->id }})" onclick="confirm('هل أنت متأكد من حذف المنتج نهائياً؟') || event.stopImmediatePropagation()" class="px-3 py-1.5 bg-red-100 hover:bg-red-600 hover:text-white text-red-700 rounded shadow-sm text-xs font-bold transition-colors" title="حذف">🗑️</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-500 font-bold text-lg">لا توجد منتجات مطابقة للبحث.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-gray-50 border-t">
                {{ $productsList->links() }}
            </div>
        </div>

    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <div class="bg-purple-800 text-white p-4 font-bold flex flex-col md:flex-row justify-between items-center gap-3">
            <span class="text-lg">📦 الإدارة الشاملة لمنتجات الجملة</span>
            <div class="relative w-full md:w-1/3">
                <input type="text" wire:model.live.debounce.300ms="search_wholesale" class="w-full text-gray-800 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="🔍 ابحث عن منتج جملة بالاسم أو الباركود...">
            </div>
        </div>

        <div class="overflow-x-auto min-h-[300px]">
            <table class="w-full text-right text-sm">
                <thead class="bg-purple-50 text-purple-900 border-b border-purple-200">
                    <tr>
                        <th class="p-3 border-b">منتج الجملة (الاسم والوحدة)</th>
                        <th class="p-3 border-b text-center">الباركود</th>
                        <th class="p-3 border-b text-center">سعر الجملة</th>
                        <th class="p-3 border-b text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productUnitsList as $pu)
                    <tr wire:key="pu-{{ $pu->id }}" class="hover:bg-purple-50 border-b border-gray-100 transition-colors">
                        <td class="p-3">
                            <div class="font-black text-gray-800">{{ $pu->product->name ?? '---' }}</div>
                            <div class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full inline-block mt-1">
                                وحدة الجملة: {{ $pu->unit->name ?? '---' }}
                            </div>
                        </td>
                        <td class="p-3 text-center font-mono text-gray-600 font-bold">
                            {{ $pu->barcode }}
                        </td>
                        <td class="p-3 text-center font-black text-lg text-purple-700">
                            {{ $pu->specific_selling_price ? number_format($pu->specific_selling_price, 0) : 'تلقائي (حسب القطاعي)' }}
                        </td>
                        <td class="p-3 text-center flex justify-center gap-2 items-center h-full mt-2">
                            <button type="button" wire:click.prevent="editWholesale({{ $pu->id }})" class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-500 hover:text-white text-yellow-700 rounded shadow-sm text-xs font-bold transition-colors" title="تعديل السعر والباركود">✏️</button>
                            <button type="button" wire:click.prevent="deleteBarcode({{ $pu->id }})" onclick="confirm('هل أنت متأكد من فك ارتباط هذه الوحدة؟') || event.stopImmediatePropagation()" class="px-3 py-1.5 bg-red-100 hover:bg-red-600 hover:text-white text-red-700 rounded shadow-sm text-xs font-bold transition-colors" title="حذف تسعيرة الجملة">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-500 font-bold text-lg">لا توجد منتجات جملة مطابقة للبحث.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50 border-t">
            {{ $productUnitsList->links() }}
        </div>
    </div>


    @if($isEditModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 relative animate-fade-in-up border-t-4 border-blue-600">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">✏️ تعديل بيانات المنتج</h3>
            <form wire:submit.prevent="updateProduct">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">اسم المنتج</label>
                    <input type="text" wire:model="edit_product_name" class="w-full border-2 border-gray-200 p-2 rounded mt-1 outline-none focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">الفئة</label>
                        
                        <div x-data="{
                            open: false, search: '', selectedId: @entangle('edit_category_id'), options: [],
                            init() { this.options = Array.from(this.$refs.hiddenEditCats.options).map(o => ({ id: o.value, name: o.text })); },
                            get filtered() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
                            get selectedName() { let item = this.options.find(i => i.id == this.selectedId); return item ? item.name : '-- بدون فئة --'; }
                        }" class="relative w-full" @click.away="open = false">
                            <select x-ref="hiddenEditCats" class="hidden">
                                <option value="">-- بدون فئة --</option>
                                @foreach($categoriesList as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                            </select>
                            
                            <div @click="open = !open" class="w-full border-2 border-gray-200 p-2 rounded mt-1 bg-white cursor-pointer flex justify-between items-center focus:border-blue-500">
                                <span x-text="selectedName" class="text-gray-700 truncate text-sm"></span>
                                <span class="text-gray-400 text-xs transition-transform" :class="{'rotate-180': open}">▼</span>
                            </div>
                            <div x-show="open" style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-blue-200 rounded-md shadow-xl max-h-48 flex flex-col overflow-hidden">
                                <div class="p-2 bg-blue-50 border-b border-blue-100">
                                    <input x-model="search" type="text" class="w-full border border-gray-300 p-1.5 text-sm rounded outline-none focus:border-blue-500" placeholder="🔍 بحث...">
                                </div>
                                <ul class="overflow-y-auto flex-1">
                                    <template x-for="item in filtered" :key="item.id || 'empty'">
                                        <li @click="selectedId = item.id; $wire.set('edit_category_id', item.id); open = false; search = ''" class="p-2 text-sm hover:bg-blue-50 cursor-pointer border-b border-gray-50" :class="{'bg-blue-100 font-bold text-blue-700': selectedId == item.id}">
                                            <span x-text="item.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">الباركود (SKU)</label>
                        <input type="text" wire:model="edit_sku" class="w-full border-2 border-gray-200 p-2 rounded mt-1 outline-none focus:border-blue-500" dir="ltr">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">تكلفة الوحدة</label>
                        <input type="number" step="any" wire:model="edit_cost_price" class="w-full border-2 border-gray-200 p-2 rounded mt-1 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">سعر بيع الوحدة</label>
                        <input type="number" step="any" wire:model="edit_selling_price" class="w-full border-2 border-green-300 bg-green-50 p-2 rounded mt-1 outline-none focus:border-green-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" wire:click.prevent="closeModal" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-bold hover:bg-gray-300 transition-colors">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-colors shadow">💾 حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($isEditWholesaleModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 relative animate-fade-in-up border-t-4 border-purple-600">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4 flex justify-between">
                <span>📦 تعديل بيانات الجملة</span>
                <span class="text-purple-600 text-sm bg-purple-100 px-2 py-1 rounded">{{ $edit_wholesale_product_name }}</span>
            </h3>
            
            <form wire:submit.prevent="updateWholesale">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">الباركود (SKU) الخاص بالكرتونة/الجملة</label>
                    <input type="text" wire:model="edit_wholesale_barcode" class="w-full border-2 border-purple-200 p-2 rounded mt-1 outline-none focus:border-purple-500" dir="ltr">
                    @error('edit_wholesale_barcode') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-purple-700">سعر البيع (جملة)</label>
                    <input type="number" step="any" wire:model="edit_wholesale_price" class="w-full border-2 border-purple-300 bg-purple-50 p-3 text-lg font-black text-center rounded mt-1 outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200" placeholder="اتركه فارغاً ليعتمد على سعر القطاعي ضرب الكمية">
                    <p class="text-xs text-gray-500 mt-1 font-bold">إذا تركته فارغاً، سيقوم النظام بحسابه تلقائياً بناءً على سعر القطاعي ومعامل التحويل.</p>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" wire:click.prevent="closeWholesaleModal" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-bold hover:bg-gray-300 transition-colors">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition-colors shadow">💾 حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>