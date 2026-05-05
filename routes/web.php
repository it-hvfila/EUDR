<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\SupplierDocumentController;
use App\Http\Controllers\CompanyDocumentController;
use App\Http\Controllers\Cpd_lotController;
use App\Http\Controllers\Fg_CpdController;
use App\Http\Controllers\InvoiceController;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/download-geojson/{filename}', function ($filename) {
    // ให้ไปหาใน public_path แทน storage_path
    $path = public_path($filename);

    if (file_exists($path)) {
        return response()->download($path);
    }

    abort(404, 'ไม่พบไฟล์ที่: ' . $path);
})->name('geojson.download')->where('filename', '.*');

Route::get(
    '/company_docs/download/{token}',
    [CompanyDocumentController::class, 'download']
)->name('company.docs.download');

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/test', function () {
    return view('index');
});
Route::get('/', function () {
    return view('login.login');
});
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/status', [UserController::class, 'status']);
Route::post('/register', [UserController::class, 'register']);







//start-middleware
Route::middleware(['username.session'])->group(function () {

    // ✅ หน้า View
    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.page');
    // ✅ ดึงข้อมูล DataTables
    Route::get('/doc_dataTable', [DocumentsController::class, 'index'])->name('documents.dataTable');
    Route::post('/documents/upload', [DocumentsController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/view/{encodedId}', [DocumentsController::class, 'view'])->name('documents.view');
    Route::get('/documents/download/{encodedId}', [DocumentsController::class, 'download'])->name('documents.download');



    // ✅ หน้า View
    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.page');
    Route::get('/supplier_dataTable', [SupplierController::class, 'index'])->name('supplier.dataTable');
    Route::post('/suppliers/store', [SupplierController::class, 'store'])->name('suppliers.store');
    // แบบง่ายใช้ POST สำหรับ AJAX delete
    Route::post('/suppliers/{id}/delete', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/lots/{id}', [LotController::class, 'show'])->name('lots.show');
    Route::post('/lots/store', [LotController::class, 'store'])->name('lots.store');
    Route::post('/lots/update/{id}', [LotController::class, 'update'])->name('lots.update');
    Route::delete('/lots/delete/{id}', [LotController::class, 'destroy'])->name('lots.destroy');

    //map
    Route::post('/lots/map-cpd', [LotController::class, 'mapToCpd']);

    // ดึงไฟล์ของ Lot
    Route::get('lots/{id}/files', [LotController::class, 'getFiles']);
    // อัปโหลดไฟล์ของ Lot
    Route::post('lots/{id}/upload', [LotController::class, 'uploadLotFile']);
    // ลบไฟล์ Lot
    Route::delete('lots/file/{id}', [LotController::class, 'deleteFile']);

    Route::get('/supplier/{supplier_id}/lots', [LotController::class, 'index'])->name('supplier.lots');

    Route::get('/lots_dataTable', [LotController::class, 'index'])->name('lots.dataTable');

    // เอกสาร Supplier
    // Supplier Documents
    // Supplier Documents
    Route::get('/supplier_docs', [SupplierDocumentController::class, 'index'])->name('supplier_docs.index');
    Route::get('/supplier_docs/data', [SupplierDocumentController::class, 'index'])->name('supplier_docs.data');
    Route::post('/supplier_docs/save', [SupplierDocumentController::class, 'saveDocument'])->name('supplier_docs.save');
    Route::get('/supplier_docs/get/{id}', [SupplierDocumentController::class, 'getDocument'])->name('supplier_docs.get');
    Route::get('/supplier_docs/view/{token}', [SupplierDocumentController::class, 'view'])->name('supplier_docs.view');
    Route::get('/supplier_docs/download/{token}', [SupplierDocumentController::class, 'download'])->name('supplier_docs.download');
    Route::delete('/supplier_docs/delete/{id}', [SupplierDocumentController::class, 'destroy'])->name('supplier_docs.destroy');




    // เอกสารบริษัท
    Route::get('/company_docs', [CompanyDocumentController::class, 'index'])->name('company_docs.page');
    Route::get('/company_docs/data', [CompanyDocumentController::class, 'index'])->name('company_docs.data');
    Route::get('/company_docs/view/{token}', [CompanyDocumentController::class, 'view'])->name('company_docs.view');
    Route::get('/company_docs/download_2/{token}', [CompanyDocumentController::class, 'download'])->name('company_docs.download');
    Route::get('/company_docs/get/{id}', [CompanyDocumentController::class, 'getDocument'])->name('company_docs.get');
    Route::post('/company_docs/save', [CompanyDocumentController::class, 'saveDocument'])->name('company_docs.save');
    Route::delete('/company_docs/delete/{id}', [CompanyDocumentController::class, 'destroy'])->name('company_docs.delete');

    Route::get('/manage_user', [UserController::class, 'index'])->name('manage_user.page');
    Route::get('/manage_user/data', [UserController::class, 'index'])->name('manage_user.data');
    Route::get('/manage_user/get/{id}', [UserController::class, 'getUser'])->name('manage_user.get');
    Route::post('/manage_user/save', [UserController::class, 'store'])->name('manage_user.store');
    Route::delete('/manage_user/delete/{id}', [UserController::class, 'destroy'])->name('manage_user.delete');

    //cpd_lot
    Route::get('cpd-lots/{lot_id}', [Cpd_lotController::class, 'index'])
        ->name('cpd.lots.index');
    // เพิ่ม CPD (map)
    Route::post('cpd-lots/store', [Cpd_lotController::class, 'store'])
        ->name('cpd.lots.store');

    // ดึงข้อมูล 1 record (edit)
    Route::get('cpd-lots/show/{id}', [Cpd_lotController::class, 'show'])
        ->name('cpd.lots.show');

    // แก้ไข CPD
    Route::post('cpd-lots/update/{id}', [Cpd_lotController::class, 'update'])
        ->name('cpd.lots.update');

    // ลบ CPD
    Route::delete('cpd-lots/delete/{id}', [Cpd_lotController::class, 'destroy'])
        ->name('cpd.lots.delete');


    // หน้า list FG ของ CPD
    Route::get('fg-cpd/{cpd_id}', [Fg_CpdController::class, 'index'])
        ->name('fg.cpd.index');

    // เพิ่ม FG map CPD
    Route::post('fg-cpd/store', [Fg_CpdController::class, 'store'])
        ->name('fg.cpd.store');

    // ดึงข้อมูล FG 1 รายการ (edit)
    Route::get('fg-cpd/show/{id}', [Fg_CpdController::class, 'show'])
        ->name('fg.cpd.show');

    // แก้ไข FG
    Route::post('fg-cpd/update/{id}', [Fg_CpdController::class, 'update'])
        ->name('fg.cpd.update');

    // ลบ FG
    Route::delete('fg-cpd/delete/{id}', [Fg_CpdController::class, 'destroy'])
        ->name('fg.cpd.delete');

    // invoice
    Route::get('/invoice', [InvoiceController::class, 'index']);
    Route::post('/invoice/search', [InvoiceController::class, 'search'])->name('invoice.search');



    //Header
    Route::get('/notifications', [HeaderController::class, 'index']);
    //Home
    Route::get('/need_help', function () {
        return view('need_help');
    });

    //Home
    Route::get('/home', function () {
        return view('home');
    });
    Route::get('/dashboard_report', function () {
        return view('dashboard_report');
    });
    Route::get('/count-jobs-today', [HomeController::class, 'countJobsToday']);
    Route::get('/count_jobs_month', [HomeController::class, 'countJobsThisMonth']);
    Route::get('/count_jobs_year', [HomeController::class, 'countJobsThisYear']);
    Route::get('/reports_data', [HomeController::class, 'getReportData']);
    Route::get('/recent-jobs', [HomeController::class, 'getRecentJobs']);
    Route::get('/chart-data', [HomeController::class, 'getChartData']);
    Route::get('/work_efficiency', [HomeController::class, 'index'])->name('work_efficiency');

    //News
    Route::get('/latest-news', [HomeController::class, 'getLatestNews']);
    //insert
    Route::post('/Form_News', [HomeController::class, 'insert']);
    Route::get('/news/{id}/edit', [HomeController::class, 'edit'])->name('news.edit');
    //Create a job
    Route::get('/job', function () {
        return view('job');
    });
});
//end-middleware
