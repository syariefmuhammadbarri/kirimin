<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Branch\BranchAdminController;
use App\Http\Controllers\Branch\WalkInBookingController;
use App\Http\Controllers\Courier\CourierController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ShipmentCancelController;
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
    // Step 1: Role selection page (Staff vs Customer)
    Route::get('/login', [AuthController::class, 'showLoginChoice'])->name('login.choose');
    // Step 2: Login form for specific type
    Route::get('/login/{type}', [AuthController::class, 'showLoginForm'])->name('login.form');
    // Step 3: Process login
    Route::post('/login', [AuthController::class, 'login'])->middleware('recaptcha')->name('login');
    
    // Step 1: Role selection page for registration (Staff vs Customer)
    Route::get('/register', [AuthController::class, 'showRegisterChoice'])->name('register.choose');
    // Step 2: Registration form for specific type
    Route::get('/register/{type}', [AuthController::class, 'showRegisterForm'])->name('register.form');
    // Step 3: Process registration
    Route::post('/register', [AuthController::class, 'register'])->middleware('recaptcha')->name('register');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Logout & Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    // OTP-based email verification (replaces email link verification)
    Route::post('/email/verify/otp', [AuthController::class, 'verifyWithOtp'])
        ->name('verification.verify-otp');
    Route::post('/email/verify/resend-otp', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:1,5')
        ->name('verification.resend-otp');
    // Keep old routes for backward compatibility but they won't be used
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
        // FR-01: Cancel shipment oleh customer
        Route::post('/shipment/{shipment}/cancel', [ShipmentCancelController::class, 'cancel'])->name('shipment.cancel');
        // FR-11: Profil customer
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // FR-08: Notifikasi inbox
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });

    // Branch Admin Role
    Route::middleware('role:admin_cabang')->prefix('branch')->name('branch.')->group(function () {

        Route::get('/dashboard', [BranchAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/scan', [BranchAdminController::class, 'showScan'])->name('scan.show');
        Route::post('/scan', [BranchAdminController::class, 'processScan'])->name('scan.process');
        Route::get('/shipment/{shipment}/process', [BranchAdminController::class, 'processPage'])->name('shipment.process');
        Route::post('/process-weigh/{shipment}', [BranchAdminController::class, 'processWeigh'])->name('process-weigh');
        Route::post('/confirm-cash/{shipment}', [BranchAdminController::class, 'confirmCashPayment'])->name('confirm-cash');
        Route::post('/assign-courier/{shipment}', [BranchAdminController::class, 'assignCourier'])->name('assign-courier');
        Route::post('/assign-pickup-courier/{shipment}', [BranchAdminController::class, 'assignPickupCourier'])->name('assign-pickup-courier');
        Route::get('/receipt/{shipment}', [BranchAdminController::class, 'printReceipt'])->name('receipt');
        Route::get('/assignments', [BranchAdminController::class, 'viewAssignments'])->name('assignments');
        Route::get('/reports', [BranchAdminController::class, 'downloadBranchReport'])->name('reports');
        Route::post('/send-transit/{shipment}', [BranchAdminController::class, 'sendTransit'])->name('send-transit');
        Route::post('/receive-transit/{shipment}', [BranchAdminController::class, 'receiveTransit'])->name('receive-transit');

        // FR-07: Walk-in booking oleh admin cabang
        Route::get('/booking/walk-in', [WalkInBookingController::class, 'create'])->name('booking.walkin');
        Route::post('/booking/walk-in/store', [WalkInBookingController::class, 'store'])->name('booking.walkin.store');
        // Alur Sekuensial Walk-In & Cash Settlement (PRD Section 2B)
        Route::get('/payment/verify/{shipment}', [WalkInBookingController::class, 'verifyPayment'])->name('payment.verify');
        Route::post('/payment/process/{shipment}', [WalkInBookingController::class, 'processPayment'])->name('payment.process');
    });

    // Courier Role
    Route::middleware('role:kurir')->prefix('courier')->name('courier.')->group(function () {
        Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('dashboard');
        // Alur Validasi Update Status & POD (PRD Section 2C)
        Route::get('/shipment/{shipment}/detail', [CourierController::class, 'show'])->name('shipment.detail');
        Route::post('/shipment/{shipment}/complete', [CourierController::class, 'completeDelivery'])->name('shipment.complete');
        // Legacy/operational action routes
        Route::post('/pickup/{shipment}', [CourierController::class, 'pickUp'])->name('pickup');
        Route::post('/collect/{shipment}', [CourierController::class, 'collectFromCustomer'])->name('collect');
        Route::post('/drop-at-branch/{shipment}', [CourierController::class, 'dropAtBranch'])->name('drop-at-branch');
        Route::post('/out-for-delivery/{shipment}', [CourierController::class, 'outForDelivery'])->name('out-for-delivery');
        Route::post('/deliver/{shipment}', [CourierController::class, 'deliver'])->name('deliver');
        Route::post('/fail/{shipment}', [CourierController::class, 'failDelivery'])->name('fail');
        // Fase 3: Retry pengantaran yang gagal
        Route::post('/retry/{shipment}', [CourierController::class, 'retryDelivery'])->name('retry');
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

        // FR-09: Moderasi akun staff
        Route::post('/users/{user}/toggle-active', [ManagerController::class, 'toggleUserActive'])->name('users.toggle-active');

        // FR-10: Manajemen akun customer
        Route::get('/customers', [ManagerController::class, 'listCustomers'])->name('customers.index');
        Route::post('/customers/{customer}/toggle-suspend', [ManagerController::class, 'toggleCustomerSuspend'])->name('customers.toggle-suspend');
    });

    // Owner Role
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/export-report', [OwnerController::class, 'exportReport'])->name('export-report');
    });
});
