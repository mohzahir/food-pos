<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4 fixed inset-0 z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center border-t-8 border-red-600 animate-fade-in-up">
        
        <div class="text-6xl mb-4">🔒</div>
        <h1 class="text-3xl font-black text-gray-800 mb-2">النظام مقفل</h1>
        <p class="text-gray-600 font-bold mb-6">هذه النسخة غير مفعلة أو تم نقلها لجهاز آخر. يرجى إدخال مفتاح الترخيص.</p>

        <div class="bg-gray-100 p-4 rounded-lg border border-gray-300 mb-6">
            <p class="text-sm text-gray-500 font-bold mb-1">رقم الجهاز (أرسله للمطور):</p>
            <p class="text-2xl font-black text-blue-700 tracking-widest font-mono" dir="ltr">{{ $machine_id }}</p>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded-lg font-bold mb-6 border border-red-300 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="activate">
            <div class="mb-6 text-left">
                <label class="block text-sm font-bold text-gray-700 mb-2 text-right">أدخل مفتاح التفعيل هنا:</label>
                <input type="text" wire:model="entered_key" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-center font-black text-xl tracking-widest text-gray-800 uppercase" placeholder="XXXX-XXXX-XXXX-XXXX" dir="ltr">
                
                @error('entered_key') 
                    <span class="text-red-600 font-bold text-sm block mt-2 text-center bg-red-50 py-1 rounded">{{ $message }}</span> 
                @enderror
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-lg shadow-lg transition-transform transform hover:scale-105 text-lg flex justify-center items-center gap-2">
                <span>تفعيل النظام الآن</span> <span>🔓</span>
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-6 font-bold">تطوير: Mohammed Zahir | جميع الحقوق محفوظة ©</p>
    </div>
</div>