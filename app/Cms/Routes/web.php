<?php

use Cms\Controllers\News\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['web'])->group(function () {
    Route::apiResource('/news', NewsController::class)->only(['index', 'show']);
});
