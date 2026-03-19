<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 flex items-center gap-2">
                <span>📚</span> سجل المشتريات والفواتير
            </h1>
            <p class="text-gray-500 mt-1 font-bold">راجع كل البضائع التي دخلت للمخزن</p>
        </div>
        <a href="{{ route('purchases') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors flex items-center gap-2">
            <span>➕ فاتورة مشتريات جديدة</span>
        </a>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-col md:flex-row gap-4 border border-gray-100">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1">بحث باسم المورد</label>
            <input type="text" wire:model.live.debounce.300ms="search_supplier" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none" placeholder="اكتب اسم المورد...">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1">من تاريخ</label>
            <input type="date" wire:model.live="date_from" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1">إلى تاريخ</label>
            <input type="date" wire:model.live="date_to" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
        <table class="w-full text-right border-collapse">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="p-4 border-b font-bold">رقم الفاتورة</th>
                    <th class="p-4 border-b font-bold">اسم المورد</th>
                    <th class="p-4 border-b text-center font-bold">تاريخ الفاتورة</th>
                    <th class="p-4 border-b text-center font-bold">إجمالي المبلغ</th>
                    <th class="p-4 border-b text-center font-bold">الحالة والمتبقي</th>
                    <th class="p-4 border-b text-center font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr class="hover:bg-gray-50 border-b transition-colors">
                        <td class="p-4 font-bold text-gray-700">#{{ $purchase->id }}</td>
                        <td class="p-4 font-bold text-blue-700">{{ $purchase->supplier_name ?: 'مورد عام (غير محدد)' }}</td>
                        <td class="p-4 text-center text-gray-600">{{ date('Y-m-d', strtotime($purchase->purchase_date)) }}</td>
                        <td class="p-4 text-center font-black text-red-600 text-lg">{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="p-4 text-center">
                            @if($purchase->payment_status == 'paid')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">خالصة ✅</span>
                            @else
                                <div class="flex flex-col items-center gap-1">
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">متبقي: {{ number_format($purchase->remaining_amount, 0) }}</span>
                                    <button wire:click="openPaymentModal({{ $purchase->id }})" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">سداد دفعة 💸</button>
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button wire:click="viewDetails({{ $purchase->id }})" class="bg-gray-100 hover:bg-blue-100 text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded font-bold text-sm transition-colors shadow-sm">
                                👁️ عرض التفاصيل
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500 font-bold text-lg">لا توجد فواتير مشتريات مطابقة للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 bg-gray-50 border-t">
            {{ $purchases->links() }}
        </div>
    </div>

    @if($isModalOpen && $selectedPurchase)
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col animate-fade-in-up">
            
            <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                <div>
                    <h3 class="text-xl font-black text-gray-800">تفاصيل فاتورة المشتريات #{{ $selectedPurchase->id }}</h3>
                    <p class="text-sm text-gray-500 font-bold mt-1">المورد: <span class="text-blue-600">{{ $selectedPurchase->supplier_name ?: 'عام' }}</span> | التاريخ: {{ date('Y-m-d', strtotime($selectedPurchase->purchase_date)) }}</p>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold transition-colors">&times;</button>
            </div>

            <div class="p-0 overflow-y-auto flex-1">
                <table class="w-full text-right border-collapse text-sm">
                    <thead class="bg-gray-100 sticky top-0 shadow-sm">
                        <tr>
                            <th class="p-3 border-b">المنتج</th>
                            <th class="p-3 border-b text-center">الوحدة</th>
                            <th class="p-3 border-b text-center">الكمية</th>
                            <th class="p-3 border-b text-center">التكلفة (للوحدة)</th>
                            <th class="p-3 border-b text-center">سعر البيع المحدث</th>
                            <th class="p-3 border-b text-center">تاريخ الانتهاء</th>
                            <th class="p-3 border-b text-center">الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseItems as $item)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-3 font-bold text-gray-800">{{ $item->product ? $item->product->name : 'منتج محذوف' }}</td>
                            <td class="p-3 text-center text-gray-600">{{ $item->unit ? $item->unit->name : '-' }}</td>
                            <td class="p-3 text-center font-black text-blue-600">{{ (float) $item->quantity }}</td>
                            <td class="p-3 text-center font-bold text-red-600">{{ number_format($item->unit_cost_price, 2) }}</td>
                            <td class="p-3 text-center font-bold text-green-600">{{ number_format($item->new_unit_selling_price, 2) }}</td>
                            <td class="p-3 text-center text-orange-600 text-xs font-bold">{{ $item->expiry_date ? date('Y-m-d', strtotime($item->expiry_date)) : '---' }}</td>
                            <td class="p-3 text-center font-black">{{ number_format($item->quantity * $item->unit_cost_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t bg-gray-50 rounded-b-xl flex justify-between items-center">
                <button wire:click="closeModal" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-lg transition-colors">إغلاق</button>
                <div class="text-xl">
                    <span class="font-bold text-gray-600">إجمالي الفاتورة:</span>
                    <span class="font-black text-red-600 ml-2">{{ number_format($selectedPurchase->total_amount, 2) }}</span>
                </div>
            </div>

        </div>
    </div>
    @endif


    @if($isPaymentModalOpen && $purchaseToPay)
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 animate-fade-in-up">
            <h3 class="text-xl font-black text-gray-800 border-b pb-3 mb-4">💸 سداد مديونية مورد</h3>
            
            <div class="mb-4 bg-gray-50 p-3 rounded border">
                <p class="text-sm font-bold text-gray-700">المورد: <span class="text-blue-600">{{ $purchaseToPay->supplier_name }}</span></p>
                <p class="text-sm font-bold text-red-600 mt-1">المبلغ المتبقي: {{ number_format($purchaseToPay->remaining_amount, 0) }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">المبلغ المراد سداده الآن</label>
                <input type="number" step="1" wire:model="pay_amount" max="{{ $purchaseToPay->remaining_amount }}" class="w-full border-2 border-gray-300 p-2 rounded focus:ring-blue-500 outline-none text-center font-bold text-lg">
                @error('pay_amount') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">طريقة الدفع</label>
                <select wire:model="pay_method" class="w-full border-2 border-gray-300 p-2 rounded focus:ring-blue-500 outline-none font-bold">
                    <option value="cash">💵 نقداً (من الخزينة)</option>
                    <option value="bankak">📱 تحويل بنكك</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="$set('isPaymentModalOpen', false)" class="px-5 py-2 bg-gray-200 text-gray-800 rounded font-bold hover:bg-gray-300">إلغاء</button>
                <button wire:click="submitPayment" class="px-5 py-2 bg-green-600 text-white rounded font-bold hover:bg-green-700 shadow">تأكيد السداد ✅</button>
            </div>
        </div>
    </div>
    @endif

</div>