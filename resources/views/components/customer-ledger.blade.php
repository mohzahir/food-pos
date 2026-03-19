<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">كشف حساب: {{ $customer->name }}</h1>
        {{-- <a href="/pos" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">العودة للكاشير</a> --}}
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 shadow">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-500">
                <h3 class="text-gray-500 font-bold mb-2">إجمالي الديون المتراكمة</h3>
                <p class="text-4xl font-black text-red-600">{{ number_format($customer->balance, 2) }} <span class="text-sm text-gray-600">جنيه</span></p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold border-b pb-2 mb-4">استلام دفعة جديدة</h3>
                
                <form wire:submit.prevent="addPayment">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">المبلغ المدفوع</label>
                        <input type="number" step="any" wire:model="amount" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">طريقة الدفع</label>
                        <select wire:model.live="payment_method" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="cash">كاش</option>
                            <option value="bankak">بنكك</option>
                        </select>
                    </div>

                    @if($payment_method == 'bankak')
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-green-700 mb-1">رقم الإشعار</label>
                        <input type="text" wire:model="transaction_number" class="w-full border border-green-300 p-2 rounded bg-green-50 focus:ring-2 focus:ring-green-500 outline-none">
                        @error('transaction_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">ملاحظات (اختياري)</label>
                        <input type="text" wire:model="notes" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none" placeholder="مثال: دفعة من حساب شهر مارس">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow">
                        حفظ الدفعة وتحديث الرصيد
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold border-b pb-2 mb-4">سجل الدفعات (سندات القبض)</h3>
                
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="p-2">التاريخ</th>
                            <th class="p-2">المبلغ</th>
                            <th class="p-2">الطريقة</th>
                            <th class="p-2">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2 text-sm">{{ $payment->created_at->format('Y-m-d h:i A') }}</td>
                            <td class="p-2 font-bold text-green-600">{{ number_format($payment->amount, 2) }}</td>
                            <td class="p-2 text-sm">
                                {{ $payment->payment_method == 'bankak' ? 'بنكك ('.$payment->transaction_number.')' : 'كاش' }}
                            </td>
                            <td class="p-2 text-sm text-gray-500">{{ $payment->notes }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">لا توجد دفعات سابقة لهذا العميل.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold border-b pb-2 mb-4 text-gray-700">الفواتير الآجلة المعلقة</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($unpaidSales as $sale)
                        <div class="border border-red-200 bg-red-50 p-3 rounded">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-sm">{{ $sale->receipt_number }}</span>
                                <span class="text-xs text-gray-500">{{ $sale->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="text-sm">إجمالي الفاتورة: {{ number_format($sale->total_amount, 2) }}</div>
                            <div class="text-sm font-bold text-red-600 mt-1">المتبقي منها: {{ number_format($sale->remaining_amount, 2) }}</div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-2">لا توجد فواتير معلقة.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>