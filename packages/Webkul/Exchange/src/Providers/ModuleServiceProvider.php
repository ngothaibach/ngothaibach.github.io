<?php

namespace Webkul\Exchange\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Exchange\Models\ExchangeNote::class,
    ];
}