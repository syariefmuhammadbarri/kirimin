<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Branch\BranchAdminController;
use App\Http\Controllers\Courier\CourierController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Manager\LandingContentController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\SettingController;
use App\Http\Controllers\Manager\VehicleController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Webhook\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [CustomerController::class, 'landing'])->name('landing');
Route::get('/track', [CustomerController::class, 'trackPublic'])->name('track.public');
Route::get('/calculator', [CustomerController::class, 'calculator'])->name('calculator');
Route::get('/branches', [CustomerController::class, 'branches'])->name('branches');

// Webhook
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handleNotification'])->name('webhook.midtrans');

// Authentication Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('recaptcha');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('recaptcha');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Logout & Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:1,5')
        ->name('verification.send');
});

// Authenticated Panel Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Customer Role
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/booking', [CustomerController::class, 'showBooking'])->name('booking.create');
        Route::post('/booking', [CustomerController::class, 'createBooking'])->name('booking.store');
        Route::post('/calculate-rate', [CustomerController::class, 'calculateRate'])->name('calculate-rate');
        Route::get('/payment/{shipment}', [CustomerController::class, 'paymentDetails'])->name('payment.details');
        Route::post('/payment/mock-settle/{shipment}', [CustomerController::class, 'mockSettlePayment'])->name('payment.mock-settle');
        Route::get('/invoice/{shipment}', [CustomerController::class, 'downloadInvoice'])->name('invoice.download');
    });

    // Branch Admin Role
    Route::middleware('role:admin_cabang')->prefix('branch')->name('branch.')->group(function () {
        Route::get('/dashboard', [BranchAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/scan', [BranchAdminController::class, 'showScan'])->name('scan.show');
        Route::post('/scan', [BranchAdminController::class, 'processScan'])->name('scan.process');
        Route::post('/process-weigh/{shipment}', [BranchAdminController::class, 'processWeigh'])->name('process-weigh');
        Route::post('/confirm-cash/{shipment}', [BranchAdminController::class, 'confirmCashPayment'])->name('confirm-cash');
        Route::post('/assign-courier/{shipment}', [BranchAdminController::class, 'assignCourier'])->name('assign-courier');
        Route::get('/receipt/{shipment}', [BranchAdminController::class, 'printReceipt'])->name('receipt');
        Route::get('/assignments', [BranchAdminController::class, 'viewAssignments'])->name('assignments');
        Route::get('/reports', [BranchAdminController::class, 'downloadBranchReport'])->name('reports');
    });

    // Courier Role
    Route::middleware('role:kurir')->prefix('courier')->name('courier.')->group(function () {
        Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('dashboard');
        Route::post('/out-for-delivery/{shipment}', [CourierController::class, 'outForDelivery'])->name('out-for-delivery');
        Route::post('/deliver/{shipment}', [CourierController::class, 'deliver'])->name('deliver');
        Route::post('/fail/{shipment}', [CourierController::class, 'failDelivery'])->name('fail');
    });

    // Manager / Super Admin Role
    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
        
        // CRUD Branches
        Route::get('/branches', [ManagerController::class, 'listBranches'])->name('branches.index');
        Route::get('/branches/create', [ManagerController::class, 'createBranch'])->name('branches.create');
        Route::post('/branches', [ManagerController::class, 'storeBranch'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [ManagerController::class, 'editBranch'])->name('branches.edit');
        Route::post('/branches/{branch}', [ManagerController::class, 'updateBranch'])->name('branches.update');
        Route::delete('/branches/{branch}', [ManagerController::class, 'deleteBranch'])->name('branches.destroy');

        // CRUD Users (Internal Admins & Couriers)
        Route::get('/users', [ManagerController::class, 'listUsers'])->name('users.index');
        Route::get('/users/create', [ManagerController::class, 'createUser'])->name('users.create');
        Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [ManagerController::class, 'editUser'])->name('users.edit');
        Route::post('/users/{user}', [ManagerController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [ManagerController::class, 'deleteUser'])->name('users.destroy');

        // CRUD Vehicles
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::post('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Landing Contents
        Route::get('/landing-contents', [LandingContentController::class, 'index'])->name('landing-contents.index');
        Route::get('/landing-contents/create', [LandingContentController::class, 'create'])->name('landing-contents.create');
        Route::post('/landing-contents', [LandingContentController::class, 'store'])->name('landing-contents.store');
        Route::get('/landing-contents/{landingContent}/edit', [LandingContentController::class, 'edit'])->name('landing-contents.edit');
        Route::post('/landing-contents/{landingContent}', [LandingContentController::class, 'update'])->name('landing-contents.update');
        Route::delete('/landing-contents/{landingContent}', [LandingContentController::class, 'destroy'])->name('landing-contents.destroy');

        Route::get('/report', [ManagerController::class, 'downloadReport'])->name('report');
    });

    // Owner Role
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/export-report', [OwnerController::class, 'exportReport'])->name('export-report');
    });
});
