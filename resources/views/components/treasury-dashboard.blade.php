<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-black text-gray-800 flex items-center gap-2">
            <span>🏦</span> إدارة الخزينة المركزية وحسابات البنوك
        </h1>
        <p class="text-gray-500 font-bold mt-1">مراقبة السيولة النقدية وتتبع الحركات المالية</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md font-bold animate-fade-in-up">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-green-100 font-bold mb-1">الرصيد الفعلي في درج الكاشير</p>
                    <h3 class="text-4xl font-black" dir="ltr">{{ number_format($actualCashBalance, 0) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-xl text-4xl shadow-inner">💵</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-blue-100 font-bold mb-1">الرصيد الفعلي في تطبيق بنكك</p>
                    <h3 class="text-4xl font-black" dir="ltr">{{ number_format($actualBankakBalance, 0) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-xl text-4xl shadow-inner">📱</div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-yellow-500 h-fit">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">🔄 تنفيذ حركة مالية</h3>
            
            <form wire:submit.prevent="saveAdjustment">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع الحركة</label>
                    <select wire:model="adj_type" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none font-bold">
                        <optgroup label="تحويلات بين الحسابات">
                            <option value="transfer_to_bank">⬅️ توريد كاش من الدرج إلى بنكك</option>
                            <option value="transfer_to_cash">➡️ سحب من بنكك ووضعه كاش في الدرج</option>
                        </optgroup>
                        <optgroup label="عمليات الكاش">
                            <option value="deposit_cash">📥 إيداع رأس مال (كاش)</option>
                            <option value="withdrawal_cash">📤 سحب أرباح أو شخصي (كاش)</option>
                        </optgroup>
                        <optgroup label="عمليات بنكك">
                            <option value="deposit_bankak">📥 إيداع رأس مال (بنكك)</option>
                            <option value="withdrawal_bankak">📤 سحب أرباح أو شخصي (بنكك)</option>
                        </optgroup>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">المبلغ</label>
                    <input type="number" step="1" wire:model="adj_amount" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none font-black text-xl text-center" placeholder="0">
                    @error('adj_amount') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">البيان / ملاحظات</label>
                    <input type="text" wire:model="adj_notes" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none" placeholder="مثال: توريد كاش لنهاية الأسبوع">
                </div>

                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition-colors text-lg">
                    تأكيد وتسجيل الحركة
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border-t-4 border-gray-800">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">📜 سجل الحركات والتسويات الأخيرة</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="p-3 border-b font-bold">التاريخ</th>
                            <th class="p-3 border-b font-bold">نوع الحركة</th>
                            <th class="p-3 border-b font-bold text-center">المبلغ</th>
                            <th class="p-3 border-b font-bold">البيان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAdjustments as $adj)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-500" dir="ltr">{{ $adj->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3 font-bold">
                                @if($adj->type == 'transfer_to_bank') <span class="text-blue-600">توريد للبنك</span>
                                @elseif($adj->type == 'transfer_to_cash') <span class="text-green-600">سحب كاش من البنك</span>
                                @elseif($adj->type == 'deposit_cash') <span class="text-green-600">إيداع كاش</span>
                                @elseif($adj->type == 'withdrawal_cash') <span class="text-red-600">سحب كاش</span>
                                @elseif($adj->type == 'deposit_bankak') <span class="text-blue-600">إيداع بنكك</span>
                                @elseif($adj->type == 'withdrawal_bankak') <span class="text-red-600">سحب بنكك</span>
                                @endif
                            </td>
                            <td class="p-3 text-center font-black text-lg">{{ number_format($adj->amount, 0) }}</td>
                            <td class="p-3 text-gray-600 font-bold">{{ $adj->notes ?: '---' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-400 font-bold">لا توجد حركات تسوية مسجلة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>