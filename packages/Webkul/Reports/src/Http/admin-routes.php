<?php

Route::group(['middleware' => ['web', 'admin']], function () {

    Route::get('/admin/reports', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index')->defaults('_config', [
        'view' => 'reports::admin.index',
    ])->name('reports.admin.index');

    Route::post('admin/reports/export_sales', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@export_sales_report')->defaults('_config', [
        'view' => 'reports::admin.index',
    ])->name('reports.admin.export_sales_report');
    // Route::get('/admin/reports/customers', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index')->defaults('_config', [
    //     'view' => 'reports::admin.customers',
    // ])->name('reports.admin.customers');
    // Route::get('/admin/reports', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index')->defaults('_config', [
    //     'view' => 'reports::admin.staff',
    // ])->name('reports.admin.staff');

});