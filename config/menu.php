<?php

return [
    'admin' => [
    	[
            'key'        => 'report',
            'name'       => 'Báo cáo',
            'route'      => 'admin.dashboard.index',
            'sort'       => 2,
            'icon-class' => 'dashboard-icon',
        ],[
            'key' => 'exchange',
            'name' => 'Quản lý kho',
            'route' => 'exchange.admin.index',
            'sort' => 3,
            'icon-class' => 'cms-icon',
        ],[
            'key' => 'exchange.list_purchases',
            'name' => 'Nhập hàng',
            'route' => 'admin.exchange.purchase-order.list',
            'sort' => 1,
            'icon-class' => 'cms-icon',
        ],[
            'key' => 'exchange.list_transfer',
            'name' => 'Chuyển hàng',
            'route' => 'admin.exchange.transfer.list',
            'sort' => 2,
            'icon-class' => 'cms-icon',
        ],[
            'key'        => 'exchange.vptinventory',
            'name'       => 'Tồn kho',
            'route'      => 'admin.inventory_sources.inventories',
            'sort'       => 3,
            'icon-class' => 'catalog-icon',
        ], [
            'key'        => 'catalog',
            'name'       => 'admin::app.layouts.catalog',
            'route'      => 'admin.catalog.products.index',
            'sort'       => 4,
            'icon-class' => 'catalog-icon',
        ], [
            'key'        => 'catalog.products',
            'name'       => 'admin::app.layouts.products',
            'route'      => 'admin.catalog.products.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'catalog.categories',
            'name'       => 'admin::app.layouts.categories',
            'route'      => 'admin.catalog.categories.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'catalog.attributes',
            'name'       => 'admin::app.layouts.attributes',
            'route'      => 'admin.catalog.attributes.index',
            'sort'       => 3,
            'icon-class' => '',
        ], [
            'key'        => 'catalog.families',
            'name'       => 'admin::app.layouts.attribute-families',
            'route'      => 'admin.catalog.families.index',
            'sort'       => 4,
            'icon-class' => '',
        ],[
            'key'        => 'sales',
            'name'       => 'admin::app.layouts.sales',
            'route'      => 'admin.sales.orders.index',
            'sort'       => 5,
            'icon-class' => 'sales-icon',
        ], [
            'key'        => 'sales.orders',
            'name'       => 'admin::app.layouts.orders',
            'route'      => 'admin.sales.orders.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'sales.shipments',
            'name'       => 'admin::app.layouts.shipments',
            'route'      => 'admin.sales.shipments.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'sales.invoices',
            'name'       => 'admin::app.layouts.invoices',
            'route'      => 'admin.sales.invoices.index',
            'sort'       => 3,
            'icon-class' => '',
        ], [
            'key'        => 'sales.refunds',
            'name'       => 'admin::app.layouts.refunds',
            'route'      => 'admin.sales.refunds.index',
            'sort'       => 4,
            'icon-class' => '',
        ], [
            'key'        => 'customers',
            'name'       => 'admin::app.layouts.customers',
            'route'      => 'admin.customer.index',
            'sort'       => 6,
            'icon-class' => 'customer-icon',
        ], [
            'key'        => 'customers.customers',
            'name'       => 'admin::app.layouts.customers',
            'route'      => 'admin.customer.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'customers.groups',
            'name'       => 'admin::app.layouts.groups',
            'route'      => 'admin.groups.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'customers.reviews',
            'name'       => 'admin::app.layouts.reviews',
            'route'      => 'admin.customer.review.index',
            'sort'       => 3,
            'icon-class' => '',
        ],[
            'key'        => 'supplier',
            'name'       => 'Nhà cung cấp',
            'route'      => 'admin.customer.index',
            'sort'       => 7,
            'icon-class' => 'customer-icon',
        ], [
            'key'        => 'configuration',
            'name'       => 'admin::app.layouts.configure',
            'route'      => 'admin.configuration.index',
            'sort'       => 8,
            'icon-class' => 'configuration-icon',
        ], [
            'key'        => 'settings',
            'name'       => 'admin::app.layouts.settings',
            'route'      => 'admin.locales.index',
            'sort'       => 9,
            'icon-class' => 'settings-icon',
        ], [
            'key'        => 'settings.locales',
            'name'       => 'admin::app.layouts.locales',
            'route'      => 'admin.locales.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'settings.currencies',
            'name'       => 'admin::app.layouts.currencies',
            'route'      => 'admin.currencies.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'settings.exchange_rates',
            'name'       => 'admin::app.layouts.exchange-rates',
            'route'      => 'admin.exchange_rates.index',
            'sort'       => 3,
            'icon-class' => '',
        ], [
            'key'        => 'settings.inventory_sources',
            'name'       => 'admin::app.layouts.inventory-sources',
            'route'      => 'admin.inventory_sources.index',
            'sort'       => 4,
            'icon-class' => '',
        ], [
            'key'        => 'settings.channels',
            'name'       => 'admin::app.layouts.channels',
            'route'      => 'admin.channels.index',
            'sort'       => 5,
            'icon-class' => '',
        ], [
            'key'        => 'settings.users',
            'name'       => 'admin::app.layouts.users',
            'route'      => 'admin.users.index',
            'sort'       => 6,
            'icon-class' => '',
        ], [
            'key'        => 'settings.users.users',
            'name'       => 'admin::app.layouts.users',
            'route'      => 'admin.users.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'settings.users.roles',
            'name'       => 'admin::app.layouts.roles',
            'route'      => 'admin.roles.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'settings.sliders',
            'name'       => 'admin::app.layouts.sliders',
            'route'      => 'admin.sliders.index',
            'sort'       => 7,
            'icon-class' => '',
        ], [
            'key'        => 'settings.taxes',
            'name'       => 'admin::app.layouts.taxes',
            'route'      => 'admin.tax-categories.index',
            'sort'       => 8,
            'icon-class' => '',
        ], [
            'key'        => 'settings.taxes.tax-categories',
            'name'       => 'admin::app.layouts.tax-categories',
            'route'      => 'admin.tax-categories.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'settings.taxes.tax-rates',
            'name'       => 'admin::app.layouts.tax-rates',
            'route'      => 'admin.tax-rates.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'marketing',
            'name'       => 'admin::app.layouts.marketing',
            'route'      => 'admin.catalog-rules.index',
            'sort'       => 10,
            'icon-class' => 'promotion-icon',
        ], [
            'key'        => 'marketing.promotions',
            'name'       => 'admin::app.layouts.promotions',
            'route'      => 'admin.catalog-rules.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.promotions.catalog-rules',
            'name'       => 'admin::app.promotions.catalog-rules.title',
            'route'      => 'admin.catalog-rules.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.promotions.cart-rules',
            'name'       => 'admin::app.promotions.cart-rules.title',
            'route'      => 'admin.cart-rules.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.email-marketing',
            'name'       => 'admin::app.layouts.email-marketing',
            'route'      => 'admin.email-templates.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.email-marketing.email-templates',
            'name'       => 'admin::app.layouts.email-templates',
            'route'      => 'admin.email-templates.index',
            'sort'       => 1,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.email-marketing.events',
            'name'       => 'admin::app.layouts.events',
            'route'      => 'admin.events.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.email-marketing.campaigns',
            'name'       => 'admin::app.layouts.campaigns',
            'route'      => 'admin.campaigns.index',
            'sort'       => 2,
            'icon-class' => '',
        ], [
            'key'        => 'marketing.email-marketing.subscribers',
            'name'       => 'admin::app.layouts.newsletter-subscriptions',
            'route'      => 'admin.customers.subscribers.index',
            'sort'       => 3,
            'icon-class' => '',
        ], [
            'key'        => 'cms',
            'name'       => 'admin::app.layouts.cms',
            'route'      => 'admin.cms.index',
            'sort'       => 11,
            'icon-class' => 'cms-icon',
        ], [
            'key'        => 'cms.pages',
            'name'       => 'admin::app.cms.pages.pages',
            'route'      => 'admin.cms.index',
            'sort'       => 1,
            'icon-class' => '',
        ]
    ],

    'customer' => [

    ]
];

?>