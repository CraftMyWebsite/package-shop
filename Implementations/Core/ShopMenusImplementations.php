<?php

namespace CMW\Implementation\Shop\Core;

use CMW\Interface\Core\IMenus;
use CMW\Model\Shop\Category\ShopCategoriesModel;

class ShopMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        $catSlug = [];
        $catSlug['Shop'] = 'shop';

        foreach ((new ShopCategoriesModel())->getShopCategories() as $cat) {
            $catSlug['Cat : '.$cat->getName()] = 'shop/cat/' . $cat->getSlug();
        }

        return $catSlug;
    }

    public function getPackageName(): string
    {
        return 'Shop';
    }
}