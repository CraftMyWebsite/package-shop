<?php

namespace CMW\Controller\Shop\Admin\Item;

use CMW\Controller\Users\UsersController;
use CMW\Event\Shop\ShopAddItemEvent;
use CMW\Event\Shop\ShopDeleteItemEvent;
use CMW\Event\Shop\ShopEditItemEvent;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Interface\Shop\IPriceTypeMethod;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;

/**
 * Class: @ShopItemsController
 * @desc this controller manages: categories, items requirement, items actions, items tags
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopItemsController extends AbstractController
{
    #[Link('/items', Link::GET, [], '/cmw-admin/shop')]
    public function shopItems(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        View::createAdminView('Shop', 'Items/manage')
            ->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'review' => $review, 'allowReviews' => $allowReviews])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/items/archived', Link::GET, [], '/cmw-admin/shop')]
    public function shopItemsArchived(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        View::createAdminView('Shop', 'Items/archived')
            ->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'review' => $review, 'allowReviews' => $allowReviews])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/items/review/:id', Link::GET, [], '/cmw-admin/shop')]
    public function shopItemsReviews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $categoryModel = ShopCategoriesModel::getInstance();
        $item = ShopItemsModel::getInstance()->getShopItemsById($id);
        $imagesItem = ShopImagesModel::getInstance()->getShopImagesByItem($id);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();

        View::createAdminView('Shop', 'Items/review')
            ->addVariableList(['categoryModel' => $categoryModel, 'item' => $item, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'review' => $review])
            ->view();
    }

    #[Link('/items/review/:id/delete/:reviewId', Link::GET, [], '/cmw-admin/shop')]
    public function shopItemsDeleteReviews(int $id, int $reviewId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        ShopReviewsModel::getInstance()->deleteReview($reviewId);
        Flash::send(Alert::SUCCESS, 'Boutique', 'Avis supprimé !');
        Redirect::redirectPreviousRoute();
    }

    #[Link('/items/cat/:catId', Link::GET, ['.*?'], '/cmw-admin/shop')]
    public function shopItemsByCat(string $catId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $thisCat = ShopCategoriesModel::getInstance()->getShopCategoryById($catId);
        $items = ShopItemsModel::getInstance()->getShopItemByCat($catId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        View::createAdminView('Shop', 'Items/filterCat')
            ->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'thisCat' => $thisCat])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/items/add', Link::GET, [], '/cmw-admin/shop')]
    public function adminAddShopItem(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        if (ShopCategoriesModel::getInstance()->getShopCategories()) {
            $categoryModel = ShopCategoriesModel::getInstance();

            View::createAdminView('Shop', 'Items/add')
                ->addVariableList(['categoryModel' => $categoryModel, 'virtualMethods' => $this->getVirtualItemsMethods(), 'priceTypeMethods' => $this->getPriceTypeMethods()])
                ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                    'Admin/Resources/Vendors/Tinymce/Config/full.js')
                ->view();
        } else {
            Redirect::redirect('cmw-admin/shop/cat');
        }
    }

    #[Link('/items/add', Link::POST, [], '/cmw-admin/shop')]
    public function adminAddShopItemPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        [$name, $shortDesc, $category, $description, $type, $stock, $price, $priceType, $byOrderLimit, $globalLimit, $userLimit] = Utils::filterInput('shop_item_name', 'shop_item_short_desc', 'shop_category_id', 'shop_item_description', 'shop_item_type', 'shop_item_default_stock', 'shop_item_price', 'shop_item_price_type', 'shop_item_by_order_limit', 'shop_item_global_limit', 'shop_item_user_limit');

        $itemId = ShopItemsModel::getInstance()->createShopItem($name, $shortDesc, $category, $description, $type, ($stock === '' ? null : $stock), ($price === '' ? 0 : $price), $priceType, ($byOrderLimit === '' ? null : $byOrderLimit), ($globalLimit === '' ? null : $globalLimit), ($userLimit === '' ? null : $userLimit));

        // Variantes
        $variantNames = $_POST['shop_item_variant_name'] ?? [];
        $variantValues = $_POST['shop_item_variant_value'] ?? [];

        if (!empty($variantNames) && !empty($variantValues)) {
            foreach ($variantNames as $parentIndex => $variantName) {
                $variantId = ShopItemVariantModel::getInstance()->createVariant($variantName, $itemId);

                foreach ($variantValues[$parentIndex] as $variantValue) {
                    if ($variantValue === '') {
                        continue;
                    }
                    ShopItemVariantValueModel::getInstance()->addVariantValue($variantValue, $variantId->getId() ?? null);
                }
            }
        }

        [$numberOfImage] = Utils::filterInput('numberOfImage');
        if ($numberOfImage !== '') {
            for ($i = 0; $i < $numberOfImage; $i++) {
                $imageKey = 'image-' . $i;
                $orderKey = 'order-' . $i;

                if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES[$imageKey];
                    $order = isset($_POST[$orderKey]) ? intval($_POST[$orderKey]) : 0;
                    ShopImagesModel::getInstance()->addShopItemImage($image, $itemId, $order);
                }
            }
        }

        if ($type == '0') {
            [$weight, $length, $width, $height] = Utils::filterInput('shop_item_weight', 'shop_item_length', 'shop_item_width', 'shop_item_height');
            $length = is_null($length) ? 0 : (float) $length;
            $width = is_null($width) ? 0 : (float) $width;
            $height = is_null($height) ? 0 : (float) $height;
            ShopItemsPhysicalRequirementModel::getInstance()->createPhysicalRequirement($itemId, $weight, $length, $width, $height);
        }

        if ($type == '1') {
            [$varName] = Utils::filterInput('shop_item_virtual_method_var_name');
            if (!empty($varName)) {
                $validPrefixes = Utils::filterInput('shop_item_virtual_prefix');
                $virtualMethod = ShopItemsVirtualMethodModel::getInstance()->insertMethod($varName, $itemId);
                $virtualMethodId = $virtualMethod->getId();

                foreach ($_POST as $key => $value) {
                    foreach ($validPrefixes as $prefix) {
                        // Vérifiez si la clé commence par un des préfixes valides
                        if (str_starts_with($key, $prefix)) {
                            $widgetKey = FilterManager::filterData($key, 50);
                            $widgetValue = FilterManager::filterData($value, 255);
                            if ($widgetKey != $widgetValue) {
                                if (!ShopItemsVirtualRequirementModel::getInstance()->insertSetting($virtualMethodId, $key . $itemId, $value)) {
                                    Flash::send(Alert::ERROR, 'Erreur',
                                        "Impossible de mettre à jour le paramètre $widgetKey.");
                                    Redirect::redirect('cmw-admin/shop/items');
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        Flash::send(Alert::SUCCESS, 'Success', 'Article ajouté !');

        Emitter::send(ShopAddItemEvent::class, $itemId);

        Redirect::redirect('cmw-admin/shop/items');
    }

    #[Link('/items/edit/:id', Link::GET, [], '/cmw-admin/shop')]
    public function adminEditShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $categoryModel = ShopCategoriesModel::getInstance();
        $item = ShopItemsModel::getInstance()->getShopItemsById($id);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $physicalInfo = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($id);
        $itemVariants = ShopItemVariantModel::getInstance()->getShopItemVariantByItemId($id);
        $variantValuesModel = ShopItemVariantValueModel::getInstance();

        View::createAdminView('Shop', 'Items/edit')
            ->addVariableList(['categoryModel' => $categoryModel, 'item' => $item, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'physicalInfo' => $physicalInfo, 'virtualMethods' => $this->getVirtualItemsMethods(), 'priceTypeMethods' => $this->getPriceTypeMethods(), 'itemVariants' => $itemVariants, 'variantValuesModel' => $variantValuesModel])
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->view();
    }

    #[Link('/items/edit/:id', Link::POST, [], '/cmw-admin/shop')]
    public function adminEditShopItemPost(int $id): void
    {
        $backupItemInfo = ShopItemsModel::getInstance()->getShopItemsById($id);

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        [$name, $shortDesc, $category, $description, $type, $stock, $price, $priceType, $byOrderLimit, $globalLimit, $userLimit] = Utils::filterInput('shop_item_name', 'shop_item_short_desc', 'shop_category_id', 'shop_item_description', 'shop_item_type', 'shop_item_default_stock', 'shop_item_price', 'shop_item_price_type', 'shop_item_by_order_limit', 'shop_item_global_limit', 'shop_item_user_limit');

        ShopItemsModel::getInstance()->editShopItem($id, $name, $shortDesc, $category, $description, $type, ($stock === '' ? null : $stock), ($price === '' ? 0 : $price), $priceType, ($byOrderLimit === '' ? null : $byOrderLimit), ($globalLimit === '' ? null : $globalLimit), ($userLimit === '' ? null : $userLimit));

        // Variantes
        $variantNames = $_POST['shop_item_variant_name'] ?? [];
        $variantValues = $_POST['shop_item_variant_value'] ?? [];

        if (!empty($variantNames) && !empty($variantValues)) {
            ShopItemVariantModel::getInstance()->clearVariants($id);
            ShopCartItemModel::getInstance()->removeItemForAllCart($id);
            // todo notify user item has been removed bcs variantes changed
            foreach ($variantNames as $parentIndex => $variantName) {
                $variantId = ShopItemVariantModel::getInstance()->createVariant($variantName, $id);
                foreach ($variantValues[$parentIndex] as $variantValue) {
                    if ($variantValue === '') {
                        continue;
                    }
                    ShopItemVariantValueModel::getInstance()->addVariantValue($variantValue, $variantId->getId() ?? null);
                }
            }
        } elseif (ShopItemVariantModel::getInstance()->itemHasVariant($id)) {
            ShopItemVariantModel::getInstance()->clearVariants($id);
            ShopCartItemModel::getInstance()->removeItemForAllCart($id);
            // todo notify user item has been removed bcs variantes changed
        }

        ShopImagesModel::getInstance()->clearImages($id);

        [$numberOfImage] = Utils::filterInput('numberOfImage');

        if ($numberOfImage !== '') {
            for ($i = 0; $i < $numberOfImage; $i++) {
                $imageKey = 'image-' . $i;
                $existingImageKey = 'image-existing-' . $i;
                $orderKey = 'order-' . $i;

                if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES[$imageKey];
                    $order = isset($_POST[$orderKey]) ? intval($_POST[$orderKey]) : 0;
                    ShopImagesModel::getInstance()->addShopItemImage($image, $id, $order);
                } elseif (isset($_POST[$existingImageKey])) {
                    $image = $_POST[$existingImageKey];
                    $order = isset($_POST[$orderKey]) ? intval($_POST[$orderKey]) : 0;
                    ShopImagesModel::getInstance()->addReuseShopItemImage($image, $id, $order);
                }
            }
        }

        if ($type == '0') {
            [$weight, $length, $width, $height] = Utils::filterInput('shop_item_weight', 'shop_item_length', 'shop_item_width', 'shop_item_height');
            $length = is_null($length) ? 0 : (float) $length;
            $width = is_null($width) ? 0 : (float) $width;
            $height = is_null($height) ? 0 : (float) $height;
            if ($backupItemInfo->getType() === 0) {
                ShopItemsPhysicalRequirementModel::getInstance()->updatePhysicalRequirement($id, $weight, $length, $width, $height);
            } else {
                ShopItemsVirtualMethodModel::getInstance()->clearMethod($id);  // This model also clear setting automatically
                ShopItemsPhysicalRequirementModel::getInstance()->createPhysicalRequirement($id, $weight, $length, $width, $height);
                ShopCartItemModel::getInstance()->removeItemForAllCart($id);
                // todo notify user item has been removed bcs type changed
            }
        }

        // TODO : If this change : clear basket and notify user type changed
        if ($type == '1') {
            [$varName] = Utils::filterInput('shop_item_virtual_method_var_name');
            if (!empty($varName)) {
                $validPrefixes = Utils::filterInput('shop_item_virtual_prefix');
                if ($backupItemInfo->getType() === 1) {
                    $updatedVirtualMethod = ShopItemsVirtualMethodModel::getInstance()->updateMethod($varName, $id);
                    $updatedVirtualMethodId = $updatedVirtualMethod->getId();
                    ShopItemsVirtualRequirementModel::getInstance()->clearSetting($updatedVirtualMethodId);
                    foreach ($_POST as $key => $value) {
                        foreach ($validPrefixes as $prefix) {
                            // Vérifiez si la clé commence par un des préfixes valides
                            if (str_starts_with($key, $prefix)) {
                                $widgetKey = FilterManager::filterData($key, 50);
                                $widgetValue = FilterManager::filterData($value, 255);
                                if ($widgetKey != $widgetValue) {
                                    if (!ShopItemsVirtualRequirementModel::getInstance()->insertSetting($updatedVirtualMethodId, $key . $id, $value)) {
                                        Flash::send(Alert::ERROR, 'Erreur',
                                            "Impossible de mettre à jour le paramètre $widgetKey.");
                                        Redirect::redirect('cmw-admin/shop/items');
                                    }
                                }
                                break;
                            }
                        }
                    }
                } else {
                    ShopItemsPhysicalRequirementModel::getInstance()->clearPhysicalRequirement($id);
                    $virtualMethod = ShopItemsVirtualMethodModel::getInstance()->insertMethod($varName, $id);
                    $virtualMethodId = $virtualMethod->getId();
                    foreach ($_POST as $key => $value) {
                        foreach ($validPrefixes as $prefix) {
                            // Vérifiez si la clé commence par un des préfixes valides
                            if (str_starts_with($key, $prefix)) {
                                $widgetKey = FilterManager::filterData($key, 50);
                                $widgetValue = FilterManager::filterData($value, 255);
                                if ($widgetKey != $widgetValue) {
                                    if (!ShopItemsVirtualRequirementModel::getInstance()->insertSetting($virtualMethodId, $key . $id, $value)) {
                                        Flash::send(Alert::ERROR, 'Erreur',
                                            "Impossible de mettre à jour le paramètre $widgetKey.");
                                        Redirect::redirect('cmw-admin/shop/items');
                                    }
                                }
                                break;
                            }
                        }
                    }
                    ShopCartItemModel::getInstance()->removeItemForAllCart($id);
                    // todo notify user item has been removed bcs type changed
                }
            }
        }

        ShopImagesModel::getInstance()->clearLocalNonUsedImages();

        Flash::send(Alert::SUCCESS, 'Success', 'Article modifier !');

        Emitter::send(ShopEditItemEvent::class, $id);

        Redirect::redirectPreviousRoute();
    }

    #[Link('/items/delete/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    public function adminDeleteShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $isInACart = ShopCartItemModel::getInstance()->itemIsPresentInACart($id);
        $isOrdered = ShopHistoryOrdersItemsModel::getInstance()->itemIsOrdered($id);

        if (!$isInACart || !$isOrdered) {
            Flash::send(Alert::ERROR, 'Boutique', 'Suppression impossible. il est donc maintenant archivé !<br> Rendez-vous dans la page des archives pour en savoir plus');
            if (!$isInACart) {
                ShopItemsModel::getInstance()->archiveItem($id, 1);
            }
            if (!$isOrdered) {
                ShopItemsModel::getInstance()->archiveItem($id, 2);
            }
        }

        if ($isInACart && $isOrdered) {
            ShopItemsModel::getInstance()->deleteShopItem($id);
            Flash::send(Alert::SUCCESS, 'Boutique', "Cet article n'existe plus");
            Emitter::send(ShopDeleteItemEvent::class, $id);
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link('/items/activate/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    public function adminActivateShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        ShopItemsModel::getInstance()->unarchivedItem($id);

        Flash::send(Alert::SUCCESS, 'Boutique', "L'article est à nouveau disponible");

        Redirect::redirect('cmw-admin/shop/items');
    }

    /**
     * @return \CMW\Interface\Shop\IVirtualItems[]
     */
    public function getVirtualItemsMethods(): array
    {
        return Loader::loadImplementations(IVirtualItems::class);
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IVirtualItems|null
     */
    public function getVirtualItemsMethodsByVarName(string $varName): ?IVirtualItems
    {
        foreach ($this->getVirtualItemsMethods() as $virtualMethod) {
            if ($virtualMethod->varName() === $varName) {
                return $virtualMethod;
            }
        }
        return null;
    }

    /**
     * @return \CMW\Interface\Shop\IPriceTypeMethod[]
     */
    public function getPriceTypeMethods(): array
    {
        return Loader::loadImplementations(IPriceTypeMethod::class);
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IPriceTypeMethod|null
     */
    public function getPriceTypeMethodsByVarName(string $varName): ?IPriceTypeMethod
    {
        foreach ($this->getPriceTypeMethods() as $priceTypeMethod) {
            if ($priceTypeMethod->varName() === $varName) {
                return $priceTypeMethod;
            }
        }
        return null;
    }

    /**
     * @return \CMW\Interface\Shop\IVirtualItems[]
     */
    public function getGlobalVarVirtualItemsMethods(): array
    {
        $allVirtualItemMethods = Loader::loadImplementations(IVirtualItems::class);
        return array_filter($allVirtualItemMethods, function ($virtualItemMethods) {
            return $virtualItemMethods->useGlobalConfigWidgetsInShopConfig();
        });
    }
}
