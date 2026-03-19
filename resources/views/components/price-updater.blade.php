<div class="p-6 max-w-5xl mx-auto min-h-screen">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تحديث الأسعار السريع (الديناميكي)</h1>
        <p class="text-gray-600 mt-2">اختر المنتج، وسيقوم النظام بجلب كل وحداته وتسعيراته لتحديثها بضغطة واحدة.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md text-center font-bold text-xl animate-fade-in-up">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-blue-500">
        
        <div class="mb-8 bg-blue-50 p-4 rounded-lg border border-blue-100">
            <label class="block text-sm font-bold text-blue-800 mb-2">اختر المنتج المراد تحديث أسعاره</label>
            <select wire:model.live="selected_product" class="w-full border-2 border-blue-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-xl font-bold bg-white">
                <option value="">-- ابحث أو اختر المنتج --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        @if($selected_product)
        
        <div class="bg-gray-50 border rounded-lg p-6 mb-8 relative">
            <h2 class="text-lg font-black text-gray-700 border-b pb-2 mb-4">1. التكلفة وسعر بيع القطاعي</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-600 mb-1">أدخل التكلفة والقطاعي بناءً على وحدة:</label>
                <select wire:model.live="pricing_unit_id" class="w-1/2 border-2 border-gray-300 p-2 rounded-lg outline-none font-bold">
                    @foreach($available_units as $unit)
                        <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-red-500 font-bold ml-2">سيقوم النظام بقسمتها على ({{ (float)$conversion_rate }}) لحساب سعر الحبة/الكيلو.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">التكلفة (لـ {{ $pricing_unit_name }})</label>
                    <input type="number" step="any" wire:model="cost_price" onclick="this.select()" class="w-full border-2 border-gray-300 p-3 rounded focus:border-blue-500 outline-none text-xl font-bold text-center">
                </div>
                <div>
                    <label class="block text-sm font-bold text-green-800 mb-1">سعر البيع قطاعي (لـ {{ $pricing_unit_name }})</label>
                    <input type="number" step="any" wire:model="retail_price" onclick="this.select()" class="w-full border-2 border-green-400 p-3 rounded focus:border-green-600 outline-none text-xl font-bold text-center text-green-700 bg-green-50 shadow-sm">
                </div>
            </div>
        </div>

        @if(count($wholesale_records) > 0)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-8 relative">
            <div class="absolute top-0 right-0 bg-purple-500 text-white text-xs px-3 py-1 rounded-bl-lg font-bold">تسعيرات الجملة المربوطة</div>
            <h2 class="text-lg font-black text-purple-800 border-b border-purple-200 pb-2 mb-4">2. أسعار بيع الجملة</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($wholesale_records as $record)
                <div class="bg-white p-4 rounded-lg border shadow-sm">
                    <label class="block text-sm font-bold text-purple-700 mb-2">
                        سعر جملة ({{ $record->unit->name }})
                    </label>
                    <input type="number" step="any" wire:model="wholesale_prices.{{ $record->id }}" onclick="this.select()" class="w-full border-2 border-purple-300 p-2 rounded focus:border-purple-600 outline-none text-lg font-bold text-center text-purple-800" placeholder="أدخل السعر الخاص">
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="flex items-center justify-center p-4 mb-8 border-2 border-dashed border-gray-200 rounded-lg text-gray-400 text-sm font-bold text-center">
            هذا المنتج يباع قطاعي فقط<br>(لا يوجد له وحدات جملة مسجلة)
        </div>
        @endif

        <button wire:click="updatePrice" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg transition-transform transform hover:scale-[1.01] text-2xl flex justify-center items-center gap-2">
            <span>💾</span> اعتماد وتحديث كل الأسعار
        </button>
        
        @endif

    </div>
</div>