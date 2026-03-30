<div class="p-6 max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 bg-slate-800 text-white">
            <h2 class="text-xl font-black">⚙️ إعدادات المتجر الهوية</h2>
            <p class="text-slate-400 text-sm">تحكم في اسم المحل والمعلومات التي تظهر في الفاتورة</p>
        </div>

        <div class="p-8 space-y-6">
            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 font-bold">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم المتجر</label>
                    <input type="text" wire:model="store_name" class="w-full border-2 border-slate-100 rounded-xl p-3 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" wire:model="phone" class="w-full border-2 border-slate-100 rounded-xl p-3 focus:border-indigo-500 outline-none text-left" dir="ltr">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
                <input type="text" wire:model="address" class="w-full border-2 border-slate-100 rounded-xl p-3 focus:border-indigo-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">رسالة أسفل الفاتورة</label>
                <textarea wire:model="receipt_footer" rows="3" class="w-full border-2 border-slate-100 rounded-xl p-3 focus:border-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button wire:click="save" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                    حفظ التغييرات ✨
                </button>
            </div>
        </div>
    </div>

    <div class="mt-10 border-t-2 border-slate-100 pt-8">
        <h3 class="text-xl font-black text-slate-800 mb-4 flex items-center gap-2">
            <span class="text-emerald-500">💾</span> النسخ الاحتياطي للبيانات
        </h3>
        
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center gap-6 shadow-inner">
            <div>
                <p class="font-black text-emerald-800 text-lg">حماية بياناتك هي الأهم!</p>
                <p class="text-sm text-emerald-600 mt-1 font-bold leading-relaxed">
                    قم بتحميل نسخة كاملة من قاعدة البيانات (تشمل المنتجات، فواتير المبيعات، ديون العملاء، وحسابات الموردين). يُنصح بتحميل نسخة يومياً وحفظها في فلاشة (USB) أو إرسالها لنفسك عبر تليجرام/واتساب كإجراء أمني.
                </p>
            </div>
            
            <button wire:click="downloadBackup" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-8 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2 shrink-0 text-lg">
                <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>تحميل النسخة الآن</span>
            </button>
        </div>
    </div>
</div>