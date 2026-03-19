<div class="p-6 bg-white rounded-xl shadow-md border-t-4 border-blue-600 mb-6">
    <div class="flex justify-between items-center mb-4 border-b pb-3">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>📥</span> الاستيراد الذكي للمنتجات (CSV)
        </h2>
        <button wire:click="downloadTemplate" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline bg-blue-50 px-3 py-1 rounded transition-colors">
            تحميل قالب الإكسيل المعتمد ⬇️
        </button>
    </div>

    @if($step == 1)
    <form wire:submit.prevent="analyzeFile" class="flex items-end gap-4 animate-fade-in-up">
        <div class="flex-1">
            <label class="block text-sm font-bold text-gray-700 mb-2">اختر ملف CSV المجهز</label>
            <input type="file" wire:model="file" accept=".csv, .txt" class="w-full border-2 border-dashed border-gray-300 p-3 rounded-lg text-gray-600 bg-gray-50 focus:outline-none focus:border-blue-500 cursor-pointer">
            @error('file') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-lg shadow transition-colors flex items-center gap-2 h-14" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="analyzeFile">تحليل الملف 🔍</span>
            <span wire:loading wire:target="analyzeFile">جاري القراءة... ⏳</span>
        </button>
    </form>
    @endif

    @if($step == 2)
    <div class="animate-fade-in-up">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
            <h3 class="font-bold text-yellow-800 text-lg">⚠️ وجدنا كلمات غير موجودة في النظام!</h3>
            <p class="text-yellow-700 text-sm mt-1">يبدو أن ملف الإكسيل يحتوي على فئات أو وحدات بأسماء مختلفة قليلاً. يرجى إخبارنا كيف نتعامل معها لتجنب التكرار.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @if(count($unmatchedCategories) > 0)
            <div class="border rounded-lg p-4 bg-gray-50">
                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📂 مطابقة الفئات الغريبة</h4>
                @foreach($unmatchedCategories as $catName)
                <div class="mb-3">
                    <label class="block text-xs font-bold text-red-600 mb-1">في الإكسيل: "{{ $catName }}"</label>
                    <select wire:model="categoryMappings.{{ $catName }}" class="w-full border p-2 rounded text-sm focus:ring-blue-500 outline-none">
                        <option value="NEW">➕ إنشاء كفئة جديدة تماماً</option>
                        <optgroup label="أو دمجها مع فئة موجودة:">
                            @foreach($dbCategories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                @endforeach
            </div>
            @endif

            @if(count($unmatchedUnits) > 0)
            <div class="border rounded-lg p-4 bg-gray-50">
                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">⚖️ مطابقة الوحدات الغريبة</h4>
                @foreach($unmatchedUnits as $unitName)
                <div class="mb-3">
                    <label class="block text-xs font-bold text-red-600 mb-1">في الإكسيل: "{{ $unitName }}"</label>
                    <select wire:model="unitMappings.{{ $unitName }}" class="w-full border p-2 rounded text-sm focus:ring-blue-500 outline-none">
                        <option value="NEW">➕ إنشاء كوحدة جديدة تماماً</option>
                        <optgroup label="أو اعتبارها كوحدة موجودة:">
                            @foreach($dbUnits as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <button wire:click="resetImporter" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold px-6 py-2 rounded-lg">إلغاء</button>
            <button wire:click="executeImport" class="bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-2 rounded-lg shadow" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="executeImport">اعتماد ومتابعة الاستيراد ✅</span>
                <span wire:loading wire:target="executeImport">جاري الإدخال... ⏳</span>
            </button>
        </div>
    </div>
    @endif

    @if($step == 3 && $importStats)
        <div class="p-4 rounded-lg bg-green-50 border border-green-200 animate-fade-in-up">
            <h3 class="font-bold text-green-800 text-lg mb-3">🎉 تمت العملية بنجاح!</h3>
            <div class="flex gap-4 mb-4">
                <span class="bg-white border text-gray-800 font-black px-4 py-2 rounded">تمت قراءة: {{ $importStats['total'] }}</span>
                <span class="bg-green-100 text-green-800 font-black px-4 py-2 rounded">تم الاستيراد: {{ $importStats['successful'] }} ✅</span>
                @if($importStats['failed'] > 0)
                <span class="bg-red-100 text-red-800 font-black px-4 py-2 rounded">فشل: {{ $importStats['failed'] }} ❌</span>
                @endif
            </div>

            @if(count($failedRows) > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                    <h4 class="font-bold text-red-800 mb-2">⚠️ مسار الأخطاء (صفوف معطوبة في ملفك):</h4>
                    <ul class="text-sm list-disc list-inside text-red-700 max-h-40 overflow-y-auto pl-2">
                        @foreach($failedRows as $row)
                            <li class="mb-1">سطر <strong>{{ $row['row_number'] }}</strong> ({{ $row['product_name'] }}): {{ $row['error'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex justify-end">
                <button wire:click="resetImporter" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg shadow">استيراد ملف آخر</button>
            </div>
        </div>
    @endif
</div>