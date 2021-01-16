<?php

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    Route::get('/exchange', 'Webkul\Exchange\Http\Controllers\Shop\ExchangeController@index')->defaults('_config', [
        'view' => 'exchange::shop.index',
    ])->name('exchange.shop.index');

});