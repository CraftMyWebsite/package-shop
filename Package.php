<?php

namespace CMW\Package\Shop;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "Shop";
    }

    public function version(): string
    {
        return "0.0.1";
    }

    public function authors(): array
    {
        return ["Teyir", "Zomb"];
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
                lang: "fr",
                icon: "fas fa-shop",
                title: "Boutique",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Configuration',
                        permission: 'todo', //TODO PERM
                        url: 'shop/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Catégories',
                        permission: 'todo', //TODO PERM
                        url: 'shop/cat',
                    ),
                    new PackageSubMenuType(
                        title: 'Articles',
                        permission: 'todo', //TODO PERM
                        url: 'shop/items',
                    ),
                    new PackageSubMenuType(
                        title: 'Promotions',
                        permission: 'todo', //TODO PERM
                        url: 'shop/discounts',
                    ),
                    new PackageSubMenuType(
                        title: 'Paniers',
                        permission: 'todo', //TODO PERM
                        url: 'shop/carts',
                    ),
                    new PackageSubMenuType(
                        title: 'Commandes',
                        permission: 'todo', //TODO PERM
                        url: 'shop/orders',
                    ),
                    new PackageSubMenuType(
                        title: 'Livraisons',
                        permission: 'todo', //TODO PERM
                        url: 'shop/deliveries',
                    ),
                    new PackageSubMenuType(
                        title: 'Paiements',
                        permission: 'todo', //TODO PERM
                        url: 'shop/payments',
                    ),
                ]
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-shop",
                title: "Boutique",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Configuration',
                        permission: 'todo', //TODO PERM
                        url: 'shop/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Categories',
                        permission: 'todo', //TODO PERM
                        url: 'shop/cat',
                    ),
                    new PackageSubMenuType(
                        title: 'Items',
                        permission: 'todo', //TODO PERM
                        url: 'shop/items',
                    ),
                    new PackageSubMenuType(
                        title: 'Discounts',
                        permission: 'todo', //TODO PERM
                        url: 'shop/discounts',
                    ),
                    new PackageSubMenuType(
                        title: 'Carts',
                        permission: 'todo', //TODO PERM
                        url: 'shop/carts',
                    ),
                    new PackageSubMenuType(
                        title: 'Orders',
                        permission: 'todo', //TODO PERM
                        url: 'shop/orders',
                    ),
                    new PackageSubMenuType(
                        title: 'Deliveries',
                        permission: 'todo', //TODO PERM
                        url: 'shop/deliveries',
                    ),
                    new PackageSubMenuType(
                        title: 'Payments',
                        permission: 'todo', //TODO PERM
                        url: 'shop/payments',
                    ),
                ]
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ["Core"];
    }
}