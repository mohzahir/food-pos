<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen relative">
    
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">👥</span> سجل العملاء وإدارة الديون
            </h1>
            <p class="text-slate-500 mt-2 font-bold">تتبع مديونيات الزبائن وتنظيم الحسابات المالية</p>
        </div>
        
        <div class="flex flex-wrap gap-4">
            <div class="bg-white border border-slate-200 px-5 py-3 rounded-2xl shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl">💸</div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase">إجمالي الديون بالسوق</p>
                    <p class="text-lg font-black text-slate-800" dir="ltr">{{ number_format($customers->sum('balance'), 0) }}</p>
                </div>
            </div>

            <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                <span>➕ إضافة عميل جديد</span>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-6 font-bold animate-slide-in flex items-center gap-3">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 bg-white p-4 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group transition-all focus-within:border-blue-300">
        <div class="relative">
            <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                <span class="text-2xl opacity-40 group-focus-within:opacity-100 transition-opacity">🔍</span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-none rounded-2xl p-4 pr-14 text-lg focus:ring-0 font-bold bg-transparent text-slate-700 placeholder-slate-400" placeholder="ابحث عن عميل بالاسم أو رقم الهاتف...">
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-200">
                        <th class="p-5 font-black text-sm uppercase tracking-wider border-b border-slate-800">العميل</th>
                        <th class="p-5 font-black text-sm uppercase tracking-wider border-b border-slate-800 text-center">التواصل</th>
                        <th class="p-5 font-black text-sm uppercase tracking-wider border-b border-slate-800 text-center">الرصيد المتبقي</th>
                        <th class="p-5 font-black text-sm uppercase tracking-wider border-b border-slate-800 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center text-xl font-black group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-inner">
                                    {{ mb_substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 text-lg leading-tight">{{ $customer->name }}</p>
                                    <p class="text-xs text-slate-400 font-bold mt-1">عضو منذ {{ $customer->created_at->format('Y/m/d') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-center">
                            @if($customer->phone)
                                <span class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-full font-bold text-sm" dir="ltr">📞 {{ $customer->phone }}</span>
                            @else
                                <span class="text-slate-300 italic text-sm">لا يوجد رقم</span>
                            @endif
                        </td>
                        <td class="p-5 text-center">
                            @if($customer->balance > 0)
                                <div class="inline-flex flex-col items-center">
                                    <span class="bg-rose-50 text-rose-600 border border-rose-100 px-5 py-2 rounded-2xl font-black text-xl shadow-sm" dir="ltr">
                                        {{ number_format($customer->balance, 0) }}
                                    </span>
                                    <span class="text-[10px] text-rose-400 font-bold mt-1 uppercase tracking-tighter">مديونية قائمة ⚠️</span>
                                </div>
                            @else
                                <div class="inline-flex flex-col items-center opacity-40">
                                    <span class="bg-emerald-50 text-emerald-600 px-5 py-2 rounded-2xl font-black text-xl">0</span>
                                    <span class="text-[10px] text-emerald-500 font-bold mt-1 uppercase tracking-tighter">الحساب صافي ✅</span>
                                </div>
                            @endif
                        </td>
                        <td class="p-5">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('customers.ledger', $customer->id) }}" class="bg-white border-2 border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white font-black py-2.5 px-5 rounded-2xl shadow-sm transition-all text-sm flex items-center gap-2 group-hover:border-blue-600">
                                    <span>📄 كشف الحساب</span>
                                </a>
                                
                                <button wire:click="editCustomer({{ $customer->id }})" class="w-10 h-10 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-xl transition-all flex items-center justify-center shadow-sm" title="تعديل البيانات">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>

                                <button wire:click="deleteCustomer({{ $customer->id }})" wire:confirm="هل أنت متأكد من حذف هذا العميل؟ سيؤدي ذلك لحذف سجل ديونه أيضاً!" class="w-10 h-10 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-all flex items-center justify-center shadow-sm" title="حذف العميل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center text-5xl mb-4 grayscale opacity-30 shadow-inner">👥</div>
                                <p class="text-2xl font-black text-slate-400">لا يوجد نتائج</p>
                                <p class="text-sm font-bold text-slate-300 mt-1">تأكد من كتابة الاسم بشكل صحيح أو أضف عميلاً جديداً</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] w-full max-w-md overflow-hidden animate-slide-in border border-slate-200">
            
            <div class="bg-slate-900 p-6 flex justify-between items-center text-white border-b border-slate-800">
                <h3 class="text-xl font-black flex items-center gap-3">
                    <span class="text-blue-400">{{ $isEditMode ? '✏️' : '➕' }}</span>
                    {{ $isEditMode ? 'تعديل بيانات العميل' : 'إضافة عميل جديد' }}
                </h3>
                <button wire:click="closeModal" class="w-8 h-8 bg-slate-800 rounded-full flex items-center justify-center hover:bg-rose-500 transition-colors">&times;</button>
            </div>

            <div class="p-8">
                <form wire:submit.prevent="saveCustomer" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-wide">اسم العميل (إلزامي)</label>
                        <input type="text" wire:model="name" autofocus class="w-full border-2 border-slate-100 bg-slate-50 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-lg text-slate-800 transition-all" placeholder="مثال: محمد أحمد">
                        @error('name') <span class="text-rose-500 text-xs font-bold mt-2 block bg-rose-50 p-2 rounded-lg tracking-tight">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-wide">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="phone" dir="ltr" class="w-full border-2 border-slate-100 bg-slate-50 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-lg text-left tracking-widest text-slate-800 transition-all" placeholder="0123456789">
                        @error('phone') <span class="text-rose-500 text-xs font-bold mt-2 block bg-rose-50 p-2 rounded-lg tracking-tight">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-3 pt-4">
                        <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 text-lg">
                            {{ $isEditMode ? '💾 حفظ التعديلات الجديدة' : '💾 حفظ وتسجيل العميل' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="w-full bg-slate-100 text-slate-500 font-bold py-3 rounded-2xl hover:bg-slate-200 transition-all">إلغاء العملية</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
/* حركة الظهور الانسيابية */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>