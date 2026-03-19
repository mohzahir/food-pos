<div class="p-6 max-w-7xl mx-auto min-h-screen relative">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 flex items-center gap-2">
                <span>🚛</span> سجل الموردين والشركات
            </h1>
            <p class="text-gray-500 mt-1 font-bold">إدارة بيانات الموردين وتتبع مديونيات المحل لهم</p>
        </div>
        
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
            <span>➕ إضافة مورد جديد</span>
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 font-bold animate-fade-in-up">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 font-bold animate-fade-in-up">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
        <div class="relative">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-400">🔍</span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-2 border-gray-200 rounded-lg p-3 pr-10 text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-bold bg-gray-50" placeholder="ابحث عن مورد بالاسم، الشركة، أو رقم الهاتف...">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-4 border-b">اسم المندوب / المورد</th>
                        <th class="p-4 border-b text-center">الشركة</th>
                        <th class="p-4 border-b text-center">رقم الهاتف</th>
                        <th class="p-4 border-b text-center text-red-400">إجمالي الديون (علينا)</th>
                        <th class="p-4 border-b text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-800 text-lg">{{ $supplier->name }}</td>
                        <td class="p-4 text-center font-bold text-blue-700">{{ $supplier->company ?? '---' }}</td>
                        <td class="p-4 text-center text-gray-600 font-bold" dir="ltr">{{ $supplier->phone ?? '---' }}</td>
                        
                        <td class="p-4 text-center">
                            @if($supplier->balance > 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-black text-lg shadow-inner">
                                    {{ number_format($supplier->balance, 0) }}
                                </span>
                            @else
                                <span class="text-gray-400 font-bold text-sm">حساب خالص</span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-center flex items-center justify-center gap-2">
                            <button wire:click="editSupplier({{ $supplier->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 px-3 rounded shadow transition-colors text-sm" title="تعديل البيانات">
                                ✏️ تعديل
                            </button>
                            <button wire:click="deleteSupplier({{ $supplier->id }})" wire:confirm="هل أنت متأكد من حذف هذا المورد؟" class="bg-red-100 hover:bg-red-600 text-red-600 hover:text-white font-bold py-1.5 px-3 rounded shadow-sm transition-colors text-sm" title="حذف">
                                🗑️
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">
                            <div class="text-5xl mb-3 opacity-50">🚛</div>
                            <p class="text-xl font-bold">لا يوجد موردين مسجلين أو مطابقين للبحث.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-fade-in-up overflow-hidden">
            
            <div class="bg-gray-800 p-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-black">{{ $isEditMode ? '✏️ تعديل بيانات المورد' : '➕ إضافة مورد جديد' }}</h3>
                <button wire:click="closeModal" class="text-gray-300 hover:text-red-500 text-2xl font-bold transition-colors">&times;</button>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="saveSupplier">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم المندوب / المورد (إلزامي)</label>
                        <input type="text" wire:model="name" autofocus class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg" placeholder="مثال: أحمد مصطفى">
                        @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم الشركة (اختياري)</label>
                        <input type="text" wire:model="company" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg" placeholder="مثال: شركة سيقا">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="phone" dir="ltr" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg text-left" placeholder="0123456789">
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t">
                        <button type="button" wire:click="closeModal" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg font-bold hover:bg-gray-300 transition-colors">إلغاء</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-md">
                            {{ $isEditMode ? '💾 حفظ التعديلات' : '💾 حفظ المورد' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @endif

</div>