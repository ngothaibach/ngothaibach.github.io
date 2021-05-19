<?php

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    Route::get('/reports', 'Webkul\Reports\Http\Controllers\Shop\ReportsController@index')->defaults('_config', [
        'view' => 'reports::shop.index',
    ])->name('reports.shop.index');

});