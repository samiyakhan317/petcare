<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminGroomerController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GroomingReportController;


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get(
    '/login',
    [LoginController::class, 'showLogin']
)->name('login');

Route::post(
    '/login',
    [LoginController::class, 'login']
);

Route::get(
    '/logout',
    [LoginController::class, 'logout']
)->name('logout');


/*
|--------------------------------------------------------------------------
| Signup
|--------------------------------------------------------------------------
*/

Route::get(
    '/signup',
    [SignupController::class, 'index']
)->name('signup');

Route::post(
    '/signup',
    [SignupController::class, 'store']
)->name('signup.store');


/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/

/*
Route::get(
    '/customers',
    [CustomerController::class, 'index']
)->name('customers');
*/


/*
|--------------------------------------------------------------------------
| Customer Pet Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/pets',
    [PetController::class, 'index']
)->name('pets.index');

Route::get(
    '/pets/create',
    [PetController::class, 'create']
)->name('pets.create');

Route::post(
    '/pets',
    [PetController::class, 'store']
)->name('pets.store');

Route::get(
    '/pets/{id}/edit',
    [PetController::class, 'edit']
)->name('pets.edit');

Route::put(
    '/pets/{id}',
    [PetController::class, 'update']
)->name('pets.update');

Route::delete(
    '/pets/{id}',
    [PetController::class, 'destroy']
)->name('pets.destroy');


/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/dashboard',
    [AdminController::class, 'dashboard']
)->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| Admin Customer Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/customers',
    [AdminCustomerController::class, 'index']
)->name('admin.customers');

Route::post(
    '/admin/customers/store',
    [AdminCustomerController::class, 'store']
)->name('admin.customers.store');

Route::delete(
    '/admin/customers/{id}',
    [AdminCustomerController::class, 'destroy']
)->name('admin.customers.destroy');


/*
|--------------------------------------------------------------------------
| Admin Groomer Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/groomers',
    [AdminGroomerController::class, 'index']
)->name('admin.groomers');

Route::post(
    '/admin/groomers/store',
    [AdminGroomerController::class, 'store']
)->name('admin.groomers.store');

Route::delete(
    '/admin/groomers/{id}',
    [AdminGroomerController::class, 'destroy']
)->name('admin.groomers.destroy');


/*
|--------------------------------------------------------------------------
| Admin Service Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/services',
    [AdminServiceController::class, 'index']
)->name('admin.services.index');

Route::get(
    '/admin/services/create',
    [AdminServiceController::class, 'create']
)->name('admin.services.create');

Route::post(
    '/admin/services',
    [AdminServiceController::class, 'store']
)->name('admin.services.store');

Route::delete(
    '/admin/services/{id}',
    [AdminServiceController::class, 'destroy']
)->name('admin.services.destroy');


/*
|--------------------------------------------------------------------------
| Admin Payment Management
|--------------------------------------------------------------------------
|
| Admin can:
|
| 1. View all customer payments
| 2. View individual payment details
| 3. Mark pending/unpaid payments as PAID
|
*/


/*
|--------------------------------------------------------------------------
| Admin Payment List
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/payments',
    [AdminPaymentController::class, 'index']
)->name('admin.payments');


/*
|--------------------------------------------------------------------------
| Admin Mark Payment As Paid
|--------------------------------------------------------------------------
|
| Used mainly for cash payments.
|
| PENDING / UNPAID
|        ↓
|       PAID
|
*/

Route::post(
    '/admin/payments/{paymentId}/mark-paid',
    [AdminPaymentController::class, 'markAsPaid']
)->name('admin.payments.markPaid');


/*
|--------------------------------------------------------------------------
| Admin Payment Details
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This route is after /mark-paid so that
| the static "mark-paid" URL is not treated
| as a payment ID.
|
*/

Route::get(
    '/admin/payments/{paymentId}',
    [AdminPaymentController::class, 'show']
)->name('admin.payments.show');


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get(
    '/home',
    [HomeController::class, 'index']
)->name('home');


