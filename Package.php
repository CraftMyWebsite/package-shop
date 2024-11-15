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
                        title: 'Configuration',
                        permission: 'shop.config',
                        url: 'shop/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Catégories',
                        permission: 'shop.cat',
                        url: 'shop/cat',
                    ),
                    new PackageSubMenuType(
                        title: 'Articles',
                        permission: 'shop.items',
                        url: 'shop/items',
                    ),
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
                    new PackageSubMenuType(
                        title: 'Paniers',
                        permission: 'shop.cart',
                        url: 'shop/carts',
                    ),
                    new PackageSubMenuType(
                        title: 'Commandes',
                        permission: 'shop.order',
                        url: 'shop/orders',
                    ),
                    new PackageSubMenuType(
                        title: 'S.A.V',
                        permission: 'shop.afterSales',
                        url: 'shop/afterSales',
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
                    new PackageSubMenuType(
                        title: 'Statistiques',
                        permission: 'shop.stats',
                        url: 'shop/statistics',
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
