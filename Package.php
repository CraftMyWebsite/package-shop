<?php

namespace CMW\Package\Shop;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'Shop';
    }

    public function version(): string
    {
        return '0.0.1';
    }

    public function authors(): array
    {
        return ['Teyir', 'Zomb'];
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
                title: 'Boutique',
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-sliders mr-2"></i> Réglages',
                        permission: 'shop.config',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: 'Globaux',
                                permission: 'shop.config',
                                url: 'shop/settings/global',
                            ),
                            new PackageSubMenuType(
                                title: 'Méthodes',
                                permission: 'shop.config',
                                url: 'shop/settings/methods',
                            ),
                            new PackageSubMenuType(
                                title: 'Livraisons',
                                permission: 'shop.shipping',
                                url: 'shop/shipping',
                            ),
                            new PackageSubMenuType(
                                title: 'Paiements',
                                permission: 'shop.payment',
                                url: 'shop/payments',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-boxes-stacked mr-2"></i> Articles & Catégories',
                        permission: 'shop.items',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: 'Articles',
                                permission: 'shop.items',
                                url: 'shop/items',
                            ),
                            new PackageSubMenuType(
                                title: 'Catégories',
                                permission: 'shop.cat',
                                url: 'shop/cat',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-ticket mr-2"></i> Avantages Client',
                        permission: 'shop.discount',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: 'Promotions',
                                permission: 'shop.discount',
                                url: 'shop/discounts',
                            ),
                            new PackageSubMenuType(
                                title: 'Avoirs / Credits',
                                permission: 'shop.credit',
                                url: 'shop/credits',
                            ),
                            new PackageSubMenuType(
                                title: 'Carte cadeau',
                                permission: 'shop.gift',
                                url: 'shop/giftCard',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-cash-register mr-2"></i> Commandes',
                        permission: 'shop.order',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: 'En cours',
                                permission: 'shop.order',
                                url: 'shop/orders/inProgress',
                            ),
                            new PackageSubMenuType(
                                title: 'Terminé',
                                permission: 'shop.order',
                                url: 'shop/orders/ended',
                            ),
                            new PackageSubMenuType(
                                title: 'Annulé',
                                permission: 'shop.order',
                                url: 'shop/orders/canceled',
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-headset mr-3"></i> S.A.V',
                        permission: 'shop.afterSales',
                        url: 'shop/afterSales',
                    ),
                    new PackageSubMenuType(
                        title: '<i class="fa-solid fa-chart-line mr-2"></i> Stats',
                        permission: 'shop.stats',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: 'Statistiques global',
                                permission: 'shop.stats',
                                url: 'shop/statistics',
                            ),
                            new PackageSubMenuType(
                                title: 'Paniers',
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

    public function uninstall(): bool
    {
        return false;
    }
}
