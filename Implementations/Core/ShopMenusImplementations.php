<?php

namespace CMW\Implementation\Shop\Core;

use CMW\Interface\Core\IMenus;
use CMW\Model\Shop\Category\ShopCategoriesModel;

class ShopMenusImplementations implements IMenus
{
    public function getRoutes(): array
    {
        $catSlug = [];
        $catSlug['Shop'] = 'shop';

        $catSlug['Panier'] = 'shop/cart';

        $catSlug['Historique'] = 'shop/history';

        $catSlug['Paramètres'] = 'shop/settings';

        foreach ((new ShopCategoriesModel())->getShopCategories() as $cat) {
            $catSlug['Cat : ' . $cat->getName()] = 'shop/cat/' . $cat->getSlug();
            foreach (ShopCategoriesModel::getInstance()->getSubsCat($cat->getId()) as $subcat) {
                $catSlug['Sous-Cat : ' . $subcat->getSubCategory()->getName()] = 'shop/cat/' . $subcat->getSubCategory()->getSlug();
            }
        }

        return $catSlug;
    }

    public function getPackageName(): string
    {
        return 'Shop';
    }
}
