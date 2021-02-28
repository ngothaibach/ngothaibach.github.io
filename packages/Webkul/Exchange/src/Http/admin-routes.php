<?php

Route::group(['middleware' => ['web', 'admin']], function () {

    Route::get('/admin/exchange', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@index')->defaults('_config', [
        'view' => 'exchange::admin.index',
    ])->name('exchange.admin.index');
    Route::get('/admin/exchange/create', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@create')->defaults('_config', [
        'view' => 'exchange::admin.create',
    ])->name('exchange.admin.create');    
    Route::get('/admin/transfer_notes', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_transfer_notes')->defaults('_config', [
        'view' => 'exchange::admin.list_transfer_notes',
    ])->name('exchange.admin.list_transfer_notes');
    Route::post('admin/exchange/store', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@store')->name('admin.exchange.store');
    Route::get('/admin/exchange/get_transfered_products', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@get_transfered_products')->name('exchange.admin.get_transfered_products');
    
    Route::get('/admin/purchase/create', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@create_purchase')->defaults('_config', [
        'view' => 'exchange::admin.purchase.create',
    ])->name('admin.exchange.purchase-order.create');
    Route::get('/admin/purchase/list', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_purchases')->defaults('_config', [
        'view' => 'exchange::admin.purchase.index',
    ])->name('admin.exchange.purchase-order.list');
    Route::get('/admin/transfer/create', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@create_transfer')->defaults('_config', [
        'view' => 'exchange::admin.transfer.create',
    ])->name('admin.exchange.transfer.create');
    Route::get('/admin/transfer/list', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_transfers')->defaults('_config', [
        'view' => 'exchange::admin.transfer.index',
    ])->name('admin.exchange.transfer.list');
    //minhpd
    // Route::get('/admin/orders/list', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@list_orders')->defaults('_config', [
    //     'view' => 'exchange::admin.orders.index',
    // ])->name('admin.exchange.orders.list');
    // Route::get('/admin/orders/create', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@create_orders')->defaults('_config', [
    //     'view' => 'exchange::admin.orders.create',
    // ])->name('admin.exchange.orders.create');
    // Route::post('admin/orders/store', 'Webkul\Exchange\Http\Controllers\Admin\ExchangeController@store_orders')->name('admin.orders.store');
});