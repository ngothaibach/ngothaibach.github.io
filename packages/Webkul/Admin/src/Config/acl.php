<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'admin::app.acl.dashboard',
        'route' => 'admin.dashboard.index',
        'sort'  => 1,
    ], 
    [
        'key' => 'exchange',
        'name' => 'Quản lý kho',
        'route' => 'exchange.admin.index',
        'sort' => 2
    ],
    [
        'key' => 'exchange.list_purchases',
        'name' => 'Nhập hàng',
        'route' => 'admin.exchange.purchase-order.list',
        'sort' => 1
    ],
    [
        'key' => 'exchange.list_purchases.view',
        'name' => 'Xem',
        'route' => 'admin.exchange.purchase-order.list',
        'sort' => 1
    ],
    [
        'key'   => 'exchange.list_purchases.create',
        'name'  => 'Thêm',
        'route' => 'admin.exchange.purchase-order.create',
        'sort'  => 2
    ],
    [
        'key'   => 'exchange.list_purchases.update',
        'name'  => 'Sửa',
        'route' => 'admin.exchange.update',
        'sort'  => 3
    ],
    [
        'key' => 'exchange.list_transfer',
        'name' => 'Chuyển hàng',
        'route' => 'admin.exchange.transfer.list',
        'sort' => 2
    ],
    [
        'key' => 'exchange.list_transfer.view',
        'name' => 'xem',
        'route' => 'admin.exchange.transfer.list',
        'sort' => 1
    ],
    [
        'key' => 'exchange.list_transfer.create',
        'name' => 'Thêm',
        'route' => 'admin.exchange.transfer.create',
        'sort' => 2
    ],
    [
        'key' => 'exchange.list_transfer.update',
        'name' => 'Sửa',
        'route' => 'admin.exchange.updateTransfer',
        'sort' => 3
    ],
    [
        'key' => 'exchange.vptinventory',
        'name' => 'Tồn kho',
        'route'=> 'admin.inventory_sources.inventories',
        'sort' => 3
    ],
    [
        'key'   => 'sales',
        'name'  => 'admin::app.acl.sales',
        'route' => 'admin.sales.orders.index',
        'sort'  => 2,
    ], [
        'key'   => 'sales.orders',
        'name'  => 'admin::app.acl.orders',
        'route' => 'admin.sales.orders.index',
        'sort'  => 1,
    ], [
        'key'   => 'sales.invoices',
        'name'  => 'admin::app.acl.invoices',
        'route' => 'admin.sales.invoices.index',
        'sort'  => 2,
    ], [
        'key'   => 'sales.shipments',
        'name'  => 'admin::app.acl.shipments',
        'route' => 'admin.sales.shipments.index',
        'sort'  => 3,
    ], [
        'key'   => 'catalog',
        'name'  => 'admin::app.acl.catalog',
        'route' => 'admin.catalog.index',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.products',
        'name'  => 'admin::app.acl.products',
        'route' => 'admin.catalog.products.index',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.products.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.catalog.products.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.products.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.catalog.products.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.products.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.catalog.products.delete',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.categories',
        'name'  => 'admin::app.acl.categories',
        'route' => 'admin.catalog.categories.index',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.categories.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.catalog.categories.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.categories.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.catalog.categories.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.categories.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.catalog.categories.delete',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.attributes',
        'name'  => 'admin::app.acl.attributes',
        'route' => 'admin.catalog.attributes.index',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.attributes.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.catalog.attributes.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.attributes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.catalog.attributes.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.attributes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.catalog.attributes.delete',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.families',
        'name'  => 'admin::app.acl.attribute-families',
        'route' => 'admin.catalog.families.index',
        'sort'  => 4,
    ], [
        'key'   => 'catalog.families.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.catalog.families.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.families.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.catalog.families.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.families.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.catalog.families.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers',
        'name'  => 'admin::app.acl.customers',
        'route' => 'admin.customer.index',
        'sort'  => 4,
    ], [
        'key'   => 'customers.customers',
        'name'  => 'admin::app.acl.customers',
        'route' => 'admin.customer.index',
        'sort'  => 1,
    ], [
        'key'   => 'customers.customers.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.customer.create',
        'sort'  => 1,
    ], [
        'key'   => 'customers.customers.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.customer.edit',
        'sort'  => 2,
    ], [
        'key'   => 'customers.customers.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.customer.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers.groups',
        'name'  => 'admin::app.acl.groups',
        'route' => 'admin.groups.index',
        'sort'  => 2,
    ], [
        'key'   => 'customers.groups.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.groups.create',
        'sort'  => 1,
    ], [
        'key'   => 'customers.groups.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.groups.edit',
        'sort'  => 2,
    ], [
        'key'   => 'customers.groups.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.groups.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers.reviews',
        'name'  => 'admin::app.acl.reviews',
        'route' => 'admin.customer.review.index',
        'sort'  => 3,
    ], [
        'key'   => 'customers.reviews.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.customer.review.edit',
        'sort'  => 1,
    ], [
        'key'   => 'customers.reviews.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.customer.review.delete',
        'sort'  => 2,
    ], [
        'key'   => 'configuration',
        'name'  => 'admin::app.acl.configure',
        'route' => 'admin.configuration.index',
        'sort'  => 5,
    ], [
        'key'   => 'settings',
        'name'  => 'admin::app.acl.settings',
        'route' => 'admin.users.index',
        'sort'  => 6,
    ], [
        'key'   => 'settings.locales',
        'name'  => 'admin::app.acl.locales',
        'route' => 'admin.locales.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.locales.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.locales.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.locales.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.locales.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.locales.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.locales.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.currencies',
        'name'  => 'admin::app.acl.currencies',
        'route' => 'admin.currencies.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.currencies.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.currencies.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.currencies.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.currencies.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.currencies.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.currencies.delete',
        'sort'  => 3,
    ], 
    [
        'key'   => 'settings.exchange_rates',
        'name'  => 'admin::app.acl.exchange-rates',
        'route' => 'admin.exchange_rates.index',
        'sort'  => 3,
    ], 
    [
        'key'   => 'settings.exchange_rates.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.exchange_rates.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.exchange_rates.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.exchange_rates.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.exchange_rates.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.exchange_rates.delete',
        'sort'  => 3,
    ], 
    [
        'key'   => 'settings.inventory_sources',
        'name'  => 'admin::app.acl.inventory-sources',
        'route' => 'admin.inventory_sources.index',
        'sort'  => 4,
    ], [
        'key'   => 'settings.inventory_sources.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.inventory_sources.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.inventory_sources.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.inventory_sources.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.inventory_sources.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.inventory_sources.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.channels',
        'name'  => 'admin::app.acl.channels',
        'route' => 'admin.channels.index',
        'sort'  => 5,
    ], [
        'key'   => 'settings.channels.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.channels.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.channels.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.channels.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.channels.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.channels.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.users',
        'name'  => 'admin::app.acl.users',
        'route' => 'admin.users.index',
        'sort'  => 6,
    ], [
        'key'   => 'settings.users.users',
        'name'  => 'admin::app.acl.users',
        'route' => 'admin.users.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.users.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.users.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.users.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.users.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.users.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.users.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.users.roles',
        'name'  => 'admin::app.acl.roles',
        'route' => 'admin.roles.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.roles.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.roles.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.roles.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.roles.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.roles.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.roles.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.sliders',
        'name'  => 'admin::app.acl.sliders',
        'route' => 'admin.sliders.index',
        'sort'  => 7,
    ], [
        'key'   => 'settings.sliders.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.sliders.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.sliders.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.sliders.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.sliders.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.sliders.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.taxes',
        'name'  => 'admin::app.acl.taxes',
        'route' => 'admin.tax-categories.index',
        'sort'  => 8,
    ], [
        'key'   => 'settings.taxes.tax-categories',
        'name'  => 'admin::app.acl.tax-categories',
        'route' => 'admin.tax-categories.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-categories.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.tax-categories.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-categories.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.tax-categories.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-categories.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.tax-categories.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.taxes.tax-rates',
        'name'  => 'admin::app.acl.tax-rates',
        'route' => 'admin.tax-rates.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-rates.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.tax-rates.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-rates.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.tax-rates.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-rates.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.tax-rates.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing',
        'name'  => 'admin::app.acl.marketing',
        'route' => 'admin.cart-rules.index',
        'sort'  => 7,
    ], [
        'key'   => 'marketing.promotions',
        'name'  => 'admin::app.acl.promotions',
        'route' => 'admin.cart-rules.index',
        'sort'  => 7,
    ], [
        'key'   => 'marketing.promotions.cart-rules',
        'name'  => 'admin::app.acl.cart-rules',
        'route' => 'admin.cart-rules.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.promotions.cart-rules.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.cart-rules.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.promotions.cart-rules.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.cart-rules.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.promotions.cart-rules.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.cart-rules.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing.promotions.catalog-rules',
        'name'  => 'admin::app.acl.catalog-rules',
        'route' => 'admin.catalog-rules.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.promotions.catalog-rules.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.catalog-rules.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.promotions.catalog-rules.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.catalog-rules.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.promotions.catalog-rules.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.catalog-rules.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing.email-marketing',
        'name'  => 'admin::app.acl.email-marketing',
        'route' => 'admin.email-templates.index',
        'sort'  => 7,
    ], [
        'key'   => 'marketing.email-marketing.email-templates',
        'name'  => 'admin::app.acl.email-templates',
        'route' => 'admin.email-templates.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.email-templates.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.email-templates.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.email-templates.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.email-templates.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.email-marketing.email-templates.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.email-templates.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing.email-marketing.events',
        'name'  => 'admin::app.acl.events',
        'route' => 'admin.events.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.events.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.events.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.events.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.events.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.email-marketing.events.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.events.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing.email-marketing.campaigns',
        'name'  => 'admin::app.acl.campaigns',
        'route' => 'admin.campaigns.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.campaigns.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.campaigns.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.campaigns.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.campaigns.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.email-marketing.campaigns.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.campaigns.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketing.email-marketing.subscribers',
        'name'  => 'admin::app.acl.subscribers',
        'route' => 'admin.subscribers.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketing.email-marketing.subscribers.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.subscribers.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketing.email-marketing.subscribers.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.subscribers.delete',
        'sort'  => 3,
    ]
];

?>