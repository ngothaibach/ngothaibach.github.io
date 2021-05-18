<?php

Route::group(['middleware' => ['web', 'admin']], function () {
    //báo cáo bán hàng
    Route::get('/admin/reports_sales', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index')->defaults('_config', [
        'view' => 'reports::admin.index',
    ])->name('reports.admin.index');
    Route::post('admin/reports_sales/export', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@export_sales_report')->defaults('_config', [
        'view' => 'reports::admin.index',
    ])->name('reports.admin.export_sales_report');
    
    //báo cáo khách hàng
    Route::get('/admin/reports_customers', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index_customer')->defaults('_config', [
        'view' => 'reports::admin.customers',
    ])->name('reports.admin.customers');

    //báo cáo nhân viên
    Route::get('/admin/reports_staff', 'Webkul\Reports\Http\Controllers\Admin\ReportsController@index_staff')->defaults('_config', [
        'view' => 'reports::admin.staff',
    ])->name('reports.admin.staff');

});