/*
|--------------------------------------------------------------------------
| Customer Services
|--------------------------------------------------------------------------
*/

Route::get(
    '/services',
    [ServiceController::class, 'index']
)->name('services.index');


/*
|--------------------------------------------------------------------------
| Customer Appointments
|--------------------------------------------------------------------------
*/

Route::get(
    '/appointments',
    [AppointmentController::class, 'index']
)->name('appointments.index');

Route::get(
    '/appointments/create',
    [AppointmentController::class, 'create']
)->name('appointments.create');

Route::post(
    '/appointments',
    [AppointmentController::class, 'store']
)->name('appointments.store');

Route::get(
    '/appointments/{id}',
    [AppointmentController::class, 'show']
)->name('appointments.show');

Route::post(
    '/appointments/{id}/cancel',
    [AppointmentController::class, 'cancel']
)->name('appointments.cancel');


/*
|--------------------------------------------------------------------------
| Grooming Reports
|--------------------------------------------------------------------------
|
| Customer:
|   View grooming reports
|
| Groomer:
|   Create/Edit grooming reports
|
*/

Route::get(
    '/grooming-reports',
    [GroomingReportController::class, 'index']
)->name('grooming-reports.index');


/*
|--------------------------------------------------------------------------
| Customer View Grooming Report
|--------------------------------------------------------------------------
*/

Route::get(
    '/grooming-reports/{appointmentId}/view',
    [GroomingReportController::class, 'viewReport']
)->name('grooming-reports.view');


/*
|--------------------------------------------------------------------------
| Groomer Create Grooming Report
|--------------------------------------------------------------------------
*/

Route::get(
    '/grooming-reports/{appointmentId}/create',
    [GroomingReportController::class, 'create']
)->name('grooming-reports.create');

Route::post(
    '/grooming-reports/{appointmentId}',
    [GroomingReportController::class, 'store']
)->name('grooming-reports.store');


/*
|--------------------------------------------------------------------------
| Groomer Edit Grooming Report
|--------------------------------------------------------------------------
*/

Route::get(
    '/grooming-reports/{appointmentId}/edit',
    [GroomingReportController::class, 'edit']
)->name('grooming-reports.edit');

Route::put(
    '/grooming-reports/{appointmentId}',
    [GroomingReportController::class, 'update']
)->name('grooming-reports.update');


/*
|--------------------------------------------------------------------------
| Customer Payments
|--------------------------------------------------------------------------
|
| Customer can:
|
| 1. Open payment page
| 2. Pay by cash
| 3. Pay online
| 4. View payment details
|
*/


/*
|--------------------------------------------------------------------------
| Payment Page
|--------------------------------------------------------------------------
*/

Route::get(
    '/payments/{appointmentId}/create',
    [PaymentController::class, 'create']
)->name('payments.create');


/*
|--------------------------------------------------------------------------
| Cash Payment
|--------------------------------------------------------------------------
|
| Customer selects:
|   CASH
|
| Payment created as:
|
| Payment_Status = PENDING
| Payment_Method = CASH
|
*/

Route::post(
    '/payments/{appointmentId}/cash',
    [PaymentController::class, 'cash']
)->name('payments.cash');


/*
|--------------------------------------------------------------------------
| Online Payment
|--------------------------------------------------------------------------
|
| Customer selects:
|
| CARD
| BKASH
| NAGAD
|
| Controller stores:
|
| Payment_Method = ONLINE
| Payment_Status = PAID
|
| This is simulated online payment for the project.
|
*/

Route::post(
    '/payments/{appointmentId}/online',
    [PaymentController::class, 'online']
)->name('payments.online');


/*
|--------------------------------------------------------------------------
| Customer Payment Details
|--------------------------------------------------------------------------
|
| Example:
| /payments/15
|
| This route stays LAST because
| {appointmentId} is a dynamic parameter.
|
*/

Route::get(
    '/payments/{appointmentId}',
    [PaymentController::class, 'show']
)->name('payments.show');