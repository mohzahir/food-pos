<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex items-center gap-3 mb-8 border-b pb-4">
        <span class="text-4xl">📡</span>
        <div>
            <h1 class="text-3xl font-black text-gray-800">رادار الصلاحية</h1>
            <p class="text-gray-500 font-bold mt-1">مراقبة تواريخ انتهاء المنتجات المشتراة لتقليل الخسائر</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded-lg shadow-sm">
            <h3 class="font-bold text-red-800 text-lg">🔴 منتهية الصلاحية</h3>
            <p class="text-red-600 text-sm font-bold">يجب إزالتها من الرفوف فوراً</p>
        </div>
        <div class="bg-orange-50 border-r-4 border-orange-500 p-4 rounded-lg shadow-sm">
            <h3 class="font-bold text-orange-800 text-lg">🟠 أقل من 30 يوماً</h3>
            <p class="text-orange-600 text-sm font-bold">يُنصح بعمل عروض وتخفيضات عليها</p>
        </div>
        <div class="bg-green-50 border-r-4 border-green-500 p-4 rounded-lg shadow-sm">
            <h3 class="font-bold text-green-800 text-lg">🟢 آمنة (أكثر من شهر)</h3>
            <p class="text-green-600 text-sm font-bold">صلاحيتها سارية لفترة مريحة</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="p-4 border-b">المنتج</th>
                    <th class="p-4 border-b text-center">الكمية الحالية / المشتراة</th>
                    <th class="p-4 border-b text-center">تاريخ الإدخال</th>
                    <th class="p-4 border-b text-center">تاريخ الانتهاء</th>
                    <th class="p-4 border-b text-center">الأيام المتبقية</th>
                    <th class="p-4 border-b text-center">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expiringItems as $item)
                    @php
                        // تحديد الألوان بناءً على الأيام المتبقية
                        if ($item->days_left < 0) {
                            $rowClass = 'bg-red-50';
                            $textClass = 'text-red-700';
                            $badge = '<span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow">منتهي!</span>';
                        } elseif ($item->days_left <= 30) {
                            $rowClass = 'bg-orange-50';
                            $textClass = 'text-orange-700';
                            $badge = '<span class="bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow">قريب الانتهاء</span>';
                        } else {
                            $rowClass = 'hover:bg-gray-50';
                            $textClass = 'text-gray-700';
                            $badge = '<span class="bg-green-100 text-green-700 border border-green-300 px-3 py-1 rounded-full text-xs font-bold">آمن</span>';
                        }
                    @endphp

                    <tr class="border-b transition-colors {{ $rowClass }}">
                        <td class="p-4">
                            <span class="font-black text-lg {{ $textClass }}">{{ $item->product_name }}</span>
                            <br>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="text-xs text-gray-500 font-bold">الوحدة: {{ $item->unit_name }} | المورد: {{ $item->supplier_name }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $item->source == 'مخزون افتتاحي' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $item->source }}
                                </span>
                            </div>
                        </td>
                        <td class="p-4 text-center font-bold text-gray-700 text-lg">{{ $item->quantity }}</td>
                        <td class="p-4 text-center font-bold text-gray-500">{{ $item->purchase_date }}</td>
                        <td class="p-4 text-center font-black {{ $textClass }}">{{ date('Y-m-d', strtotime($item->expiry_date)) }}</td>
                        <td class="p-4 text-center font-black text-xl {{ $textClass }}" dir="ltr">
                            {{ $item->days_left }}
                        </td>
                        <td class="p-4 text-center">
                            {!! $badge !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-500 font-bold text-lg">
                            🎉 لا توجد أي منتجات مسجلة بتواريخ صلاحية في قاعدة البيانات.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>