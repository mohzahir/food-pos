<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800">💸 إدارة المصروفات</h1>
            <p class="text-gray-500 font-bold mt-1">سجل كل منصرفات المحل لخصمها من الأرباح لاحقاً</p>
        </div>
        
        <div class="bg-white p-2 rounded-lg shadow border flex items-center gap-3">
            <label class="font-bold text-gray-700">عرض وتسجيل في يوم:</label>
            <input type="date" wire:model.live="filter_date" class="border-2 border-blue-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-700">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md text-center font-bold text-xl animate-fade-in-up">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-red-500 h-fit">
            <h3 class="font-black text-red-700 mb-4 text-xl border-b pb-2">تسجيل منصرف جديد</h3>
            
            <form wire:submit.prevent="addExpense">
                
                <label class="block text-sm font-bold text-gray-700 mb-2">المبلغ (من أين تم الدفع؟)</label>
                <div class="grid grid-cols-2 gap-3 mb-4 bg-red-50 p-3 rounded-lg border border-red-100">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">💵 كاش (من الدرج)</label>
                        <input type="number" step="any" wire:model="paid_cash" onclick="this.select()" class="w-full border-2 border-green-300 p-2 rounded focus:ring-2 focus:ring-green-500 outline-none font-black text-center text-green-700 bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">📱 بنكك (من الحساب)</label>
                        <input type="number" step="any" wire:model.live.debounce.300ms="paid_bankak" onclick="this.select()" class="w-full border-2 border-blue-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none font-black text-center text-blue-700 bg-white" placeholder="0">
                    </div>
                    @if($paid_bankak > 0)
                    <div class="col-span-2 animate-fade-in-up mt-1">
                        <input type="text" wire:model="transaction_number" class="w-full border border-blue-400 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50" placeholder="رقم إشعار تحويل بنكك...">
                    </div>
                    @endif
                    @error('payment') <span class="col-span-2 text-red-500 text-xs font-bold text-center mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف</label>
                    <select wire:model="category" class="w-full border-2 border-gray-300 p-3 rounded-lg outline-none focus:border-red-500 font-bold bg-white">
                        <option value="نثريات">نثريات (أكياس، نظافة، الخ)</option>
                        <option value="فواتير">فواتير (كهرباء، مياه، انترنت)</option>
                        <option value="رواتب">رواتب ويوميات عمال</option>
                        <option value="إيجار">إيجار المحل</option>
                        <option value="أخرى">أخرى</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">التفاصيل (البيان)</label>
                    <textarea wire:model="description" rows="3" class="w-full border-2 border-gray-300 p-3 rounded-lg outline-none focus:border-red-500 font-bold" placeholder="مثال: شراء أكياس تعبئة صغيرة..."></textarea>
                    @error('description') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-lg shadow-md transition-colors text-lg flex justify-center items-center gap-2">
                    <span>تأكيد الخصم</span> <span>🔻</span>
                </button>
            </form>
        </div>

        <div class="md:col-span-2">
            
            <div class="bg-red-50 border-2 border-red-200 p-6 rounded-xl shadow-sm mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-red-800 font-bold text-lg">إجمالي مصروفات يوم ({{ date('Y-m-d', strtotime($filter_date)) }})</h3>
                </div>
                <div>
                    <span class="text-4xl font-black text-red-600">{{ number_format($totalExpenses, 2) }}</span> <span class="text-gray-600 font-bold">جنيه</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="p-4 border-b">التصنيف</th>
                            <th class="p-4 border-b w-1/2">البيان / التفاصيل</th>
                            <th class="p-4 border-b text-center">المبلغ</th>
                            <th class="p-4 border-b text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expensesList as $expense)
                            <tr class="hover:bg-gray-50 border-b transition-colors">
                                <td class="p-4">
                                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-xs font-bold">{{ $expense->category }}</span>
                                </td>
                                <td class="p-4 font-bold text-gray-700">{{ $expense->description }}</td>
                                <td class="p-4 text-center">
                                    <div class="font-black text-red-600 text-lg">{{ number_format($expense->amount, 0) }}</div>
                                    <div class="text-[10px] font-bold mt-1 flex flex-col gap-0.5 items-center">
                                        @if($expense->paid_cash > 0)
                                            <span class="text-green-600 bg-green-50 px-1 rounded">كاش: {{ number_format($expense->paid_cash, 0) }}</span>
                                        @endif
                                        @if($expense->paid_bankak > 0)
                                            <span class="text-blue-600 bg-blue-50 px-1 rounded">بنكك: {{ number_format($expense->paid_bankak, 0) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="هل أنت متأكد من مسح هذا المنصرف؟" class="text-red-400 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-full transition-colors" title="حذف">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-gray-500 font-bold text-lg">
                                    ✨ لا توجد منصرفات مسجلة في هذا اليوم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>