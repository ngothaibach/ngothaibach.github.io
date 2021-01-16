<?php

Route::group(['middleware' => ['web', 'admin']], function () {

    Route::get('/admin/exchange', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@index')->defaults('_config', [
        'view' => 'exchange::admin.index',
    ])->name('exchange.admin.index');
    Route::get('/admin/exchange/create', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@create')->defaults('_config', [
        'view' => 'exchange::admin.create',
    ])->name('exchange.admin.create');
    Route::get('/admin/receipt_notes', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_receipt_notes')->defaults('_config', [
        'view' => 'exchange::admin.list_receipt_notes',
    ])->name('exchange.admin.list_receipt_notes');
    Route::get('/admin/transfer_notes', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_transfer_notes')->defaults('_config', [
        'view' => 'exchange::admin.list_transfer_notes',
    ])->name('exchange.admin.list_transfer_notes');

});