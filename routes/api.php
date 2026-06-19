<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

Route::middleware(['auth:sanctum', 'organization', 'organization.throttle'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/projects/{id}/restore', [ProjectController::class, 'restore']);
    Route::apiResource('projects', ProjectController::class);

    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);

    Route::get('/billing/status', [BillingController::class, 'status']);
    Route::post('/billing/checkout', [BillingController::class, 'checkout']);
});
