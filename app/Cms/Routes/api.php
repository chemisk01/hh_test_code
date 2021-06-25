<?php

use Cms\Routes\NewsRoutes;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
    NewsRoutes::initRoutes();
});
