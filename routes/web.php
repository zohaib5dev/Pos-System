<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Installer;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\Actions\Logout;

use App\Livewire\Dashboard;

use App\Livewire\ActivityLog\Index as ActivityLogIndex;
use App\Livewire\ActivityLog\Show as ActivityLogShow;

use App\Livewire\Expense\Index as ExpenseIndex;
use App\Livewire\Expense\Form as ExpenseForm;
use App\Livewire\Expense\Show as ExpenseShow;

use App\Livewire\Brand\Index as BrandIndex;
use App\Livewire\Brand\Form as BrandForm;

use App\Livewire\Category\Index as CategoryIndex;
use App\Livewire\Category\Form as CategoryForm;

use App\Livewire\Product\Index as ProductIndex;
use App\Livewire\Product\Form as ProductForm;
use App\Livewire\Product\Show as ProductShow;

use App\Livewire\Customer\Index as CustomerIndex;
use App\Livewire\Customer\Form as CustomerForm;
use App\Livewire\Customer\Show as CustomerShow;

use App\Livewire\Supplier\Index as SupplierIndex;
use App\Livewire\Supplier\Form as SupplierForm;
use App\Livewire\Supplier\Show as SupplierShow;
use App\Livewire\Supplier\Ledger as SupplierLedger;

use App\Livewire\Purchase\Index as PurchaseIndex;
use App\Livewire\Purchase\Form as PurchaseForm;
use App\Livewire\Purchase\Show as PurchaseShow;
use App\Livewire\Purchase\Receive as PurchaseReceive;

use App\Livewire\Order\Index as OrderIndex;
use App\Livewire\Order\Form as OrderForm;
use App\Livewire\Order\Show as OrderShow;
use App\Livewire\Order\Invoice as OrderInvoice;

use App\Livewire\Inventory\Index as InventoryIndex;
use App\Livewire\Inventory\Adjustments as InventoryAdjustments;
use App\Livewire\Inventory\LowStock as InventoryLowStock;
use App\Livewire\Inventory\Count as InventoryCount;

use App\Livewire\Report\Index as ReportIndex;
use App\Livewire\Setting\Index as SettingIndex;




$installed = Storage::disk('public')->exists('installed');

