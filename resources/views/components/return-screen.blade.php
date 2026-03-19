<div class="p-6 max-w-6xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إدارة المرتجعات (Returns)</h1>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 shadow font-bold text-lg text-center">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4 shadow font-bold text-lg text-center">{{ session('error') }}</div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 border-t-4 border-blue-500">
        <form wire:submit.prevent="searchInvoice" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">رقم الفاتورة (مثال: INV-1710...)</label>
                <input type="text" wire:model="receipt_number" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-lg" placeholder="اكتب رقم الفاتورة أو امسحه بالباركود..." autofocus dir="ltr">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg text-lg">
                🔍 بحث
            </button>
        </form>
    </div>

    @if($sale)
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-red-500">
        
        <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-gray-800">فاتورة: {{ $sale->receipt_number }}</h2>
                <p class="text-gray-600 mt-1">العميل: <span class="font-bold">{{ $sale->customer ? $sale->customer->name : 'نقدي عام' }}</span> | التاريخ: {{ $sale->created_at->format('Y-m-d h:i A') }}</p>
            </div>
            <div class="text-left">
                <p class="text-sm text-gray-500">الإجمالي الحالي:</p>
                <p class="text-2xl font-black text-blue-600">{{ number_format($sale->total_amount, 2) }}</p>
            </div>
        </div>

        @if($sale->items->count() > 0)
        <div class="overflow-x-auto p-4">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3 border">الصنف (الوحدة)</th>
                        <th class="p-3 border text-center">الكمية المشتراة</th>
                        <th class="p-3 border text-center">سعر الوحدة</th>
                        <th class="p-3 border text-center">الإجمالي</th>
                        <th class="p-3 border text-center bg-red-50 text-red-700 w-48">إجراء المرتجع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border font-bold">{{ $item->product->name }} <br><span class="text-sm text-gray-500">({{ $item->unit->name }})</span></td>
                        <td class="p-3 border text-center text-lg">{{ (float) $item->quantity }}</td>
                        <td class="p-3 border text-center">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-3 border text-center font-bold text-blue-600">{{ number_format($item->subtotal, 2) }}</td>
                        <td class="p-3 border bg-red-50">
                            <div class="flex gap-2">
                                <input type="number" step="any" min="0" max="{{ $item->quantity }}" wire:model="return_quantities.{{ $item->id }}" class="w-20 border-2 border-red-200 p-1 rounded text-center focus:ring-2 focus:ring-red-500 outline-none">
                                <button wire:click="processReturn({{ $item->id }})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold shadow">
                                    إرجاع
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-500 text-lg font-bold">
            تم إرجاع جميع أصناف هذه الفاتورة مسبقاً (الفاتورة ملغية).
        </div>
        @endif

    </div>
    @endif

</div>