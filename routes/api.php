<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\TenantAuthController;
use App\Http\Controllers\Api\Auth\HouseOwnerAuthController;
use App\Http\Controllers\Api\Auth\AdministratorAuthController;
use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AgreementController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// --- Public Routes (No authentication required) ---
// Authentication for different user types
Route::post('/tenant/register', [TenantAuthController::class, 'register']);
Route::post('/tenant/login', [TenantAuthController::class, 'login']);

Route::post('/house-owner/register', [HouseOwnerAuthController::class, 'register']);
Route::post('/house-owner/login', [HouseOwnerAuthController::class, 'login']);

Route::post('/admin/register', [AdministratorAuthController::class, 'register']);
Route::post('/admin/login', [AdministratorAuthController::class, 'login']);

// Read: Get all houses (anyone can view)
Route::get('/houses', [HouseController::class, 'index']);
// Read: Get a specific house (anyone can view)
Route::get('/houses/{house}', [HouseController::class, 'show']);

// Read: Get all bookings (publicly visible, but could be empty/restricted if desired)
Route::get('/bookings', [BookingController::class, 'index']);
// Read: Get a specific booking (public, but controllers will add auth checks)
Route::get('/bookings/{booking}', [BookingController::class, 'show']);

Route::get('/ping', function () {
    return response()->json(['message' => 'API is working']);
});

// --- Authenticated Routes ---

// Tenant Specific Routes
Route::middleware('auth:sanctum_tenant')->group(function () {
    Route::get('/tenant/user', function (Request $request) { return $request->user(); });
    Route::post('/tenant/logout', [TenantAuthController::class, 'logout']);

    // Create Booking
    Route::post('/bookings', [BookingController::class, 'store']);
    // Update/Delete Booking (only owner of booking)
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

    // Create Payment (for a booking/agreement related to tenant)
    Route::post('/payments', [PaymentController::class, 'store']);
    // Read/Update/Delete Payment (only owner of payment, for self-service portal type)
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::put('/payments/{payment}', [PaymentController::class, 'update']);
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);

    // Feedback
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);
});

// House Owner Specific Routes
Route::middleware('auth:sanctum_house_owner')->group(function () {
    Route::get('/house-owner/user', function (Request $request) { return $request->user(); });
    Route::post('/house-owner/logout', [HouseOwnerAuthController::class, 'logout']);

    // CRUD for Houses (House Owner manages their houses)
    Route::post('/houses', [HouseController::class, 'store']);
    Route::put('/houses/{house}', [HouseController::class, 'update']);
    Route::delete('/houses/{house}', [HouseController::class, 'destroy']);

    // House Owner can view their related bookings
    Route::get('/house-owner/bookings', [BookingController::class, 'houseOwnerBookings']);
    // House Owner can update booking status (e.g., approve/reject)
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    // House Owner can view their related agreements
    Route::get('/house-owner/agreements', [AgreementController::class, 'houseOwnerAgreements']);
    // House Owner can view their related payments
    Route::get('/house-owner/payments', [PaymentController::class, 'houseOwnerPayments']);
});

// Administrator Specific Routes
Route::middleware('auth:sanctum_administrator')->group(function () {
    Route::get('/admin/user', function (Request $request) { return $request->user(); });
    Route::post('/admin/logout', [AdministratorAuthController::class, 'logout']);

    // CRUD for Agreements (Admin makes agreements)
    Route::post('/agreements', [AgreementController::class, 'store']);
    Route::get('/agreements', [AgreementController::class, 'index']);
    Route::get('/agreements/{agreement}', [AgreementController::class, 'show']);
    Route::put('/agreements/{agreement}', [AgreementController::class, 'update']);
    Route::delete('/agreements/{agreement}', [AgreementController::class, 'destroy']);

    // Admin can view all records across the system
    Route::get('/admin/houses', [HouseController::class, 'indexAdmin']);
    Route::get('/admin/bookings', [BookingController::class, 'indexAdmin']);
    Route::get('/admin/payments', [PaymentController::class, 'indexAdmin']);
    Route::get('/admin/feedback', [FeedbackController::class, 'indexAdmin']);

    // List all registered house owners (add this route)
    Route::get('/admin/house-owners', [HouseOwnerAuthController::class, 'index']);

    // Example for admin managing user types (requires specific controllers for each)
    // Route::apiResource('admin/tenants', TenantManagementController::class);
    // Route::apiResource('admin/house-owners', HouseOwnerManagementController::class);
    // Route::apiResource('admin/administrators', AdministratorManagementController::class);
});