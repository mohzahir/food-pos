<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">جرد المخزون وتقييم الأصول</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-red-500">
            <p class="text-sm text-gray-500 font-bold mb-1">إجمالي رأس المال (بالتكلفة)</p>
            <h3 class="text-2xl font-black text-red-600">{{ number_format($totalInventoryCost, 2) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-green-500">
            <p class="text-sm text-gray-500 font-bold mb-1">القيمة السوقية (بسعر البيع)</p>
            <h3 class="text-2xl font-black text-green-600">{{ number_format($totalExpectedRevenue, 2) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-blue-500">
            <p class="text-sm text-gray-500 font-bold mb-1">الأرباح المتوقعة عند بيع كل المخزون</p>
            <h3 class="text-2xl font-black text-blue-600">{{ number_format($expectedProfit, 2) }}</h3>
        </div>
    </div>

    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-lg" placeholder="🔍 ابحث عن منتج بالاسم أو الباركود...">
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="p-4">المنتج (الباركود)</th>
                    <th class="p-4 text-center">الوحدة الأساسية</th>
                    <th class="p-4 text-center">الرصيد الحالي</th>
                    <th class="p-4 text-center">تكلفة الوحدة</th>
                    <th class="p-4 text-center">سعر البيع</th>
                    <th class="p-4 text-center">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-800">
                        {{ $product->name }} <br>
                        <span class="text-xs text-gray-500 font-normal">SKU: {{ $product->sku }}</span>
                    </td>
                    <td class="p-4 text-center text-gray-600">{{ $product->baseUnit ? $product->baseUnit->name : 'غير محدد' }}</td>
                    
                    <td class="p-4 text-center">
                        <div class="flex flex-col items-center justify-center gap-1">
                            <span class="font-black text-lg text-blue-700" dir="ltr">
                                {{ (float) $product->current_stock }} <span class="text-xs text-gray-500 font-bold">{{ $product->baseUnit ? $product->baseUnit->name : '' }}</span>
                            </span>

                            @if($product->productUnits->count() > 0)
                                @php
                                    // نجلب أكبر وحدة جملة مربوطة بالمنتج (مثلاً الكرتونة)
                                    $biggestUnit = $product->productUnits->sortByDesc('unit.conversion_rate')->first()->unit;
                                    $conversionRate = $biggestUnit->conversion_rate;
                                    $currentStock = $product->current_stock;

                                    if ($conversionRate > 0) {
                                        // كم كرتونة كاملة؟
                                        $wholesaleQuantity = floor($currentStock / $conversionRate);
                                        // كم حبة متبقية (فكة)؟
                                        $retailRemainder = fmod($currentStock, $conversionRate);
                                    } else {
                                        $wholesaleQuantity = 0;
                                        $retailRemainder = $currentStock;
                                    }
                                @endphp

                                @if($wholesaleQuantity > 0 || $retailRemainder > 0)
                                <div class="bg-purple-50 border border-purple-200 px-3 py-1 rounded-lg text-sm font-bold text-purple-800 flex gap-2 items-center">
                                    <span title="كمية الجملة">📦 {{ $wholesaleQuantity }} {{ $biggestUnit->name }}</span>
                                    @if($retailRemainder > 0)
                                        <span class="text-gray-400">|</span>
                                        <span title="الباقي قطاعي" class="text-gray-600">{{ (float)$retailRemainder }} {{ $product->baseUnit ? $product->baseUnit->name : 'حبة' }}</span>
                                    @endif
                                </div>
                                @endif
                            @endif
                        </div>
                    </td>
                    
                    <td class="p-4 text-center text-red-600">{{ number_format($product->current_cost_price, 2) }}</td>
                    <td class="p-4 text-center text-green-600 font-bold">{{ number_format($product->current_selling_price, 2) }}</td>
                    
                    <td class="p-4 text-center">
                        @if($product->current_stock <= 0)
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">نفد من المخزن</span>
                        @elseif($product->current_stock < 50) <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">أوشك على النفاذ</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">متوفر</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500 text-lg">لم يتم العثور على منتجات.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>