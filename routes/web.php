<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\LoginScreen;
use App\Livewire\DashboardScreen;
use App\Livewire\PosScreen;
use App\Livewire\PurchaseScreen;
use App\Livewire\PriceUpdater;
use App\Livewire\CustomerLedger;
use App\Models\Sale;
use App\Livewire\CustomersList;
use App\Livewire\ReturnScreen;
use App\Livewire\InventoryScreen;
use App\Livewire\ProductManager;
use App\Livewire\ExpiryRadar;
use App\Livewire\ExpenseManager;
use App\Livewire\PurchaseHistory;

// === مسار تسجيل الدخول (غير محمي لكي يراه الجميع) === //
// يجب تسميته 'login' لأن Laravel يبحث عن هذا الاسم تلقائياً عند طرد المستخدم غير المسجل
Route::get('/login', LoginScreen::class)->name('login');

Route::get('/license-activate', \App\Livewire\LicenseScreen::class)->name('license.activate');


Route::get('/keygen/{machine_id}', function ($machine_id) {
    $developerSecret = 'MY_SUPER_ERP_SUDAN_2026'; // يجب أن تتطابق مع الموجودة في كود العميل
    $expectedKeyRaw = strtoupper(substr(md5($machine_id . $developerSecret), 0, 16));
    return implode('-', str_split($expectedKeyRaw, 4));
});


// === مسار تسجيل الخروج === //
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ==========================================
// مسارات محمية بالترخيص (CheckLicense)
// ==========================================
Route::middleware([\App\Http\Middleware\CheckLicense::class])->group(function () {
    
    // === مسارات مسموحة للجميع (الكاشير والمدير) بشرط تسجيل الدخول === //
    Route::middleware(['auth'])->group(function () {
        Route::get('/pos', PosScreen::class)->name('pos');
        
        Route::get('/receipt/{sale}', function (Sale $sale) {
            $sale->load('items.product', 'items.unit', 'customer'); 
            return view('receipt', compact('sale'));
        })->name('receipt.show');
    });


    // === مسارات للإدارة فقط (محمية بـ CheckAdminRole) === //
    Route::middleware(['auth', App\Http\Middleware\CheckAdminRole::class])->group(function () {
        Route::get('/', DashboardScreen::class)->name('dashboard'); // لوحة التحكم
        Route::get('/purchases', PurchaseScreen::class)->name('purchases'); // المشتريات
        Route::get('/purchases-history', PurchaseHistory::class)->name('purchases.history');
        Route::get('/price-updater', PriceUpdater::class)->name('prices.update'); // تحديث الأسعار
        Route::get('/customers', CustomersList::class)->name('customers.index');
        Route::get('/customers/{customer}/ledger', CustomerLedger::class)->name('customers.ledger'); // كشوفات الحساب
        Route::get('/returns', ReturnScreen::class)->name('returns'); // شاشة المرتجعات
        Route::get('/inventory', InventoryScreen::class)->name('inventory'); // شاشة المخزون
        Route::get('/products-manager', ProductManager::class)->name('products.manager'); // إدارة المنتجات
        Route::get('/expiry-radar', ExpiryRadar::class)->name('expiry.radar');
        Route::get('/expenses', ExpenseManager::class)->name('expenses');
        Route::get('/treasury', App\Livewire\TreasuryDashboard::class)->name('treasury');
        Route::get('/suppliers', \App\Livewire\SuppliersList::class)->name('suppliers.index');
    });

});