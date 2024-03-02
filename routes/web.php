<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ExportEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteSignedController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//? Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('frontend.login');
    });

    Route::get('/order', [UserOrderController::class, 'order'])->name('order');
    Route::put('/makeorder', [UserOrderController::class, 'customerOrder'])->name('makeOrder');
});

//? Signed Routes
Route::get('/submit/{cid}/{sname}', [UserDashboardController::class, 'empData'])->name('share-entry');
Route::post('/submited', [UserDashboardController::class, 'storeEmpData'])->name('submited');
Route::delete('/delete-empdetails/{id}', [RouteSignedController::class, 'delete']);
Route::get('/edit-emp/{id}', [RouteSignedController::class, 'edit']);
Route::post('/update-emp/{id}', [RouteSignedController::class, 'update']);
Route::put('/import/excel', [CsvImportController::class, 'import']);

//? Dashboard route
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//? User Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/change-profile/{id}', [UserDashboardController::class, 'updateProfileImg']);
    Route::post('/assign/{id}', [UserOrderController::class, 'assign']);
    // Route::get('/task', [AdminDashboardController::class, 'sendTask'])->name('sendTask');
    // ?Order routes
    Route::get('/make-order', [UserOrderController::class, 'makeUserOrder'])->name('userorder');
    Route::put('/ordered', [UserOrderController::class, 'makeOrder'])->name('usermakeOrder');
    // Route::put('/order-pdf-download', [UserOrderController::class, 'downloadPDF'])->name('downloadPDF');
    Route::get('/order/download/{orderId}', [UserOrderController::class, 'downloadPDF'])->name('order.downloadPdf');
    Route::get('/orders', [UserOrderController::class, 'orders'])->name('orders');
    Route::get('/order-edit/{id}', [UserOrderController::class, 'orderEdit']);
    Route::post('/order-update/{id}', [UserOrderController::class, 'orderUpdate']);



    // Route::get('invoice', [UserOrderController::class, 'index'])->name('invoice.index');
    // Route::get('invoice/create', [UserOrderController::class, 'create'])->name('invoice.create');
    Route::post('invoice', [UserOrderController::class, 'invoice_store'])->name('invoice.store');
    Route::get('download/{id}', [UserOrderController::class, 'download'])->name('download');
    // Route::delete('invoice/delete/{testimonial}', [UserOrderController::class, 'destroy'])->name('invoice.destroy');
    // Route::post('invoice/update/{testimonial}', [UserOrderController::class, 'update'])->name('invoice.update');
    // Route::get('invoice/edit/{testimonial}', [UserOrderController::class, 'edit'])->name('invoice.edit');



    // ?Order Status updation routes
    Route::post('/m-done/{id}/{orderId}', [UserOrderController::class, 'measurement_done']);
    Route::post('/m-pending/{id}', [UserOrderController::class, 'measurement_pending']);
    Route::post('/p-done/{id}', [UserOrderController::class, 'processing_done']);
    Route::post('/d-pending/{id}', [UserOrderController::class, 'dispatching_pending']);
    Route::post('/p-pending/{id}', [UserOrderController::class, 'readyfordispatch_paymentpending']);
    Route::post('/bulk-update', [UserOrderController::class, 'bulkUpdate']);
    Route::post('/ready-dispatch/{id}', [UserOrderController::class, 'ready_dispatch']);
    Route::post('/emp_dispatch/{id}', [UserOrderController::class, 'emp_dispatch']);
    Route::get('/calender', [AdminDashboardController::class, 'calender'])->name('calender');
    Route::post('/dispatch/{id}', [UserOrderController::class, 'dispatch']);
    Route::get('/calender', [AdminDashboardController::class, 'calender'])->name('calender');
    Route::post('/calender/event', [AdminDashboardController::class, 'calender_event'])->name('calendar.event');
    Route::post('empdetails/import', [CsvImportController::class, 'import'])->name('empdetails.import');
    Route::get('empdetails/export/{customerId}', [CsvImportController::class, 'export'])->name('empdetails.export');
    Route::get('empdetails/export-emp-template', [CsvImportController::class, 'exportTemplate'])->name('empdetailstemplate');
    // Route::get('empdetails/export-template', [CsvImportController::class, 'downloadEmployeeTemplate'])->name('downloadEmployeeTemplate');


    // ? Old order customer details fetching route
    Route::post('fetchcustomer/{id}', [UserOrderController::class, 'fetchcustomer']);

    // ? Calender routes
    Route::get('events/export/', [ExportEventController::class, 'export']);
    Route::post('orders/import/', [UserOrderController::class, 'import']);
    Route::get('orders/export/', [UserOrderController::class, 'export']);

    // ?Task Routes
    Route::post('/finished/{id}', [AdminDashboardController::class, 'finished']);
});

//? Admin Routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/users', [UserDashboardController::class, 'userinfo'])->name('userinfo');
    Route::post('/send-mail/{id}', [RouteSignedController::class, 'sendMailRoute']);
    Route::post('/send-mail-by-order-id/{id}', [RouteSignedController::class, 'sendMailRouteByOrderId']);
    // Route::get('/orders', [UserOrderController::class, 'orders'])->name('orders');
    Route::delete('delete/{id}', [AdminDashboardController::class, 'destroy']);
    Route::post('/accept/{id}', [AdminDashboardController::class, 'accept']);
    Route::post('/reject/{id}', [AdminDashboardController::class, 'reject']);
    Route::post('/hold/{id}', [AdminDashboardController::class, 'hold']);

    // ? Tasks Routes
    Route::get('/tasks', [AdminDashboardController::class, 'addTasks'])->name('sendUserTask');
    Route::post('/add-task', [AdminDashboardController::class, 'addTask'])->name('sendTask');
    Route::post('/add-order-task', [AdminDashboardController::class, 'addOrderTask']);

    // ? Tasks Status Routes
    Route::post('/cancelled/{id}', [AdminDashboardController::class, 'cancelled']);
    Route::post('/onhold/{id}', [AdminDashboardController::class, 'onhold']);
    Route::post('/process/{id}', [AdminDashboardController::class, 'process']);

    // ?Order based task routes
    Route::post('/orders/customer/{id}', [AdminDashboardController::class, 'fetch_customer']);
    Route::get('/search', [SearchController::class, 'search']);

    Route::get('/send-whatsapp-message', [UserOrderController::class, 'sendMessage']);
});

require __DIR__ . '/auth.php';
