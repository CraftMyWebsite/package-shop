<?php

namespace CMW\Package\Shop;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfigV2
{
    public function name(): string
    {
        return 'Shop';
    }

    public function version(): string
    {
        return '0.0.5';
    }

    public function cmwVersion(): string
    {
        return 'beta-01';
    }

    public function imageLink(): ?string
    {
        return null;
    }

    public function authors(): array
    {
        return ['Zomb'];
    }

    public function isGame(): bool
    {
        return false;
    }

    public function isCore(): bool
    {
        return false;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                icon: 'fas fa-shop',
                title: LangManager::translate('shop.menus.shop'),
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-sliders mr-2"></i> '. LangManager::translate('shop.menus.settings.setting'),
                        permission: 'shop.config',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.settings.global'),
                                permission: 'shop.config',
                                url: 'shop/settings/global',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.settings.method'),
                                permission: 'shop.config',
                                url: 'shop/settings/methods',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.settings.shipping'),
                                permission: 'shop.shipping',
                                url: 'shop/shipping',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.settings.payment'),
                                permission: 'shop.payment',
                                url: 'shop/payments',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-boxes-stacked mr-2"></i> '. LangManager::translate('shop.menus.items.item'),
                        permission: 'shop.items',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.items.items'),
                                permission: 'shop.items',
                                url: 'shop/items',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.items.cats'),
                                permission: 'shop.cat',
                                url: 'shop/cat',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-ticket mr-2"></i> '. LangManager::translate('shop.menus.discounts.discount'),
                        permission: 'shop.discount',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.discounts.discounts'),
                                permission: 'shop.discount',
                                url: 'shop/discounts',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.discounts.credit'),
                                permission: 'shop.credit',
                                url: 'shop/credits',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.discounts.gift'),
                                permission: 'shop.gift',
                                url: 'shop/giftCard',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-cash-register mr-2"></i> '. LangManager::translate('shop.menus.orders.order'),
                        permission: 'shop.order',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.orders.inProgress'),
                                permission: 'shop.order',
                                url: 'shop/orders/inProgress',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.orders.ended'),
                                permission: 'shop.order',
                                url: 'shop/orders/ended',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.orders.canceled'),
                                permission: 'shop.order',
                                url: 'shop/orders/canceled',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-headset mr-3"></i> ' . LangManager::translate('shop.menus.sav'),
                        permission: 'shop.afterSales',
                        url: 'shop/afterSales',
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-chart-line mr-2"></i> ' . LangManager::translate('shop.menus.stats.stat'),
                        permission: 'shop.stats',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.stats.global'),
                                permission: 'shop.stats',
                                url: 'shop/statistics',
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('shop.menus.stats.cart'),
                                permission: 'shop.cart',
                                url: 'shop/carts',
                            ),
                        ]
                    ),
                ]
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ['Core'];
    }

    public function compatiblesPackages(): array
    {
        return [];
    }

    public function uninstall(): bool
    {
        return false;
    }
}
