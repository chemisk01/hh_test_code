<?php

declare(strict_types=1);

namespace Cms\Routes;

use Cms\Controllers\News\NewsController;
use Cms\Controllers\News\NewsStatusController;
use Illuminate\Support\Facades\Route;

class NewsRoutes
{
    protected static string $resourcePrefix = '/news';
    protected static string $resourceName = 'news';

    public static function initRoutes(): void
    {
        Route::group(
            [
                'prefix' => self::$resourcePrefix,
                'as' => self::$resourceName,
            ],
            function () {
                Route::get('/generate-iri', [NewsController::class, 'generateIri'])->name('.generate-iri');
                Route::post('/{news_id}/update-status', [NewsStatusController::class, 'updateStatus'])
                    ->name('.update-status');
                Route::get('/{news_id}/approve', [NewsStatusController::class, 'approve'])->name('.approve');
                Route::get('/{news_id}/reject', [NewsStatusController::class, 'reject'])->name('.reject');
            }
        );

        Route::apiResource(self::$resourcePrefix, NewsController::class)->except(['index', 'show']);
    }
}
