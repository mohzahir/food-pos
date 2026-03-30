<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50">
    
    <div class="flex justify-between items-center mb-8 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="text-blue-600">📦</span> إدارة المشتريات والتسعير
            </h1>
            <p class="text-slate-500 mt-2 font-bold">إدخال بضاعة للمخزن، تحديث الأسعار، وتسوية حساب المورد</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3 shadow-sm text-lg">
            <span class="text-2xl">✅</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl relative mb-8 font-bold animate-slide-in flex items-center gap-3 shadow-sm text-lg">
            <span class="text-2xl">⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-slate-100">
        <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-2 h-6 bg-slate-800 rounded-full"></span> بيانات الفاتورة الأساسية
        </h3>
        
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-widest">المورد (مطلوب للفواتير الآجلة)</label>
                <div class="flex gap-3">
                    <select wire:model="supplier_id" class="w-full border-2 border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none text-lg font-black bg-slate-50 text-slate-800 transition-all cursor-pointer">
                        <option value="">-- 🚛 مورد نقدي عام --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} {{ $supplier->company ? '('.$supplier->company.')' : '' }}</option>
                        @endforeach
                    </select>
                    <button wire:click="openSupplierModal" class="bg-slate-800 text-white hover:bg-blue-600 px-5 rounded-2xl shadow-md transition-all flex items-center justify-center font-black text-xl shrink-0" title="إضافة مورد جديد">
                        +
                    </button>
                </div>
                @if (session()->has('success_supplier'))
                    <span class="text-emerald-600 text-[11px] font-black mt-2 block bg-emerald-50 w-fit px-2 py-1 rounded-md">{{ session('success_supplier') }}</span>
                @endif
            </div>
            
            <div class="flex-1">
                <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-widest">تاريخ الفاتورة</label>
                <input type="date" wire:model="purchase_date" class="w-full border-2 border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none text-lg font-black bg-slate-50 text-slate-800 transition-all">
            </div>
        </div>
    </div>

    <div class="bg-blue-50/50 border border-blue-100 p-6 sm:p-8 rounded-[2.5rem] shadow-sm mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>
        
        <h3 class="font-black text-blue-800 mb-8 flex items-center gap-2 text-xl">
            <span class="w-2 h-6 bg-blue-500 rounded-full"></span> إضافة بضاعة وتحديث الأسعار
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end mb-8 relative z-10">
            
            <div class="md:col-span-2">
                <label class="block text-xs font-black text-slate-600 mb-2 uppercase">المنتج</label>
                <select wire:model.live="selected_product" class="w-full border-2 border-blue-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-slate-800 bg-white transition-all shadow-sm">
                    <option value="">-- اختر المنتج --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('selected_product') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-black text-slate-600 mb-2 uppercase">الوحدة المشتراة</label>
                <select wire:model.live="selected_unit" class="w-full border-2 border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none bg-white font-bold text-slate-700 transition-all shadow-sm" @if(!$selected_product) disabled @endif>
                    @if(!$selected_product)
                        <option value="">-- اختر المنتج أولاً --</option>
                    @else
                        @foreach($available_units as $unit)
                            <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                @error('selected_unit') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-1">
                <label class="block text-xs font-black text-slate-600 mb-2 uppercase">الكمية</label>
                <input type="number" step="any" wire:model="quantity" class="w-full border-2 border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none text-xl font-black text-center text-slate-800 bg-white transition-all shadow-sm" placeholder="0">
                @error('quantity') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-1">
                <label class="block text-xs font-black text-orange-600 mb-2 uppercase whitespace-nowrap">انتهاء الصلاحية</label>
                <input type="date" wire:model="expiry_date" class="w-full border-2 border-orange-200 bg-orange-50 p-4 rounded-2xl focus:ring-4 focus:ring-orange-100 focus:border-orange-500 outline-none text-center font-bold text-orange-800 transition-all shadow-sm">
                @error('expiry_date') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>
        </div>

        @if($selected_product)
        <div class="bg-white p-6 rounded-3xl border border-blue-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-6 animate-slide-in">
            <h4 class="font-black text-slate-700 mb-6 flex items-center gap-2">
                <span>💰</span> تحديث أسعار السوق للمنتج (اختياري)
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative group">
                    <label class="absolute -top-3 left-4 bg-rose-50 px-2 text-[10px] font-black text-rose-600 uppercase tracking-widest border border-rose-100 rounded-md z-10">تكلفة الشراء للوحدة (مال يخرج)</label>
                    <input type="number" step="any" wire:model="unit_cost_price" onclick="this.select()" class="w-full border-2 border-rose-200 bg-rose-50/50 p-5 rounded-2xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none text-2xl font-black text-center text-rose-700 transition-all shadow-inner">
                    @error('unit_cost_price') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="relative group">
                    <label class="absolute -top-3 left-4 bg-emerald-50 px-2 text-[10px] font-black text-emerald-600 uppercase tracking-widest border border-emerald-100 rounded-md z-10">سعر البيع قطاعي (مال يدخل)</label>
                    <input type="number" step="any" wire:model="new_unit_selling_price" onclick="this.select()" class="w-full border-2 border-emerald-200 bg-emerald-50/50 p-5 rounded-2xl focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 outline-none text-2xl font-black text-center text-emerald-700 transition-all shadow-inner">
                    @error('new_unit_selling_price') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            @if(count($wholesale_records) > 0)
            <div class="mt-8 pt-6 border-t border-slate-100">
                <label class="block text-[11px] font-black text-purple-600 uppercase tracking-widest mb-4">📦 تحديث تسعيرات الجملة المربوطة</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($wholesale_records as $record)
                    <div class="flex items-center bg-purple-50/50 border border-purple-100 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-purple-200">
                        <span class="bg-purple-100 text-purple-700 text-xs font-black px-4 py-3 border-l border-purple-200 whitespace-nowrap w-24 text-center">
                            {{ $record->unit->name }}
                        </span>
                        <input type="number" step="any" wire:model="wholesale_prices.{{ $record->id }}" onclick="this.select()" class="w-full bg-transparent p-3 outline-none text-center font-black text-purple-800 text-lg" placeholder="السعر">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <button wire:click="addItem" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
            <span>إضافة الصنف للفاتورة السفلية</span> <span>👇</span>
        </button>
        @endif
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8 border border-slate-100">
        <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-2 h-6 bg-slate-400 rounded-full"></span> تفاصيل أصناف الفاتورة 
            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-xs ml-auto">{{ count($cart) }} أصناف</span>
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-wider">
                        <th class="p-4 rounded-r-xl">المنتج</th>
                        <th class="p-4">الوحدة</th>
                        <th class="p-4 text-center">الكمية</th>
                        <th class="p-4 text-center">التكلفة للوحدة</th>
                        <th class="p-4 text-center text-emerald-600">سعر البيع المحدث</th>
                        <th class="p-4 text-center">الإجمالي الفرعي</th>
                        <th class="p-4 text-center text-orange-500">انتهاء الصلاحية</th>
                        <th class="p-4 text-center rounded-l-xl">إزالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($cart as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 font-black text-slate-800 text-sm">{{ $item['product_name'] }}</td>
                            <td class="p-4 text-xs font-bold text-slate-500 bg-slate-50/50">{{ $item['unit_name'] }}</td>
                            <td class="p-4 text-center font-black text-lg text-blue-600">{{ $item['quantity'] }}</td>
                            <td class="p-4 text-center font-bold text-rose-600">{{ number_format($item['unit_cost_price'], 0) }}</td>
                            <td class="p-4 text-center font-black text-emerald-600 bg-emerald-50/30">{{ number_format($item['new_unit_selling_price'], 0) }}</td>
                            <td class="p-4 text-center font-black text-slate-800 text-lg">{{ number_format($item['subtotal'], 0) }}</td>
                            <td class="p-4 text-center text-[10px] font-bold text-orange-600">
                                {{ $item['expiry_date'] ? date('Y-m-d', strtotime($item['expiry_date'])) : '---' }}
                            </td>
                            <td class="p-4 text-center">
                                <button wire:click="removeItem({{ $index }})" class="w-8 h-8 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-full transition-colors flex items-center justify-center mx-auto" title="إزالة الصنف">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-3xl grayscale opacity-30">🛒</span>
                                </div>
                                <p class="text-slate-400 font-bold">الفاتورة فارغة، قم بإضافة أصناف من القسم العلوي.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-900 p-6 sm:p-8 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] mb-8 text-white relative overflow-hidden">
        <h3 class="font-black text-slate-300 mb-8 flex items-center gap-2">
            <span class="w-2 h-6 bg-emerald-500 rounded-full"></span> تفاصيل السداد المالي للمورد
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-stretch relative z-10">
            
            <div class="bg-slate-800/80 p-6 rounded-3xl border border-slate-700 flex flex-col justify-center text-center group transition-colors hover:bg-slate-800">
                <span class="block text-[11px] font-black text-slate-400 mb-2 uppercase tracking-widest">إجمالي الفاتورة المطلوب</span>
                <span class="text-4xl font-black text-white" dir="ltr">{{ number_format($total_amount, 0) }}</span>
                <span class="text-slate-500 text-xs mt-1 font-bold">SDG</span>
            </div>

            <div class="lg:col-span-2 bg-slate-800/50 p-6 rounded-3xl border border-slate-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-emerald-400 mb-2 uppercase tracking-widest">💵 دفع نقداً (كاش)</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_cash" onclick="this.select()" class="w-full border-2 border-emerald-500/30 bg-emerald-900/20 p-4 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-black text-center text-emerald-300 text-xl transition-all" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-blue-400 mb-2 uppercase tracking-widest">📱 تحويل (بنكك)</label>
                        <input type="number" step="1" wire:model.live.debounce.300ms="paid_bankak" onclick="this.select()" class="w-full border-2 border-blue-500/30 bg-blue-900/20 p-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-black text-center text-blue-300 text-xl transition-all" placeholder="0">
                    </div>
                </div>
                
                @if($paid_bankak > 0)
                <div class="mt-5 animate-slide-in">
                    <input type="text" wire:model="transaction_number" class="w-full border border-blue-500/50 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-900/40 text-blue-100 placeholder-blue-300/50 font-bold" placeholder="أدخل رقم إشعار التحويل المرجعي...">
                </div>
                @endif
            </div>

            @php
                $remaining = $total_amount - ((float)$paid_cash + (float)$paid_bankak);
            @endphp
            <div class="bg-rose-900/20 p-6 rounded-3xl border {{ $remaining > 0 ? 'border-rose-500/50' : 'border-rose-900/50' }} flex flex-col justify-center text-center transition-colors">
                <span class="block text-[11px] font-black {{ $remaining > 0 ? 'text-rose-400' : 'text-slate-500' }} mb-2 uppercase tracking-widest">المتبقي (دين للمورد)</span>
                <span class="text-4xl font-black {{ $remaining > 0 ? 'text-rose-500' : 'text-slate-600' }}" dir="ltr">
                    {{ number_format($remaining, 0) }}
                </span>
                @if($remaining > 0)
                    <span class="text-[10px] bg-rose-500/20 text-rose-300 px-2 py-0.5 rounded-full mx-auto mt-2 font-bold animate-pulse">يُسجل في حساب المورد</span>
                @endif
            </div>

        </div>
    </div>

    <div class="flex justify-end mb-10">
        <button wire:click="savePurchase" class="w-full md:w-auto bg-emerald-500 hover:bg-emerald-600 text-white px-12 py-5 rounded-2xl font-black shadow-[0_10px_30px_rgba(16,185,129,0.3)] transition-all transform hover:-translate-y-1 text-xl flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" @if(empty($cart)) disabled @endif>
            <span>💾 تأكيد الدفع وحفظ فاتورة المشتريات</span>
            <span wire:loading wire:target="savePurchase" class="animate-spin text-white">⏳</span>
        </button>
    </div>

    @if($isSupplierModalOpen)
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.4)] w-full max-w-sm overflow-hidden transform transition-all animate-slide-in">
            
            <div class="bg-slate-900 p-6 flex justify-between items-center text-white border-b border-slate-800">
                <h3 class="text-lg font-black flex items-center gap-2">
                    <span class="text-blue-400">➕</span> إضافة مورد سريع
                </h3>
                <button wire:click="closeSupplierModal" class="w-8 h-8 bg-slate-800 rounded-full flex items-center justify-center hover:bg-rose-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50 space-y-5">
                <form wire:submit.prevent="saveNewSupplier" class="space-y-5">
                    
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-2 uppercase">اسم المندوب / المورد (إلزامي)</label>
                        <input type="text" wire:model="newSupplierName" autofocus class="w-full border-2 border-slate-200 bg-white p-3.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-black text-slate-800 shadow-sm transition-all" placeholder="مثال: شركة المراعي">
                        @error('newSupplierName') <span class="text-rose-500 text-xs font-bold block mt-2 bg-rose-50 px-2 py-1 rounded">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-2 uppercase">اسم الشركة (اختياري)</label>
                        <input type="text" wire:model="newSupplierCompany" class="w-full border-2 border-slate-200 bg-white p-3.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-800 shadow-sm transition-all" placeholder="الشركة الأم">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-2 uppercase">رقم الهاتف (اختياري)</label>
                        <input type="text" wire:model="newSupplierPhone" dir="ltr" class="w-full border-2 border-slate-200 bg-white p-3.5 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none font-bold text-slate-800 text-left tracking-widest shadow-sm transition-all" placeholder="0123456789">
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2 text-lg">
                            <span>حفظ واختيار المورد</span>
                        </button>
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
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>