if ($installed === false) {
    Route::get('/', function () {
        return redirect('installer');
    });

    Route::prefix('installer')->name('installer.')->group(function () {
        Route::get('/', [Installer::class, 'showApplicationSettings'])->name('applicationSettings');
        Route::post('/application', [Installer::class, 'saveApplicationSettings'])->name('saveApplicationSettings');

        Route::get('/database', [Installer::class, 'showDatabaseSettings'])->name('showDatabaseSettings');
        Route::post('/database', [Installer::class, 'saveDatabaseSettings'])->name('saveDatabaseSettings');

        Route::get('/review', [Installer::class, 'reviewSettings'])->name('reviewSettings');
        Route::post('/finalize', [Installer::class, 'finalizeSetup'])->name('finalizeSetup');

        Route::get('/generate-key', [Installer::class, 'generateAppKey'])->name('generateAppKey');
    });
}


Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
    Route::post('/logout', Logout::class)->name('logout');
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('can:dashboard');

    // POS Routes - accessible by cashier and above
    Route::prefix('pos')->name('pos.')->middleware(['can:pos'])->group(function () {
        Route::get('/', function () {
            return view('pos');
        })->name('index');
        Route::get('receipt/{order}', [ReceiptController::class, 'show'])->name('receipt')->middleware('can:pos');
    });

    // PRODUCT Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', ProductIndex::class)->name('index')->middleware('can:view products');
        Route::get('/create', ProductForm::class)->name('create')->middleware('can:create products');
        Route::get('/{id}/edit', ProductForm::class)->name('edit')->middleware('can:edit products');
        Route::get('/show/{id}', ProductShow::class)->name('show')->middleware('can:view products');
        Route::get('/{action}/{id?}', ProductIndex::class)->name('actions')->middleware('can:view products');
    });

    // BRAND Management
    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', BrandIndex::class)->name('index')->middleware('can:view brands');
        Route::get('/create', BrandForm::class)->name('create')->middleware('can:create brands');
        Route::get('/{id}/edit', BrandForm::class)->name('edit')->middleware('can:edit brands');
        Route::get('/{action}/{id?}', BrandIndex::class)->name('actions')->middleware('can:view brands');
    });

    // CATEGORY Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', CategoryIndex::class)->name('index')->middleware('can:view categories');
        Route::get('/create', CategoryForm::class)->name('create')->middleware('can:create categories');
        Route::get('/{id}/edit', CategoryForm::class)->name('edit')->middleware('can:edit categories');
        Route::get('/{action}/{id?}', CategoryIndex::class)->name('actions')->middleware('can:view categories');
    });

    // ORDER Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', OrderIndex::class)->name('index')->middleware('can:view orders');
        Route::get('/create', OrderForm::class)->name('create')->middleware('can:create orders');
        Route::get('/{id}', OrderShow::class)->name('show')->middleware('can:view orders');
        Route::get('/{id}/edit', OrderForm::class)->name('edit')->middleware('can:edit orders');
        Route::get('/process/{id}', OrderIndex::class)->name('process')->middleware('can:process orders');
        Route::get('/cancel/{id}', OrderIndex::class)->name('cancel')->middleware('can:cancel orders');
        Route::get('/invoice/{id}', OrderInvoice::class)->name('invoice')->middleware('can:view orders');
        Route::get('/{action}/{id?}', OrderIndex::class)->name('actions')->middleware('can:view orders');
    });

    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', CustomerIndex::class)->name('index')->middleware('can:view customers');
        Route::get('/create', CustomerForm::class)->name('create')->middleware('can:create customers');
        Route::get('/{id}', CustomerShow::class)->name('show')->middleware('can:view customers');
        Route::get('/{id}/edit', CustomerForm::class)->name('edit')->middleware('can:edit customers');
        Route::get('/ledger/{id}', CustomerIndex::class)->name('ledger')->middleware('can:view customers');
        Route::get('/{action}/{id?}', CustomerIndex::class)->name('actions')->middleware('can:view customers');
    });

    // Supplier Management
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', SupplierIndex::class)->name('index')->middleware('can:view suppliers');
        Route::get('/create', SupplierForm::class)->name('create')->middleware('can:create suppliers');
        Route::get('/{id}', SupplierShow::class)->name('show')->middleware('can:view suppliers');
        Route::get('/edit/{id}', SupplierForm::class)->name('edit')->middleware('can:edit suppliers');
        Route::get('/ledger/{id}', SupplierLedger::class)->name('ledger')->middleware('can:view suppliers');
        Route::get('/{action}/{id?}', SupplierIndex::class)->name('actions')->middleware('can:view suppliers');
    });

    // Purchase Management
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', PurchaseIndex::class)->name('index')->middleware('can:view purchases');
        Route::get('/create', PurchaseForm::class)->name('create')->middleware('can:create purchases');
        Route::get('/{id}', PurchaseShow::class)->name('show')->middleware('can:view purchases');
        Route::get('/edit/{id}', PurchaseForm::class)->name('edit')->middleware('can:edit purchases');
        Route::get('/receive/{id}', PurchaseReceive::class)->name('receive')->middleware('can:receive purchases');
        Route::get('/{action}/{id?}', PurchaseIndex::class)->name('actions')->middleware('can:view purchases');
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', InventoryIndex::class)->name('index')->middleware('can:view products');
        Route::get('/adjustments', InventoryAdjustments::class)->name('adjustments')->middleware('can:manage stock');
        Route::get('/low-stock', InventoryLowStock::class)->name('low-stock')->middleware('can:view products');
        Route::get('/count', InventoryCount::class)->name('count')->middleware('can:manage stock');
        Route::get('/adjust/{id}', InventoryIndex::class)->name('adjust')->middleware('can:manage stock');
        Route::get('/{action}', InventoryIndex::class)->name('actions')->middleware('can:view products');
    });

    // EXPENSE Management
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', ExpenseIndex::class)->name('index')->middleware('can:view expenses');
        Route::get('create', ExpenseForm::class)->name('create')->middleware('can:view expenses');
        Route::get('/{id}/edit', ExpenseForm::class)->name('edit')->middleware('can:view expenses'); // Changed to {id}
        Route::get('/{id}', ExpenseShow::class)->name('show')->middleware('can:view expenses'); // Changed to {id}
    });

    // Report Management
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', ReportIndex::class)->name('index')->middleware('can:view reports');
        Route::get('/{type}', ReportIndex::class)->name('type')->middleware('can:view reports');
    });

    // SETTINGS
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', SettingIndex::class)->name('index')->middleware('can:manage settings');
        Route::get('/{type}', SettingIndex::class)->name('type')->middleware('can:manage settings');
    });

    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', ActivityLogIndex::class)->name('index')->middleware('can:view activity logs');
        Route::get('/{id}', ActivityLogShow::class)->name('show')->middleware('can:view activity logs');
    });
});

// FALLBACK 
Route::fallback(function () {
    return view('errors.404');
});

require __DIR__ . '/auth.php';



Route::middleware(['auth'])->group(function () {
    Route::post('pos/sync-offline-order', [POSController::class, 'syncOfflineOrder']);
    Route::post('pos/products/cache', [POSController::class, 'productsCache']);
    Route::post('pos/categories/cache', [POSController::class, 'categoriesCache']);
    Route::post('pos/customers/cache', [POSController::class, 'customersCache']);
    Route::post('pos/customers/create', [POSController::class, 'createCustomer']);
});
