<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">🕵️‍♂️</span> سجل حركات المخزن والتدقيق
            </h1>
            <p class="text-gray-500 mt-2 font-bold">مراقبة التعديلات، العجز، الزيادة، وكل ما يحدث لبضاعتك</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">بحث بالمنتج</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-2 border-gray-200 bg-gray-50 p-3 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none font-bold text-gray-700" placeholder="اسم المنتج أو الباركود...">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">نوع الحركة</label>
            <select wire:model.live="type_filter" class="w-full border-2 border-gray-200 bg-gray-50 p-3 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none font-bold text-gray-700">
                <option value="">كل الحركات</option>
                <option value="reconciliation">⚖️ تسوية جردية</option>
                <option value="sale">🛒 مبيعات (منصرف)</option>
                <option value="purchase">📦 مشتريات (وارد)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">التاريخ</label>
            <input type="date" wire:model.live="date_from" class="w-full border-2 border-gray-200 bg-gray-50 p-3 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none font-bold text-gray-700 text-sm">
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="p-4 font-black text-xs uppercase tracking-wider">التاريخ والوقت</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider">الموظف</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider">المنتج</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider text-center">نوع الحركة</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider text-center">التأثير (الكمية)</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider text-center">الرصيد قبل</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider text-center">الرصيد بعد</th>
                        <th class="p-4 font-black text-xs uppercase tracking-wider">السبب / الملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm">
                            <div class="font-black text-gray-800" dir="ltr">{{ $trx->created_at->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500 font-bold" dir="ltr">{{ $trx->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-4 font-bold text-gray-700 text-sm">
                            {{ $trx->user ? $trx->user->name : 'النظام' }}
                        </td>
                        <td class="p-4 font-black text-blue-700 text-sm">
                            {{ $trx->product ? $trx->product->name : 'منتج محذوف' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($trx->type == 'reconciliation')
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">⚖️ تسوية مخزنية</span>
                            @elseif($trx->type == 'sale')
                                <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">🛒 منصرف مبيعات</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">📦 وارد مشتريات</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($trx->quantity > 0)
                                <span class="font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg" dir="ltr">+{{ (float)$trx->quantity }}</span>
                            @else
                                <span class="font-black text-rose-600 bg-rose-50 px-2 py-1 rounded-lg" dir="ltr">{{ (float)$trx->quantity }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center font-bold text-gray-500" dir="ltr">{{ (float)$trx->balance_before }}</td>
                        <td class="p-4 text-center font-black text-gray-900" dir="ltr">{{ (float)$trx->balance_after }}</td>
                        <td class="p-4 text-xs font-bold text-gray-600">
                            {{ $trx->notes ?: '---' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-gray-400 font-bold text-lg">لا توجد حركات مسجلة في المخزن حتى الآن.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>