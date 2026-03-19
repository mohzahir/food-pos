<div class="p-6 max-w-7xl mx-auto min-h-screen">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إدارة المشتريات والتسعير الشامل</h1>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md text-center font-bold text-xl animate-fade-in-up">✅ {{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6 shadow-md text-center font-bold text-xl animate-fade-in-up">❌ {{ session('error') }}</div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-md mb-6 flex gap-6 border-t-4 border-gray-800">
        <div class="bg-white p-6 rounded-xl shadow-md mb-6 flex flex-col md:flex-row gap-6 border-t-4 border-gray-800">
            <div class="flex-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">المورد (مطلوب للفواتير الآجلة)</label>
                <div class="flex gap-2">
                    <select wire:model="supplier_id" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-lg font-bold bg-white">
                        <option value="">-- مورد نقدي عام --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} {{ $supplier->company ? '('.$supplier->company.')' : '' }}</option>
                        @endforeach
                    </select>
                    <button wire:click="openSupplierModal" class="bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-4 rounded-lg border border-blue-300 transition-colors" title="إضافة مورد جديد">
                        ➕
                    </button>
                </div>
                @if (session()->has('success_supplier'))
                    <span class="text-green-600 text-[11px] font-bold mt-1 block">{{ session('success_supplier') }}</span>
                @endif
            </div>
            
            <div class="flex-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">تاريخ الفاتورة</label>
                <input type="date" wire:model="purchase_date" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-lg">
            </div>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-bold text-gray-700 mb-2">تاريخ الفاتورة</label>
            <input type="date" wire:model="purchase_date" class="w-full border-2 border-gray-200 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-lg">
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-sm mb-6">
        <h3 class="font-bold text-blue-800 mb-4 text-xl border-b border-blue-200 pb-2">📦 إضافة بضاعة للفاتورة</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end mb-4">
            
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">المنتج</label>
                <select wire:model.live="selected_product" class="w-full border-2 border-blue-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold bg-white">
                    <option value="">-- اختر المنتج --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('selected_product') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">الوحدة المشتراة (مفلترة تلقائياً)</label>
                <select wire:model.live="selected_unit" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white" @if(!$selected_product) disabled @endif>
                    @if(!$selected_product)
                        <option value="">-- اختر المنتج أولاً --</option>
                    @else
                        @foreach($available_units as $unit)
                            <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                @error('selected_unit') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">الكمية المشتراة <span class="text-blue-500 text-xs">(بهذه الوحدة)</span></label>
                <input type="number" step="any" wire:model="quantity" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-xl font-bold text-center" placeholder="الكمية">
                @error('quantity') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ الانتهاء <span class="text-orange-500 text-xs">(اختياري للمنتجات الحساسة)</span></label>
                <input type="date" wire:model="expiry_date" class="w-full border-2 border-orange-200 bg-orange-50 p-3 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none text-center font-bold text-orange-800">
                @error('expiry_date') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>
        </div>

        @if($selected_product)
        <div class="bg-white p-4 rounded-lg border shadow-sm mb-4">
            <h4 class="font-bold text-gray-600 mb-3 border-b pb-2">💰 تحديث أسعار السوق (اختياري)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-gray-50 p-3 rounded border">
                    <label class="block text-sm font-bold text-red-700 mb-1">تكلفة الشراء (للوحدة المحددة)</label>
                    <input type="number" step="any" wire:model="unit_cost_price" onclick="this.select()" class="w-full border border-red-300 bg-red-50 p-2 rounded focus:ring-2 focus:ring-red-500 outline-none text-lg font-bold text-center">
                    @error('unit_cost_price') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="bg-green-50 p-3 rounded border">
                    <label class="block text-sm font-bold text-green-800 mb-1">سعر البيع قطاعي (للوحدة المحددة)</label>
                    <input type="number" step="any" wire:model="new_unit_selling_price" onclick="this.select()" class="w-full border border-green-300 bg-white p-2 rounded focus:ring-2 focus:ring-green-500 outline-none text-lg font-bold text-green-700 text-center">
                    @error('new_unit_selling_price') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            @if(count($wholesale_records) > 0)
            <div class="mt-4 bg-purple-50 p-3 rounded border border-purple-200">
                <label class="block text-sm font-bold text-purple-800 mb-2">تسعيرات الجملة المربوطة بهذا المنتج</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($wholesale_records as $record)
                    <div>
                        <span class="text-xs text-purple-600 font-bold">جملة ({{ $record->unit->name }})</span>
                        <input type="number" step="any" wire:model="wholesale_prices.{{ $record->id }}" onclick="this.select()" class="w-full border border-purple-300 p-2 rounded outline-none text-center font-bold text-purple-800" placeholder="السعر">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <button wire:click="addItem" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all text-lg">
            ➕ إضافة إلى الفاتورة
        </button>
        @endif

    </div>

    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
        <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">تفاصيل الفاتورة</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 border">المنتج</th>
                        <th class="p-3 border">الوحدة</th>
                        <th class="p-3 border text-center">الكمية</th>
                        <th class="p-3 border text-center">التكلفة</th>
                        <th class="p-3 border text-center text-green-700">البيع</th>
                        <th class="p-3 border text-center">الإجمالي</th>
                        <th class="p-3 border text-center text-orange-600">تاريخ الانتهاء</th>
                        <th class="p-3 border text-center">إلغاء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border font-bold">{{ $item['product_name'] }}</td>
                            <td class="p-3 border">{{ $item['unit_name'] }}</td>
                            <td class="p-3 border text-center font-black text-lg">{{ $item['quantity'] }}</td>
                            <td class="p-3 border text-center text-red-600">{{ number_format($item['unit_cost_price'], 2) }}</td>
                            <td class="p-3 border text-center text-green-600 font-bold">{{ number_format($item['new_unit_selling_price'], 2) }}</td>
                            <td class="p-3 border text-center font-black text-blue-600">{{ number_format($item['subtotal'], 2) }}</td>
                            <td class="p-3 border text-center text-sm font-bold text-orange-600">
                                {{ $item['expiry_date'] ? date('Y-m-d', strtotime($item['expiry_date'])) : '---' }}
                            </td>
                            <td class="p-3 border text-center">
                                <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:bg-red-100 p-2 rounded-full transition-colors">❌</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500 text-lg">الفاتورة فارغة، قم بإضافة أصناف من الأعلى.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md mb-6 border-t-4 border-gray-800">
        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">💳 تفاصيل سداد الفاتورة للمورد</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
            
            <div class="bg-gray-100 p-4 rounded-lg border text-center shadow-inner h-full flex flex-col justify-center">
                <span class="block text-sm font-bold text-gray-500 mb-1">إجمالي الفاتورة</span>
                <span class="text-3xl font-black text-blue-700">{{ number_format($total_amount, 0) }}</span>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2">💵 ندفع من (الخزنة / الكاش)</label>
                    <input type="number" step="1" wire:model.live.debounce.300ms="paid_cash" onclick="this.select()" class="w-full border-2 border-green-300 p-2 rounded-lg focus:ring-2 focus:ring-green-500 outline-none font-black text-center text-green-700 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2">📱 ندفع من (تطبيق بنكك)</label>
                    <input type="number" step="1" wire:model.live.debounce.300ms="paid_bankak" onclick="this.select()" class="w-full border-2 border-blue-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-black text-center text-blue-700 bg-white">
                </div>
                
                @if($paid_bankak > 0)
                <div class="col-span-2 mt-2 animate-fade-in-up">
                    <input type="text" wire:model="transaction_number" class="w-full border border-blue-400 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50" placeholder="رقم إشعار التحويل للمورد...">
                </div>
                @endif
            </div>

            <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center shadow-inner h-full flex flex-col justify-center">
                <span class="block text-sm font-bold text-red-600 mb-1">المتبقي (دين للمورد)</span>
                <span class="text-3xl font-black text-red-700" dir="ltr">
                    {{ number_format($total_amount - ((float)$paid_cash + (float)$paid_bankak), 0) }}
                </span>
            </div>

        </div>
    </div>

    <div class="text-left mb-8">
        <button wire:click="savePurchase" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white px-12 py-4 rounded-xl font-black shadow-xl transition-transform transform hover:scale-105 text-xl disabled:opacity-50 disabled:cursor-not-allowed" @if(empty($cart)) disabled @endif>
            💾 تأكيد الدفع وحفظ فاتورة المشتريات
        </button>
    </div>


    @if($isSupplierModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm animate-fade-in-up">
            
            <div class="bg-blue-800 p-4 rounded-t-xl flex justify-between items-center text-white">
                <h3 class="text-lg font-black">➕ إضافة مورد سريع</h3>
                <button wire:click="closeSupplierModal" class="text-blue-200 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <div class="p-5">
                <form wire:submit.prevent="saveNewSupplier">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم المندوب / المورد (إلزامي)</label>
                        <input type="text" wire:model="newSupplierName" autofocus class="w-full border-2 border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        @error('newSupplierName') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم الشركة (اختياري)</label>
                        <input type="text" wire:model="newSupplierCompany" class="w-full border-2 border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="newSupplierPhone" dir="ltr" class="w-full border-2 border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none font-bold text-left">
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 shadow transition-colors">
                        💾 حفظ المورد واختياره
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>