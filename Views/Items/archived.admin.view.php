<?php

use CMW\Type\Shop\Enum\Item\ShopItemType;
use CMW\Type\Shop\Enum\Shop\ShopType;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

$title = LangManager::translate('shop.views.items.archived.title');
$description = '';

/* @var CMW\Entity\Shop\Categories\ShopCategoryEntity[] $categories */
/* @var CMW\Model\Shop\Item\ShopItemsModel $items */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <?= LangManager::translate('shop.views.items.archived.title') ?></h3>
    <a href="../items" type="submit" class="btn-primary"><?= LangManager::translate('shop.views.items.archived.back') ?></a>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="alert alert-warning my-4"><?= LangManager::translate('shop.views.items.archived.info') ?></div>

<div class="table-container">
    <table id="table1" data-load-per-page="10">
        <thead>
        <tr>
            <th><?= LangManager::translate('shop.views.items.archived.img') ?></th>
            <th><?= LangManager::translate('shop.views.items.archived.name') ?></th>
            <?php if ($allowReviews): ?>
                <th class="text-center"><?= LangManager::translate('shop.views.items.archived.reviews') ?></th>
            <?php endif; ?>
            <th><?= LangManager::translate('shop.views.items.archived.reason') ?></th>
            <th><?= LangManager::translate('shop.views.items.archived.price') ?></th>
            <th><?= LangManager::translate('shop.views.items.archived.stock') ?></th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($items->getShopArchivedItems() as $item): ?>
            <?php
            $itemType = $item->getType();
            $isInvalid =
                ($shopType === ShopType::VIRTUAL_ONLY && $itemType === ShopItemType::PHYSICAL) ||
                ($shopType === ShopType::PHYSICAL_ONLY && $itemType === ShopItemType::VIRTUAL);
            ?>
            <tr style="<?= $isInvalid ? 'opacity: 0.6;' : '' ?>">
                <td class="text-center" style="width: 6rem; height: 6rem;">
                    <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId()); ?>
                    <?php $v = count($getImagesItem); ?>
                    <?php if ($getImagesItem): ?>
                        <?php if ($v !== 1): ?>
                            <div class="slider-container relative w-full max-w-2xl mx-auto" data-height="80px">
                                <?php foreach ($getImagesItem as $imagesUrl): ?>
                                    <img src="<?= $imagesUrl->getImageUrl() ?>" alt="..">
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($getImagesItem as $imageUrl): ?>
                                <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                                     src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block"
                                     alt="..."/>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                             src="<?= $defaultImage ?>" class="p-2 d-block"
                             alt="..."/>
                    <?php endif; ?>
                </td>
                <td style="width: fit-content;">
                    <h6>
                        <?= mb_strimwidth($item->getName(), 0, 30, '...') ?>
                    </h6>
                    <span class="badge-info"><?= $item->getType()->label() ?></span>
                </td>
                <?php if ($allowReviews): ?>
                    <td class="text-center">
                        <a href="items/review/<?= $item->getId() ?>">
                            <?= $review->countTotalRatingByItemId($item->getId()) ?> avis<br>
                            <?= $review->getStars($item->getId()) ?>
                        </a>
                    </td>
                <?php endif; ?>
                <td style="max-width: 5rem;">
                    <?= $item->getArchivedReason() ?>
                </td>
                <td>
                    <?= $item->getPriceFormatted() ?>
                </td>
                <td>
                    <?= $item->getAdminFormattedStock() ?>
                </td>
                <td class="text-center space-x-2">
                    <button data-modal-toggle="modal-push-<?= $item->getId() ?>" type="button"
                        <?= $isInvalid ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                        <i data-bs-toggle="tooltip" title="<?= $isInvalid ? 'Incompatible avec le mode de boutique actuel' : 'Activer' ?>"
                           class="text-success fas fa-rocket"></i>
                    </button>
                </td>
            </tr>

            <div id="modal-push-<?= $item->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header">
                        <h6><?= LangManager::translate('shop.views.items.archived.unarchive') ?> <?= $item->getName() ?></h6>
                        <button type="button" data-modal-hide="modal-push-<?= $item->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <?php if ($isInvalid): ?>
                            <p class="text-danger">Cet article ne peut pas être réactivé avec le type de boutique actuel.</p>
                        <?php else: ?>
                            <p><?= LangManager::translate('shop.views.items.archived.unarchive-warn') ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <?php if (!$isInvalid): ?>
                            <a href="activate/<?= $item->getId() ?>"
                               class="btn-primary">
                                <?= LangManager::translate('shop.views.items.archived.confirm') ?>
                            </a>
                        <?php else: ?>
                            <button class="btn-secondary" disabled>Action impossible</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
