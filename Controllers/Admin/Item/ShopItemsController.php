<?php

namespace CMW\Controller\Shop\Admin\Item;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Enum\Item\ShopItemType;
use CMW\Event\Shop\ShopAddItemEvent;
use CMW\Event\Shop\ShopDeleteItemEvent;
use CMW\Event\Shop\ShopEditItemEvent;
use CMW\Interface\Shop\IGlobalConfig;
use CMW\Interface\Shop\IPriceTypeMethod;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function is_null;

/**
 * Class: @ShopItemsController
 * @desc this controller manages: categories, items requirement, items actions, items tags
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopItemsController extends AbstractController
{
    #[Link('/items', Link::GET, [], '/cmw-admin/shop')]
    private function shopItems(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $items = ShopItemsModel::getInstance()->getAdminShopItems();
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        $mailConfig = MailModel::getInstance()->getConfig();
        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');

        View::createAdminView('Shop', 'Items/manage')
            ->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage,
                'review' => $review, 'allowReviews' => $allowReviews, 'mailConfig' => $mailConfig, 'shopType' => $shopType])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/items/archived', Link::GET, [], '/cmw-admin/shop')]
    private function shopItemsArchived(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');

        View::createAdminView('Shop', 'Items/archived')
            ->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage,
                'review' => $review, 'allowReviews' => $allowReviews, 'shopType' => $shopType])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/items/review/:id', Link::GET, [], '/cmw-admin/shop')]
    private function shopItemsReviews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items');

        $categoryModel = ShopCategoriesModel::getInstance();
        $item = ShopItemsModel::getInstance()->getShopItemsById($id);
        $imagesItem = ShopImagesModel::getInstance()->getShopImagesByItem($id);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();

        View::createAdminView('Shop', 'Items/review')
            ->addVariableList(['categoryModel' => $categoryModel, 'item' => $item, 'imagesItem' => $imagesItem,
                'defaultImage' => $defaultImage, 'review' => $review])
            ->view();
    }

    #[NoReturn] #[Link('/items/review/delete/:reviewId', Link::GET, [], '/cmw-admin/shop')]
    private function shopItemsDeleteReviews(int $reviewId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.deleteRating');

        ShopReviewsModel::getInstance()->deleteReview($reviewId);
        Flash::send(Alert::SUCCESS, 'Boutique', 'Avis supprimé !');
        Redirect::redirectPreviousRoute();
    }

    #[Link('/items/add', Link::GET, [], '/cmw-admin/shop')]
    private function adminAddShopItem(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.add');

        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');
        $lockedType = $shopType === 'virtual' ? '1' : ($shopType === 'physical' ? '0' : null);
        $isLocked = $lockedType !== null;

        $reason = match ($shopType) {
            'virtual' => "Le type de boutique est défini sur 'Virtuel uniquement'.",
            'physical' => "Le type de boutique est défini sur 'Physique uniquement'.",
            default => ''
        };

        if (ShopCategoriesModel::getInstance()->getShopCategories()) {
            $categoryModel = ShopCategoriesModel::getInstance();

            View::createAdminView('Shop', 'Items/add')
                ->addVariableList(['categoryModel' => $categoryModel, 'lockedType' => $lockedType, 'isLocked' => $isLocked, 'reason' => $reason,
                    'virtualMethods' => $this->getVirtualItemsMethods(),
                    'priceTypeMethods' => $this->getPriceTypeMethods()])
                ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                    'Admin/Resources/Vendors/Tinymce/Config/full.js')
                ->view();
        } else {
            Redirect::redirect('cmw-admin/shop/cat');
        }
    }

    #[NoReturn] #[Link('/items/add', Link::POST, [], '/cmw-admin/shop')]
    private function adminAddShopItemPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.add');

        //TODO FilterManager
        [$name, $shortDesc, $category, $description, $type, $stock, $price, $priceType, $byOrderLimit, $globalLimit, $userLimit, $draft] = Utils::filterInput('shop_item_name', 'shop_item_short_desc', 'shop_category_id', 'shop_item_description', 'shop_item_type', 'shop_item_default_stock', 'shop_item_price', 'shop_item_price_type', 'shop_item_by_order_limit', 'shop_item_global_limit', 'shop_item_user_limit', 'shop_item_draft');

        $stock = empty($stock) ? null : $stock;
        $price = empty($price) ? 0 : $price;
        $byOrderLimit = empty($byOrderLimit) ? null : $byOrderLimit;
        $globalLimit = empty($globalLimit) ? null : $globalLimit;
        $userLimit = empty($userLimit) ? null : $userLimit;
        $draft = is_null($draft) ? 0 : 1;

        $itemId = ShopItemsModel::getInstance()->createShopItem(
            $name,
            $shortDesc,
            $category,
            $description,
            $type,
            $stock,
            $price,
            $priceType,
            $byOrderLimit,
            $globalLimit,
            $userLimit,
            $draft,
        );

        // Variantes
        $variantNames = $_POST['shop_item_variant_name'] ?? [];
        $variantValues = $_POST['shop_item_variant_value'] ?? [];

        if (!empty($variantNames) && !empty($variantValues)) {
            foreach ($variantNames as $parentIndex => $variantName) {
                $variantId = ShopItemVariantModel::getInstance()->createVariant($variantName, $itemId);

                foreach ($variantValues[$parentIndex] as $index => $variantValue) {
                    if (empty($variantValue)) {
                        continue;
                    }

                    $imageName = null;

                    if (isset($_FILES['shop_item_variant_value_image']['name'][$parentIndex][$index]) &&
                        $_FILES['shop_item_variant_value_image']['error'][$parentIndex][$index] === UPLOAD_ERR_OK) {

                        $image = [
                            'name' => $_FILES['shop_item_variant_value_image']['name'][$parentIndex][$index],
                            'type' => $_FILES['shop_item_variant_value_image']['type'][$parentIndex][$index],
                            'tmp_name' => $_FILES['shop_item_variant_value_image']['tmp_name'][$parentIndex][$index],
                            'error' => $_FILES['shop_item_variant_value_image']['error'][$parentIndex][$index],
                            'size' => $_FILES['shop_item_variant_value_image']['size'][$parentIndex][$index],
                        ];

                        try {
                            $imageName = ImagesManager::convertAndUpload($image, 'Shop/Variants');
                        } catch (ImagesException $e) {
                            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                                LangManager::translate('core.errors.upload.image'));
                            Redirect::redirectPreviousRoute();
                        }
                    }

                    ShopItemVariantValueModel::getInstance()->addVariantValue($variantValue, $variantId?->getId(), $imageName);
                }
            }
        }

        //TODO FilterManager
        [$numberOfImage] = Utils::filterInput('numberOfImage');
        if ($numberOfImage !== '') {
            for ($i = 0; $i < $numberOfImage; $i++) {
                $imageKey = 'image-' . $i;
                $orderKey = 'order-' . $i;

                if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES[$imageKey];
                    $order = isset($_POST[$orderKey]) ? (int)$_POST[$orderKey] : 0;
                    ShopImagesModel::getInstance()->addShopItemImage($image, $itemId, $order);
                }
            }
        }

        //TODO Proper type ??
        if ($type == '0') {
            [$weight, $length, $width, $height] = Utils::filterInput('shop_item_weight', 'shop_item_length', 'shop_item_width', 'shop_item_height');
            $length = is_null($length) ? 0 : (float)$length;
            $width = is_null($width) ? 0 : (float)$width;
            $height = is_null($height) ? 0 : (float)$height;
            ShopItemsPhysicalRequirementModel::getInstance()->createPhysicalRequirement($itemId, $weight, $length, $width, $height);
        }

        if ($type == '1') {
            [$varName] = Utils::filterInput('shop_item_virtual_method_var_name');
            if (!empty($varName)) {
                $validPrefixes = Utils::filterInput('shop_item_virtual_prefix');
                $virtualMethod = ShopItemsVirtualMethodModel::getInstance()->insertMethod($varName, $itemId);

                if (is_null($virtualMethod)) {
                    //TODO Throw an error ?
                }

                $virtualMethodId = $virtualMethod->getId();

                foreach ($_POST as $key => $value) {
                    foreach ($validPrefixes as $prefix) {
                        // Vérifiez si la clé commence par un des préfixes valides
                        if (str_starts_with($key, $prefix)) {
                            $widgetKey = FilterManager::filterData($key, 50);
                            $widgetValue = FilterManager::filterData($value, 255);
                            if ($widgetKey !== $widgetValue) {
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
    private function adminEditShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.edit');

        $categoryModel = ShopCategoriesModel::getInstance();
        $item = ShopItemsModel::getInstance()->getShopItemsById($id);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $physicalInfo = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($id);
        $itemVariants = ShopItemVariantModel::getInstance()->getShopItemVariantByItemId($id);
        $variantValuesModel = ShopItemVariantValueModel::getInstance();

        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');
        $itemType = $item->getType();

        $isInvalid =
            ($shopType === 'virtual' && $itemType === ShopItemType::PHYSICAL) ||
            ($shopType === 'physical' && $itemType === ShopItemType::VIRTUAL);

        if ($isInvalid) {
            Flash::send(Alert::ERROR, 'Erreur','Cet article ne peut pas être édité dans le mode de boutique actuel.');
            Redirect::redirect('cmw-admin/shop/items');
        }

        $lockedType = $shopType === 'virtual' ? '1' : ($shopType === 'physical' ? '0' : null);
        $isLocked = $lockedType !== null;

        $reason = match ($shopType) {
            'virtual' => "Le type de boutique est défini sur 'Virtuel uniquement'.",
            'physical' => "Le type de boutique est défini sur 'Physique uniquement'.",
            default => ''
        };

        View::createAdminView('Shop', 'Items/edit')
            ->addVariableList(['categoryModel' => $categoryModel, 'item' => $item, 'imagesItem' => $imagesItem, 'itemType' => $itemType, 'lockedType' => $lockedType, 'isLocked' => $isLocked, 'reason' => $reason,
                'defaultImage' => $defaultImage, 'physicalInfo' => $physicalInfo,
                'virtualMethods' => $this->getVirtualItemsMethods(), 'priceTypeMethods' => $this->getPriceTypeMethods(),
                'itemVariants' => $itemVariants, 'variantValuesModel' => $variantValuesModel])
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->view();
    }

    #[NoReturn] #[Link('/items/edit/:id', Link::POST, [], '/cmw-admin/shop')]
    private function adminEditShopItemPost(int $id): void
    {
        $backupItemInfo = ShopItemsModel::getInstance()->getShopItemsById($id);

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.edit');

        //TODO FilterManager
        [$name, $shortDesc, $category, $description, $type, $stock, $price, $priceType, $byOrderLimit, $globalLimit, $userLimit, $draft] = Utils::filterInput('shop_item_name', 'shop_item_short_desc', 'shop_category_id', 'shop_item_description', 'shop_item_type', 'shop_item_default_stock', 'shop_item_price', 'shop_item_price_type', 'shop_item_by_order_limit', 'shop_item_global_limit', 'shop_item_user_limit', 'shop_item_draft');

        $stock = empty($stock) ? null : $stock;
        $price = empty($price) ? 0 : $price;
        $byOrderLimit = empty($byOrderLimit) ? null : $byOrderLimit;
        $globalLimit = empty($globalLimit) ? null : $globalLimit;
        $userLimit = empty($userLimit) ? null : $userLimit;
        $draft = is_null($draft) ? 0 : 1;

        ShopItemsModel::getInstance()->editShopItem(
            $id,
            $name,
            $shortDesc,
            $category,
            $description,
            $type,
            $stock,
            $price,
            $priceType,
            $byOrderLimit,
            $globalLimit,
            $userLimit,
            $draft,
        );

        // Variantes
        $variantNames = $_POST['shop_item_variant_name'] ?? [];
        $variantValues = $_POST['shop_item_variant_value'] ?? [];

        if (!empty($variantNames) && !empty($variantValues)) {
            ShopItemVariantModel::getInstance()->clearVariants($id);
            ShopCartItemModel::getInstance()->removeItemForAllCart($id);
            // todo notify user item has been removed bcs variantes changed

            $variantImages = $_POST['shop_item_variant_value_image'] ?? [];

            foreach ($variantNames as $parentIndex => $variantName) {
                $variantId = ShopItemVariantModel::getInstance()->createVariant($variantName, $id);

                foreach ($variantValues[$parentIndex] as $index => $variantValue) {
                    if (empty($variantValue)) {
                        continue;
                    }

                    $imageName = $variantImages[$parentIndex][$index] ?? null;

                    if (isset($_FILES['shop_item_variant_value_image']['name'][$parentIndex][$index]) &&
                        $_FILES['shop_item_variant_value_image']['error'][$parentIndex][$index] === UPLOAD_ERR_OK) {

                        $image = [
                            'name' => $_FILES['shop_item_variant_value_image']['name'][$parentIndex][$index],
                            'type' => $_FILES['shop_item_variant_value_image']['type'][$parentIndex][$index],
                            'tmp_name' => $_FILES['shop_item_variant_value_image']['tmp_name'][$parentIndex][$index],
                            'error' => $_FILES['shop_item_variant_value_image']['error'][$parentIndex][$index],
                            'size' => $_FILES['shop_item_variant_value_image']['size'][$parentIndex][$index],
                        ];

                        try {
                            $imageName = ImagesManager::convertAndUpload($image, 'Shop/Variants');
                        } catch (ImagesException $e) {
                            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                                LangManager::translate('core.errors.upload.image'));
                            Redirect::redirectPreviousRoute();
                        }
                    }

                    ShopItemVariantValueModel::getInstance()->addVariantValue(
                        $variantValue,
                        $variantId?->getId(),
                        $imageName
                    );
                }
            }
        } elseif (ShopItemVariantModel::getInstance()->itemHasVariant($id)) {
            ShopItemVariantModel::getInstance()->clearVariants($id);
            ShopCartItemModel::getInstance()->removeItemForAllCart($id);
            // todo notify user item has been removed bcs variantes changed
        }

        ShopImagesModel::getInstance()->clearImages($id);

        //TODO FilterManager
        [$numberOfImage] = Utils::filterInput('numberOfImage');

        if (!empty($numberOfImage)) {
            for ($i = 0; $i < $numberOfImage; $i++) {
                $imageKey = 'image-' . $i;
                $existingImageKey = 'image-existing-' . $i;
                $orderKey = 'order-' . $i;

                if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES[$imageKey];
                    $order = isset($_POST[$orderKey]) ? (int)$_POST[$orderKey] : 0;
                    ShopImagesModel::getInstance()->addShopItemImage($image, $id, $order);
                } elseif (isset($_POST[$existingImageKey])) {
                    $image = $_POST[$existingImageKey];
                    $order = isset($_POST[$orderKey]) ? (int)$_POST[$orderKey] : 0;
                    ShopImagesModel::getInstance()->addReuseShopItemImage($image, $id, $order);
                }
            }
        }

        //TODO Proper type
        if ($type == '0') {
            [$weight, $length, $width, $height] = Utils::filterInput('shop_item_weight', 'shop_item_length', 'shop_item_width', 'shop_item_height');
            $length = is_null($length) ? 0 : (float)$length;
            $width = is_null($width) ? 0 : (float)$width;
            $height = is_null($height) ? 0 : (float)$height;
            if ($backupItemInfo?->getType() === ShopItemType::PHYSICAL) {
                ShopItemsPhysicalRequirementModel::getInstance()->updatePhysicalRequirement($id, $weight, $length, $width, $height);
            } else {
                ShopItemsVirtualMethodModel::getInstance()->clearMethod($id);  // This model also clear setting automatically
                ShopItemsPhysicalRequirementModel::getInstance()->createPhysicalRequirement($id, $weight, $length, $width, $height);
                ShopCartItemModel::getInstance()->removeItemForAllCart($id);
                // todo notify user item has been removed bcs type changed
            }
        }

        if ($type == '1') {
            [$varName] = Utils::filterInput('shop_item_virtual_method_var_name');
            if (!empty($varName)) {
                $validPrefixes = Utils::filterInput('shop_item_virtual_prefix');
                if ($backupItemInfo?->getType() === ShopItemType::VIRTUAL) {
                    $updatedVirtualMethod = ShopItemsVirtualMethodModel::getInstance()->updateMethod($varName, $id);
                    $updatedVirtualMethodId = $updatedVirtualMethod->getId();
                    ShopItemsVirtualRequirementModel::getInstance()->clearSetting($updatedVirtualMethodId);
                    foreach ($_POST as $key => $value) {
                        foreach ($validPrefixes as $prefix) {
                            // Vérifiez si la clé commence par un des préfixes valides
                            if (str_starts_with($key, $prefix)) {
                                $widgetKey = FilterManager::filterData($key, 50);
                                $widgetValue = FilterManager::filterData($value, 255);
                                if ($widgetKey !== $widgetValue) {
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
                    $virtualMethodId = $virtualMethod?->getId();
                    foreach ($_POST as $key => $value) {
                        foreach ($validPrefixes as $prefix) {
                            // Vérifiez si la clé commence par un des préfixes valides
                            if (str_starts_with($key, $prefix)) {
                                $widgetKey = FilterManager::filterData($key, 50);
                                $widgetValue = FilterManager::filterData($value, 255);
                                if ($widgetKey !== $widgetValue) {
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

    #[NoReturn] #[Link('/items/delete/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function adminDeleteShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.delete');

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

    #[NoReturn] #[Link('/items/activate/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function adminActivateShopItem(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.items.add');

        $item = ShopItemsModel::getInstance()->getShopItemsById($id);

        // Vérification de compatibilité avec le type de boutique
        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');
        $itemType = $item->getType();

        $isInvalid =
            ($shopType === 'virtual' && $itemType === ShopItemType::PHYSICAL) ||
            ($shopType === 'physical' && $itemType === ShopItemType::VIRTUAL);

        if ($isInvalid) {
            Flash::send(Alert::ERROR, 'Erreur', "Cet article ne peut pas être activé dans le mode de boutique actuel.");
            Redirect::redirect('cmw-admin/shop/items/archived');
        }

        if ($item?->getCategory()) {
            ShopItemsModel::getInstance()->unarchivedItem($id);
            Flash::send(Alert::SUCCESS, 'Boutique', "L'article est à nouveau disponible");
            Redirect::redirect('cmw-admin/shop/items');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', "La catégorie n'existe plus, cet article ne pourras plus jamais être activé");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @return IVirtualItems[]
     */
    public function getVirtualItemsMethods(): array
    {
        return Loader::loadImplementations(IVirtualItems::class);
    }

    /**
     * @param string $varName
     * @return IVirtualItems|null
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
     * @return IPriceTypeMethod[]
     */
    public function getPriceTypeMethods(): array
    {
        return Loader::loadImplementations(IPriceTypeMethod::class);
    }

    /**
     * @param string $varName
     * @return IPriceTypeMethod|null
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
     * @return IGlobalConfig[]
     */
    public function getGlobalConfigMethods(): array
    {
        return Loader::loadImplementations(IGlobalConfig::class);
    }

    /**
     * @param string $varName
     * @return IGlobalConfig|null
     */
    public function getGlobalConfigMethodsByVarName(string $varName): ?IGlobalConfig
    {
        foreach ($this->getGlobalConfigMethods() as $globalConfigMethod) {
            if ($globalConfigMethod->varName() === $varName) {
                return $globalConfigMethod;
            }
        }
        return null;
    }
}
