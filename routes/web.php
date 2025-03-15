<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\MethodPaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;


use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\PurchaseTransactionController;
use App\Http\Controllers\StockOpnameController;

use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalMingguController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\ListRentalController;


use App\Http\Controllers\ExportController;


// route for auth
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm']);
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//route for have auth
Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');



    //export SQL
    Route::get('export-sql', [ExportController::class, 'index']);

    //biodata
    Route::resource("biodata", BiodataController::class);
    Route::post('biodata-reset-password/{id}', [BiodataController::class, 'biodata_reset_password']);

    //module
    Route::prefix('module')->group(function() {
        Route::get('/', [ModuleController::class, 'index']);
        Route::get('/json', [ModuleController::class, 'json']);
        Route::post('/sort', [ModuleController::class, 'sort']);
        Route::get('/delete/{id}', [ModuleController::class, 'destroy']);
    });
    Route::resource("module", ModuleController::class);

    //level
    Route::prefix('level')->group(function() {
        Route::get('/', [LevelController::class, 'index']);
        Route::get('/json', [LevelController::class, 'json']);
        Route::get('/delete/{id}', [LevelController::class, 'destroy']);
    });
    Route::resource("level", LevelController::class);



    //user
    Route::prefix('user')->group(function() {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/json', [UserController::class, 'json']);
        Route::get('/delete/{id}', [UserController::class, 'destroy']);
        Route::get('/change-status/{id}', [UserController::class, 'change_status']);
        Route::get('/reset-password/{id}', [UserController::class, 'reset_password'])->name('user.password');
    });
    Route::resource("user", UserController::class);






    //Unit
    Route::prefix('unit')->group(function() {
        Route::get('/', [UnitController::class, 'index']);
        Route::get('/json', [UnitController::class, 'json']);
        Route::get('/delete/{id}', [UnitController::class, 'destroy']);
    });
    Route::resource("unit", UnitController::class);


    //Kategori
    Route::prefix('category')->group(function() {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/json', [CategoryController::class, 'json']);
        Route::get('/delete/{id}', [CategoryController::class, 'destroy']);
    });
    Route::resource("category", CategoryController::class);

    //Merek
    Route::prefix('brand')->group(function() {
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/json', [BrandController::class, 'json']);
        Route::get('/delete/{id}', [BrandController::class, 'destroy']);
    });
    Route::resource("brand", BrandController::class);

    //metode pembayaran
    Route::prefix('method-payment')->group(function() {
        Route::get('/', [MethodPaymentController::class, 'index']);
        Route::get('/json', [MethodPaymentController::class, 'json']);
        Route::get('/delete/{id}', [MethodPaymentController::class, 'destroy']);
    });
    Route::resource("method-payment", MethodPaymentController::class);


    //master customer
    Route::prefix('customer')->group(function() {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/json', [CustomerController::class, 'json']);
        Route::get('/delete/{id}', [CustomerController::class, 'destroy']);
    });
    Route::resource("customer", CustomerController::class);

    //master supplier
    Route::prefix('supplier')->group(function() {
        Route::get('/', [SupplierController::class, 'index']);
        Route::get('/json', [SupplierController::class, 'json']);
        Route::get('/delete/{id}', [SupplierController::class, 'destroy']);
    });
    Route::resource("supplier", SupplierController::class);

    //master produk
    Route::prefix('product')->group(function() {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/json', [ProductController::class, 'json']);
        Route::get('/delete/{id}', [ProductController::class, 'destroy']);
    });
    Route::resource("product", ProductController::class);


    //laporan penjualan
    Route::prefix('sales')->group(function() {
        Route::get('/', [SalesTransactionController::class, 'index']);
        Route::get('/json', [SalesTransactionController::class, 'json']);
        Route::get('/json-show/{id}', [SalesTransactionController::class, 'json_show']);
        Route::get('/delete/{id}', [SalesTransactionController::class, 'destroy']);
    });
    Route::resource("sales", SalesTransactionController::class);

    //laporan pembelian
    Route::prefix('purchase')->group(function() {
        Route::get('/', [PurchaseTransactionController::class, 'index']);
        Route::get('/json', [PurchaseTransactionController::class, 'json']);
        Route::get('/json-show/{id}', [PurchaseTransactionController::class, 'json_show']);
        Route::get('/delete/{id}', [PurchaseTransactionController::class, 'destroy']);
    });
    Route::resource("purchase", PurchaseTransactionController::class);

    //stock opname
    Route::prefix('stock-opname')->group(function() {
        Route::get('/', [StockOpnameController::class, 'index']);
        Route::get('/json', [StockOpnameController::class, 'json']);
        Route::get('/delete/{id}', [StockOpnameController::class, 'destroy']);
    });
    Route::resource("stock-opname", StockOpnameController::class);

    //rental
    Route::prefix('rental')->group(function() {
        Route::get('/', [RentalController::class, 'index']);
        Route::get('/check-stock/{id}', [RentalController::class, 'check_stock']);
    });
    Route::get('/get-events', [RentalController::class, 'getEvents']);
    Route::get('/events-by-date', [RentalController::class, 'getByDate']);
    Route::resource("rental", RentalController::class);

    //biaya rental weekend
    Route::resource("rental-minggu", RentalMingguController::class);

    //keranjang
    Route::prefix('keranjang')->group(function() {
        Route::get('/', [KeranjangController::class, 'index']);
        Route::get('/json', [KeranjangController::class, 'json']);
    });
    Route::resource("keranjang", KeranjangController::class);

    //list rental
    Route::prefix('list-rental')->group(function() {
        Route::get('/', [ListRentalController::class, 'index']);
        Route::get('/json', [ListRentalController::class, 'json']);
        Route::get('/rental-show/{id}', [ListRentalController::class, 'rental_show']);
        Route::get('/delete/{id}', [ListRentalController::class, 'destroy']);
    });
    Route::resource("list-rental", ListRentalController::class);




    // setting website
    Route::resource("option", OptionController::class);
});
