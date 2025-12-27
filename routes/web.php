<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerAccountTransactionController;

// Restaurant Module Controllers
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\QuickController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\IncomeTypeController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\PromissoryNoteController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\CustomerDebtController;

// Middleware
use App\Http\Middleware\AuthMiddleware;

/*
|--------------------------------------------------------------------------
| Development Routes (Geliştirme için) - Sadece local ortamda aktif
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/migrate', function () {
        $controller_name = "FeedbackController";
        Artisan::call('make:controller', ['name' => $controller_name]);
        return "Controller created successfully";
    });

    Route::get('/makeview', function () {
        $view_name = "educational_videos";
        Artisan::call('make:view', ['name' => $view_name]);
        return "View created successfully";
    });

    Route::get('/cache-clear', function () {
        Artisan::call('cache:clear');
        return "Cache cleared successfully";
    });
}




Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('login.post');
    Route::post('/password-reset', [AuthController::class, 'showPasswordReset'])->name('password-reset');
    Route::get('/getSalonInformation', [SettingsController::class, 'getSalonInformation'])->name('getSalonInformation');
});



Route::middleware([AuthMiddleware::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sales', [DashboardController::class, 'sales'])->name('sales');
    Route::get('/sales/{id}', [DashboardController::class, 'getSaleDetails'])->name('sale.details');
    Route::get('/ajax/sales-data', [DashboardController::class, 'getSalesData'])->name('sales.data');
    Route::get('/calculate-stock', [DashboardController::class, 'calculateStock'])->name('calculate.stock');


    Route::post('/clear-cache', [DashboardController::class, 'clearCache'])->name('clear.cache');

  
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    
    // Restaurant Module Index Pages
    Route::get('/menu', [DashboardController::class, 'menu'])->name('menu.index');
    Route::get('/tables', [DashboardController::class, 'tables'])->name('tables.index');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');
    Route::get('/kitchen', [DashboardController::class, 'kitchen'])->name('kitchen.index');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    
    Route::prefix('settings')->group(function () {
        Route::post('/sms', [SettingsController::class, 'updateSMS'])->name('settings.sms');
        Route::post('/sms/update', [SettingsController::class, 'updateSMS'])->name('settings.sms.update');
        Route::post('/update', [SettingsController::class, 'updateSettings'])->name('settings.update');
        //settings.sent-sms-data
        Route::get('/sent-sms-data', [SettingsController::class, 'sentSMSData'])->name('settings.sent-sms-data');
    });

    /*
    |--------------------------------------------------------------------------
    | User Management (Kullanıcı Yönetimi) - Admin only
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->group(function () {
        Route::get('/', [DashboardController::class, 'users'])->name('users');
        Route::post('/add', [UsersController::class, 'addUser'])->name('users.add');
        Route::post('/update', [UsersController::class, 'updateUser'])->name('users.update');
        Route::post('/delete', [UsersController::class, 'deleteUser'])->name('users.delete');
        Route::post('/groups/add', [UsersController::class, 'addGroup'])->name('users.groups.add');
        Route::post('/update-permissions', [UsersController::class, 'updatePermissions'])->name('users.update-permissions');
        Route::get('/get-permissions/{id}', [UsersController::class, 'getPermissions'])->name('users.get-permissions');
    });

    /*
    |--------------------------------------------------------------------------
    | Employee Management (Personel Yönetimi) - Admin/Manager only
    |--------------------------------------------------------------------------
    */
    Route::prefix('personeller')->group(function () {
        Route::get('/', [DashboardController::class, 'employees'])->name('employees');
        Route::get('/create', [DashboardController::class, 'employeesCreate'])->name('employees.create');
        Route::get('/{id}/edit', [DashboardController::class, 'employeesEdit'])->name('employees.edit');
        Route::get('/list', [DashboardController::class, 'employeesListJson'])->name('employees.list');
        Route::get('/specialties', [DashboardController::class, 'specialties'])->name('employees.specialties');

        // Employee CRUD
        Route::post('/add', [EmployeesController::class, 'add'])->name('employees.add');
        Route::post('/update', [EmployeesController::class, 'update'])->name('employees.update');
        Route::post('/delete', [EmployeesController::class, 'delete'])->name('employees.delete');
        Route::post('/add-commission', [EmployeesController::class, 'addCommission'])->name('employees.add-commission');

        // Specialties
        Route::post('/specialties/add', [EmployeesController::class, 'addSpecialty'])->name('employees.specialties.add');
        Route::post('/specialties/update', [EmployeesController::class, 'updateSpecialty'])->name('employees.specialties.update');
        Route::post('/specialties/delete', [EmployeesController::class, 'deleteSpecialty'])->name('employees.specialties.delete');
    });

    // Employee AJAX Routes
    Route::post('/employees/add/ajax', [EmployeesController::class, 'addAjax'])->name('employees.add.ajax');
    Route::post('/personeller/list', [AjaxController::class, 'getEmployeesList'])->name('employees.list.ajax');
    Route::get('/personeller/modals/{id}', [AjaxController::class, 'getEmployeeModals'])->name('employees.modals');
    
    // AJAX: Grup görevleri
    Route::get('/personeller/positions-by-group', [AjaxController::class, 'getPositionsByGroup'])
        ->name('employees.positions.by.group');
    
    // Yeni bordro routes
    Route::prefix('bordrolar')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('payrolls.index');
        Route::get('/create', [PayrollController::class, 'create'])->name('payrolls.create');
        Route::post('/', [PayrollController::class, 'store'])->name('payrolls.store');
        Route::get('/{id}', [PayrollController::class, 'show'])->name('payrolls.show');
        Route::post('/{id}/payment', [PayrollController::class, 'addPayment'])->name('payrolls.add.payment');
        Route::post('/{id}/cancel', [PayrollController::class, 'cancel'])->name('payrolls.cancel');
        Route::post('/{id}/delete', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
        Route::post('/{id}/payment/{paymentId}/delete', [PayrollController::class, 'deletePayment'])->name('payrolls.payment.delete');
        Route::post('/{id}/deduction', [PayrollController::class, 'addDeduction'])->name('payrolls.add.deduction');
        Route::post('/{id}/deduction/{deductionId}/delete', [PayrollController::class, 'deleteDeduction'])->name('payrolls.deduction.delete');
        Route::post('/{id}/adjust', [PayrollController::class, 'adjustAmount'])->name('payrolls.adjust');
    });

    // HR Grup ve Pozisyon Yönetimi
    Route::prefix('hr')->group(function () {
        // Gruplar
        Route::prefix('groups')->group(function () {
            Route::get('/', [App\Http\Controllers\EmployeeGroupsController::class, 'index'])->name('hr.groups.index');
            Route::get('/create', [App\Http\Controllers\EmployeeGroupsController::class, 'create'])->name('hr.groups.create');
            Route::post('/', [App\Http\Controllers\EmployeeGroupsController::class, 'store'])->name('hr.groups.store');
            Route::get('/{id}/edit', [App\Http\Controllers\EmployeeGroupsController::class, 'edit'])->name('hr.groups.edit');
            Route::post('/{id}', [App\Http\Controllers\EmployeeGroupsController::class, 'update'])->name('hr.groups.update');
            Route::post('/{id}/delete', [App\Http\Controllers\EmployeeGroupsController::class, 'destroy'])->name('hr.groups.destroy');
        });
        
        // Pozisyonlar
        Route::prefix('positions')->group(function () {
            Route::get('/', [App\Http\Controllers\EmployeePositionsController::class, 'index'])->name('hr.positions.index');
            Route::get('/create', [App\Http\Controllers\EmployeePositionsController::class, 'create'])->name('hr.positions.create');
            Route::post('/', [App\Http\Controllers\EmployeePositionsController::class, 'store'])->name('hr.positions.store');
            Route::get('/{id}/edit', [App\Http\Controllers\EmployeePositionsController::class, 'edit'])->name('hr.positions.edit');
            Route::post('/{id}', [App\Http\Controllers\EmployeePositionsController::class, 'update'])->name('hr.positions.update');
            Route::post('/{id}/delete', [App\Http\Controllers\EmployeePositionsController::class, 'destroy'])->name('hr.positions.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Service Management (Hizmet Yönetimi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('services')->group(function () {
        Route::get('/', [DashboardController::class, 'services'])->name('services');
        Route::get('/data', [DashboardController::class, 'getServicesData'])->name('services.data');
        Route::get('/edit/{id}', [DashboardController::class, 'ServicesEditModal'])->name('services.edit');

        // Service CRUD - All methods now return JSON
        Route::post('/add', [ServicesController::class, 'add'])->name('services.add');
        Route::post('/', [ServicesController::class, 'update'])->name('services.update');
        Route::post('/delete', [ServicesController::class, 'delete'])->name('services.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Product Management (Ürün Yönetimi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->group(function () {
        Route::get('/', [DashboardController::class, 'products'])->name('products');
        Route::post('/add', [ServicesController::class, 'add'])->name('products.add');
        Route::post('/update', [ServicesController::class, 'update'])->name('products.update');
        Route::post('/delete', [ServicesController::class, 'delete'])->name('products.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Category Management (Kategori Yönetimi) - OLD ROUTES REMOVED
    |--------------------------------------------------------------------------
    */

   


    Route::prefix('pos')->group(function () {
        Route::get('/', [DashboardController::class, 'pos'])->name('pos');
        Route::post('/add', [PosController::class, 'addSale'])->name('pos.addSale');
    });

    Route::prefix('possales')->group(function () {
        Route::get('/', [DashboardController::class, 'posSale'])->name('possales');
        Route::get('/ajax', [PosController::class, 'getSalesAjax'])->name('possales.ajax');
        Route::post('/add', [PosController::class, 'add'])->name('possales.add');
        Route::post('/', [PosController::class, 'update'])->name('possales.update');
        Route::get('/delete/{id}', [PosController::class, 'delete'])->name('possales.delete');
    });


    Route::prefix('warehouse')->group(function () {
        Route::get('/', [DashboardController::class, 'warehouse'])->name('warehouse');
        Route::get('/get', [WarehouseController::class, 'getWarehouses'])->name('warehouse.get');
        Route::get('/details/{id}', [WarehouseController::class, 'getWarehouseDetails'])->name('warehouse.details');
        Route::get('/services/{id}', [WarehouseController::class, 'getWarehouseServices'])->name('warehouse.services');
        Route::post('/add', [WarehouseController::class, 'addWarehouse'])->name('warehouse.add');
        Route::post('/update', [WarehouseController::class, 'updateWarehouse'])->name('warehouse.update');
        Route::get('/delete/{id}', [WarehouseController::class, 'deleteWarehouse'])->name('warehouse.delete');
    });

   
 
    Route::prefix('kampanyalar')->group(function () {
        Route::get('/', [DashboardController::class, 'campaigns'])->name('campaigns');
        Route::post('/add', [CampaignsController::class, 'add'])->name('campaigns.add');
        Route::post('/delete', [CampaignsController::class, 'delete'])->name('campaigns.delete');
    });


    Route::prefix('expenses')->group(function () {
        Route::get('/', [DashboardController::class, 'expenses'])->name('expenses');
        Route::get('/giderler/listesi', [DashboardController::class, 'expensesList'])->name('expenses.list');
        Route::get('/get/{id}', [ExpensesController::class, 'getExpense'])->name('expenses.get');
        Route::post('/add', [ExpensesController::class, 'addExpense'])->name('expenses.add');
        Route::post('/update', [ExpensesController::class, 'updateExpense'])->name('expenses.update');
        Route::get('/delete/{id}', [ExpensesController::class, 'deleteExpense'])->name('expenses.delete');
    });

    // Expense Management Routes (Standard CRUD)
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [ExpensesController::class, 'show'])->name('expenses.show');
    Route::get('/expenses/{id}/edit', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');

    // Enhanced Customer Management Routes
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::get('/{id}/balance', [CustomerController::class, 'getBalance'])->name('customers.balance');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('customers.show');
        
        // Customer Transactions
        Route::get('/{customerId}/transactions', [CustomerAccountTransactionController::class, 'index'])->name('customers.transactions.index');
        Route::get('/{customerId}/transactions/create', [CustomerAccountTransactionController::class, 'create'])->name('customers.transactions.create');
        Route::post('/{customerId}/transactions', [CustomerAccountTransactionController::class, 'store'])->name('customers.transactions.store');
        Route::get('/{customerId}/transactions/{transactionId}/edit', [CustomerAccountTransactionController::class, 'edit'])->name('customers.transactions.edit');
        Route::put('/{customerId}/transactions/{transactionId}', [CustomerAccountTransactionController::class, 'update'])->name('customers.transactions.update');
        Route::delete('/{customerId}/transactions/{transactionId}', [CustomerAccountTransactionController::class, 'destroy'])->name('customers.transactions.destroy');
        Route::get('/{customerId}/transactions/{transactionId}', [CustomerAccountTransactionController::class, 'getTransaction'])->name('customers.transactions.get');
    });

    // Hızlı gelir girişi için özel route
    Route::post('/quick-income', [CustomerAccountTransactionController::class, 'quickIncome'])->name('quick.income');
    Route::post('/quick-expense', [CustomerAccountTransactionController::class, 'quickExpense'])->name('quick.expense');
    
    // AJAX endpoint for quick access data
    Route::get('/quick-access-data', [DashboardController::class, 'quickAccessData'])->name('quick.access.data');

    // Simplified Check Management Routes
    Route::get('/checks', [CheckController::class, 'index'])->name('checks.index');
    Route::get('/checks/create', [CheckController::class, 'create'])->name('checks.create');
    Route::post('/checks', [CheckController::class, 'store'])->name('checks.store');
    Route::get('/checks/{id}', [CheckController::class, 'show'])->name('checks.show');
    Route::get('/checks/{id}/edit', [CheckController::class, 'edit'])->name('checks.edit');
    Route::put('/checks/{id}', [CheckController::class, 'update'])->name('checks.update');
    Route::delete('/checks/{id}', [CheckController::class, 'destroy'])->name('checks.destroy');

    // Simplified Promissory Note Management Routes
    Route::get('/promissory-notes', [PromissoryNoteController::class, 'index'])->name('promissory_notes.index');
    Route::get('/promissory-notes/create', [PromissoryNoteController::class, 'create'])->name('promissory_notes.create');
    Route::post('/promissory-notes', [PromissoryNoteController::class, 'store'])->name('promissory_notes.store');
    Route::get('/promissory-notes/{id}', [PromissoryNoteController::class, 'show'])->name('promissory_notes.show');
    Route::get('/promissory-notes/{id}/edit', [PromissoryNoteController::class, 'edit'])->name('promissory_notes.edit');
    Route::put('/promissory-notes/{id}', [PromissoryNoteController::class, 'update'])->name('promissory_notes.update');
    Route::delete('/promissory-notes/{id}', [PromissoryNoteController::class, 'destroy'])->name('promissory_notes.destroy');

    // Income Management Routes
    Route::get('/incomes', [App\Http\Controllers\IncomeController::class, 'index'])->name('incomes.index');
    Route::get('/incomes/create', [App\Http\Controllers\IncomeController::class, 'create'])->name('incomes.create');
    Route::post('/incomes', [App\Http\Controllers\IncomeController::class, 'store'])->name('incomes.store');
    Route::get('/incomes/{id}', [App\Http\Controllers\IncomeController::class, 'show'])->name('incomes.show');
    Route::get('/incomes/{id}/edit', [App\Http\Controllers\IncomeController::class, 'edit'])->name('incomes.edit');
    Route::put('/incomes/{id}', [App\Http\Controllers\IncomeController::class, 'update'])->name('incomes.update');
    Route::delete('/incomes/{id}', [App\Http\Controllers\IncomeController::class, 'destroy'])->name('incomes.destroy');
    Route::get('/incomes/delete/{id}', [App\Http\Controllers\IncomeController::class, 'deleteIncome'])->name('incomes.delete');

    // Expense Type Management Routes
    Route::get('/expense_types', [App\Http\Controllers\ExpenseTypeController::class, 'index'])->name('expense_types.index');
    Route::get('/expense_types/create', [App\Http\Controllers\ExpenseTypeController::class, 'create'])->name('expense_types.create');
    Route::post('/expense_types', [App\Http\Controllers\ExpenseTypeController::class, 'store'])->name('expense_types.store');
    Route::get('/expense_types/{id}', [App\Http\Controllers\ExpenseTypeController::class, 'show'])->name('expense_types.show');
    Route::get('/expense_types/{id}/edit', [App\Http\Controllers\ExpenseTypeController::class, 'edit'])->name('expense_types.edit');
    Route::put('/expense_types/{id}', [App\Http\Controllers\ExpenseTypeController::class, 'update'])->name('expense_types.update');
    Route::delete('/expense_types/{id}', [App\Http\Controllers\ExpenseTypeController::class, 'destroy'])->name('expense_types.destroy');

    // Expense Category Management Routes
    Route::get('/expense_categories', [App\Http\Controllers\ExpenseCategoryController::class, 'index'])->name('expense_categories.index');
    Route::get('/expense_categories/create', [App\Http\Controllers\ExpenseCategoryController::class, 'create'])->name('expense_categories.create');
    Route::post('/expense_categories', [App\Http\Controllers\ExpenseCategoryController::class, 'store'])->name('expense_categories.store');
    Route::get('/expense_categories/{id}', [App\Http\Controllers\ExpenseCategoryController::class, 'show'])->name('expense_categories.show');
    Route::get('/expense_categories/{id}/edit', [App\Http\Controllers\ExpenseCategoryController::class, 'edit'])->name('expense_categories.edit');
    Route::put('/expense_categories/{id}', [App\Http\Controllers\ExpenseCategoryController::class, 'update'])->name('expense_categories.update');
    Route::delete('/expense_categories/{id}', [App\Http\Controllers\ExpenseCategoryController::class, 'destroy'])->name('expense_categories.destroy');

    // Income Type Management Routes
    Route::get('/income_types', [IncomeTypeController::class, 'index'])->name('income_types.index');
    Route::get('/income_types/create', [IncomeTypeController::class, 'create'])->name('income_types.create');
    Route::post('/income_types', [IncomeTypeController::class, 'store'])->name('income_types.store');
    Route::get('/income_types/{id}/edit', [IncomeTypeController::class, 'edit'])->name('income_types.edit');
    Route::get('/income_types/{id}/delete', [IncomeTypeController::class, 'destroy'])->name('income_types.delete');
    Route::get('/income_types/{id}', [IncomeTypeController::class, 'show'])->name('income_types.show');
    Route::put('/income_types/{id}', [IncomeTypeController::class, 'update'])->name('income_types.update');
    Route::delete('/income_types/{id}', [IncomeTypeController::class, 'destroy'])->name('income_types.destroy');
    Route::post('/income_types/{id}/toggle-status', [IncomeTypeController::class, 'toggleStatus'])->name('income_types.toggle-status');

    // Income Category Management Routes
    Route::get('/income_categories', [IncomeCategoryController::class, 'index'])->name('income_categories.index');
    Route::get('/income_categories/create', [IncomeCategoryController::class, 'create'])->name('income_categories.create');
    Route::post('/income_categories', [IncomeCategoryController::class, 'store'])->name('income_categories.store');
    Route::get('/income_categories/{id}', [IncomeCategoryController::class, 'show'])->name('income_categories.show');
    Route::get('/income_categories/{id}/edit', [IncomeCategoryController::class, 'edit'])->name('income_categories.edit');
    Route::put('/income_categories/{id}', [IncomeCategoryController::class, 'update'])->name('income_categories.update');
    Route::delete('/income_categories/{id}', [IncomeCategoryController::class, 'destroy'])->name('income_categories.destroy');
    Route::post('/income_categories/{id}/toggle-status', [IncomeCategoryController::class, 'toggleStatus'])->name('income_categories.toggle-status');

    Route::prefix('bank')->group(function () {
        Route::get('/hesaplar', [DashboardController::class, 'accounts'])->name('accounts');
        Route::get('/hesaplar/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('/hesaplar', [AccountController::class, 'store'])->name('accounts.store');
        Route::get('/hesaplar/{id}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('/hesaplar/{id}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('/hesaplar/{id}', [AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::get('/transaction/{id}', [AccountController::class, 'ajaxTransaction'])->name('bank.transaction');
        // Eski route'lar (geriye dönük uyumluluk için)
        Route::post('/add', [AccountController::class, 'add'])->name('bank.add');
        Route::post('/', [AccountController::class, 'update'])->name('bank.update');
        Route::delete('/delete/{id}', [AccountController::class, 'delete'])->name('bank.delete');
    });

    // Account Transactions Routes
    Route::prefix('account-transactions')->name('account-transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\AccountTransactionController::class, 'index'])->name('index');
        Route::get('/{id}/get', [App\Http\Controllers\AccountTransactionController::class, 'getTransaction'])->name('get');
        Route::put('/{id}', [App\Http\Controllers\AccountTransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AccountTransactionController::class, 'destroy'])->name('destroy');
    });

 
    Route::prefix('payment')->group(function () {
        Route::post('/add', [PaymentController::class, 'addPayment'])->name('payment.add');
        Route::post('/installment', [PaymentController::class, 'processInstallmentPayment'])->name('payment.installment');
        Route::post('/sellProduct', [PaymentController::class, 'sellProduct'])->name('payment.sellProduct');
    });

 
    Route::prefix('quick_sale')->group(function () {
        Route::post('/add', [QuickController::class, 'add'])->name('quick_sale_add');
    });

 
    Route::prefix('feedback')->group(function () {
        Route::get('/', [DashboardController::class, 'feedback'])->name('feedbacks');
        Route::get('/data', [FeedbackController::class, 'feedbackData'])->name('feedback.data');
        Route::post('/add', [FeedbackController::class, 'addFeedback'])->name('feedback.add');
        Route::post('/reply', [FeedbackController::class, 'replyFeedback'])->name('feedback.reply');
        Route::post('/delete', [FeedbackController::class, 'deleteFeedback'])->name('feedback.delete');
    });



 
    Route::prefix('export')->group(function () {
        Route::get('/expenses.pdf/{ids}', [ExportController::class, 'expensesPDF'])->name('expenses.pdf');
    });


 

    Route::get('/sales/cancel/{id}', [SalesController::class, 'cancelOrder'])->name('sales.cancel');
    Route::get('/parapuan/settings/{coin}', [AjaxController::class, 'getParapuanSettings'])->name('parapuan.settings');

    Route::post('/createInvoice', [SalesController::class, 'createInvoice'])->name('createInvoice');

    /*
    |--------------------------------------------------------------------------
    | Loyalty Program (Sadakat Programı)
    |--------------------------------------------------------------------------
    */
    Route::prefix('loyalty')->group(function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('loyalty');
        Route::post('/add-points', [LoyaltyController::class, 'addPoints'])->name('loyalty.add-points');
        Route::post('/redeem-points', [LoyaltyController::class, 'redeemPoints'])->name('loyalty.redeem-points');
        Route::get('/customer/{id}', [LoyaltyController::class, 'getCustomerLoyalty'])->name('loyalty.customer');
        Route::get('/settings', [LoyaltyController::class, 'getSettings'])->name('loyalty.settings');
        Route::post('/settings', [LoyaltyController::class, 'updateSettings'])->name('loyalty.settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Staff Shifts (Vardiya Yönetimi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('shifts')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('shifts');
        Route::post('/', [ShiftController::class, 'store'])->name('shifts.store');
        Route::put('/{id}/status', [ShiftController::class, 'updateStatus'])->name('shifts.update-status');
        Route::get('/employee/{id}', [ShiftController::class, 'getEmployeeShifts'])->name('shifts.employee');
        Route::get('/today', [ShiftController::class, 'getTodayShifts'])->name('shifts.today');
        Route::get('/stats', [ShiftController::class, 'getShiftStats'])->name('shifts.stats');
        Route::delete('/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications (Bildirim Sistemi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/', [NotificationController::class, 'store'])->name('notifications.store');
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread');
        Route::get('/stats', [NotificationController::class, 'getStats'])->name('notifications.stats');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
}); 


Route::middleware(['auth', 'throttle:60,1'])->prefix('ajax')->group(function () {
    // Employee Related AJAX
    Route::get('/masa-calisanlari/{id}', [AjaxController::class, 'findEmployee'])->name('ajax.find-employee');
    Route::get('/process-employee/{id}', [AjaxController::class, 'findProcessEmployee'])->name('ajax.find-process-employee');
    Route::get('/getEmployees', [AjaxController::class, 'getEmployees'])->name('getEmployees');
    Route::get('/getEmployeeHistoryModal/{id}', [AjaxController::class, 'getEmployeeHistoryModal'])->name('getEmployeeHistoryModal');
    Route::post('/markCommissionsPaid', [EmployeesController::class, 'markCommissionsPaid'])->name('ajax.markCommissionsPaid');




    // Room & Table Related AJAX
    Route::get('/getRooms', [AjaxController::class, 'getRooms'])->name('getRooms');

    // Service & Product Related AJAX
    Route::get('/get-services', [AjaxController::class, 'getServices'])->name('get-services');
    Route::get('/getAllServices', [AjaxController::class, 'getAllServices'])->name('getAllServices');
    Route::get('/getisAvailableServices', [AjaxController::class, 'getisAvailableServices'])->name('getisAvailableServices');
    Route::get('/get-products', [AjaxController::class, 'getProducts'])->name('get-products');
    Route::get('/get-product-row', [AjaxController::class, 'getProductRow'])->name('get-product-row');
    Route::get('/get-product-details/{id}', [AjaxController::class, 'getProductDetails'])->name('get-product-details');
    Route::get('/getStockMovements/{id}', [AjaxController::class, 'getStockMovements'])->name('getStockMovements');
    Route::post('/addStockMovement', [ServicesController::class, 'addStockMovement'])->name('addStockMovement');

    // Sales Related AJAX
    Route::get('/get-sellers', [AjaxController::class, 'getSellers'])->name('get-sellers');
    Route::get('/SaleItems/{id}', [AjaxController::class, 'SaleItems'])->name('SaleItems');

    // Expense Related AJAX
    Route::get('/expense-items/{id}', [AjaxController::class, 'getExpenseItems'])->name('get-expense-items');

    // Reports Related AJAX
    Route::get('/getReportData/{type}', [ReportsController::class, 'getReportData'])->name('getReportData');
    Route::get('/getReportStats', [ReportsController::class, 'getStats'])->name('ajax.getReportStats');

    // SMS & Recommendations
    Route::get('/smsServices', [AjaxController::class, 'smsServices'])->name('smsServices');
    Route::get('/getRecommendations', [AjaxController::class, 'getRecommendations'])->name('recommendations');
});


// Debug routes - Sadece local ortamda aktif
if (app()->environment('local')) {
    Route::middleware(['auth', 'throttle:10,1'])->group(function () {
        Route::get('/ajax/test-commission-payment', function () {
            return response()->json([
                'success' => true,
                'message' => 'Commission payment endpoint is working',
                'employee_commissions_count' => DB::table('employee_commissions')->count(),
                'sample_commission' => DB::table('employee_commissions')->first()
            ]);
        });


        Route::get('/ajax/debug-tables', function () {
            try {
                $employees = DB::table('employees')->count();
                $commissions = DB::table('employee_commissions')->count();
                $reservations = 0;
                $services = DB::table('menu_items')->count();

                return response()->json([
                    'status' => 'success',
                    'table_counts' => [
                        'employees' => $employees,
                        'employee_commissions' => $commissions,
                        'services' => $services
                    ],
                    'sample_data' => [
                        'employees' => DB::table('employees')->limit(2)->get(),
                        'commissions' => DB::table('employee_commissions')->limit(2)->get(),
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        });

        Route::get('/ajax/test-report', function () {
            return response()->json([
                [
                    'Test_Column_1' => 'Test Value 1',
                    'Test_Column_2' => 100,
                    'Test_Date' => now()->format('Y-m-d'),
                ],
                [
                    'Test_Column_1' => 'Test Value 2',
                    'Test_Column_2' => 200,
                    'Test_Date' => now()->format('Y-m-d'),
                ]
            ]);
        });
    });
}


Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/reports/stats', [ReportsController::class, 'getStats']);
    Route::get('/reports/data/{type}', [ReportsController::class, 'getReportData']);
    Route::get('/ajax/getReportData/{type}', [ReportsController::class, 'getReportData']);
    
    // Restaurant Module Routes
    // Menu Management
    Route::prefix('menu')->group(function () {
        Route::post('/', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/{id}', [MenuController::class, 'getMenuItem']);
        Route::post('/{id}', [MenuController::class, 'update']);
        Route::delete('/{id}', [MenuController::class, 'destroy']);
        Route::post('/{id}/toggle-availability', [MenuController::class, 'toggleAvailability']);
        Route::get('/api/items', [MenuController::class, 'getMenuItems']);
    });

    // Category Management
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{id}', [CategoryController::class, 'getCategory']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
        Route::post('/update-sort-order', [CategoryController::class, 'updateSortOrder']);
        Route::get('/api/list', [CategoryController::class, 'getCategories']);
    });

    // Table Management
    Route::prefix('tables')->group(function () {
        Route::post('/', [TableController::class, 'store'])->name('tables.store');
        Route::get('/{id}', [TableController::class, 'getTable']);
        Route::post('/{id}', [TableController::class, 'update']);
        Route::delete('/{id}', [TableController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [TableController::class, 'toggleStatus']);
        Route::post('/{id}/update-status', [TableController::class, 'updateTableStatus']);
        Route::get('/api/list', [TableController::class, 'getTables']);
        Route::get('/api/availability', [TableController::class, 'getTableAvailability']);
    });

    // Order Management
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/pos', [OrderController::class, 'storeFromPOS'])->name('orders.store.pos');
        Route::get('/{id}', [OrderController::class, 'getOrder']);
        Route::post('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
        Route::post('/{id}/update-status', [OrderController::class, 'updateStatus']);
        Route::post('/{orderId}/items/{itemId}/update-status', [OrderController::class, 'updateOrderItemStatus']);
        Route::get('/api/list', [OrderController::class, 'getOrders']);
        Route::get('/api/kitchen', [OrderController::class, 'getKitchenOrders']);
        Route::get('/api/table/{tableId}', [OrderController::class, 'getTableOrders']);
        Route::get('/api/table/{tableId}/context', [OrderController::class, 'getTableContext']);
        Route::get('/api/tables/status', [OrderController::class, 'getAllTableStatuses']);
        Route::post('/api/table/{tableId}/complete', [OrderController::class, 'completeTableOrders']);
    });

});

// API Routes for POS (Public access for POS system)
Route::prefix('api')->group(function () {
    Route::get('/products', [MenuController::class, 'getMenuItems']);
    Route::get('/categories', [CategoryController::class, 'getCategories']);
    Route::get('/tables', [TableController::class, 'getTables']);
});

// Vue.js POS Route
Route::get('/pos-vue', function () {
    return view('pos_vue');
});

// Order API Routes for POS (Public access)
Route::prefix('orders/api')->group(function () {
    Route::get('/tables/status', [OrderController::class, 'getAllTableStatuses']);
    Route::get('/table/{tableId}/context', [OrderController::class, 'getTableContext']);
    Route::post('/table/{tableId}/complete', [OrderController::class, 'completeTableOrders']);
});

// Payment API Routes for POS (Public access)
Route::prefix('payments/api')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment']);
    Route::get('/methods', [PaymentController::class, 'getPaymentMethods']);
    Route::get('/table/{tableId}/sales', [PaymentController::class, 'getTableSalesSummary']);
});

// Order creation for POS (Public access)
Route::post('/orders/pos', [OrderController::class, 'storeFromPOS']);

// Restoran Yönetimi Ana Sayfası
Route::get('/restaurant-management', [DashboardController::class, 'restaurantManagement'])->name('restaurant.management');
    Route::get('/financial-management', [DashboardController::class, 'financialManagement'])->name('financial.management');
    Route::get('/hr-management', [DashboardController::class, 'hrManagement'])->name('hr.management');
    Route::get('/quick-menu', [DashboardController::class, 'quickMenu'])->name('quick.menu');

// Income Statement Routes
Route::get('/income-statement', [App\Http\Controllers\IncomeStatementController::class, 'index'])->name('income-statement.index');
Route::get('/income-statement/pdf', [App\Http\Controllers\IncomeStatementController::class, 'exportPDF'])->name('income-statement.pdf');

// Customer Debt Management Routes (Borç Yönetimi)
Route::prefix('customer-debts')->name('customer-debts.')->group(function () {
    Route::get('/', [App\Http\Controllers\CustomerDebtController::class, 'index'])->name('index');
    Route::get('/{customerId}', [App\Http\Controllers\CustomerDebtController::class, 'show'])->name('show');
    Route::post('/pay', [App\Http\Controllers\CustomerDebtController::class, 'payDebt'])->name('pay');
    Route::post('/cancel-payment/{paymentId}', [App\Http\Controllers\CustomerDebtController::class, 'cancelPayment'])->name('cancel-payment');
    
    // AJAX Routes
    Route::get('/api/debt-info/{expenseId}', [App\Http\Controllers\CustomerDebtController::class, 'getDebtInfo'])->name('api.debt-info');
    Route::get('/api/summary/{customerId}', [App\Http\Controllers\CustomerDebtController::class, 'getDebtSummary'])->name('api.summary');
});

// Report Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::get('/income', [App\Http\Controllers\ReportController::class, 'income'])->name('income');
    Route::get('/expense', [App\Http\Controllers\ReportController::class, 'expense'])->name('expense');
});
Route::get('/income-statement/excel', [App\Http\Controllers\IncomeStatementController::class, 'exportExcel'])->name('income-statement.excel');
