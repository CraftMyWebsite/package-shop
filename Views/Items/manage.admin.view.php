<?php

use CMW\Entity\Core\MailConfigEntity;
use CMW\Entity\Shop\Enum\Item\ShopItemType;
use CMW\Entity\Shop\Enum\Shop\ShopType;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate('shop.views.items.manage.title'));
Website::setDescription("");

/* @var ShopItemEntity [] $items */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var ShopSettingsModel $allowReviews */
/* @var ?MailConfigEntity $mailConfig */
/* @var string $shopType */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <?= LangManager::translate('shop.views.items.manage.title') ?></h3>
    <div>
        <a href="items/archived" type="submit" class="btn-warning"><?= LangManager::translate('shop.views.items.manage.archived') ?></a>
        <a href="items/add" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.add') ?></a>
    </div>
</div>

<?php if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="table-container">
    <table id="table1" data-load-per-page="10">
        <thead>
        <tr>
            <th><?= LangManager::translate('shop.views.items.manage.img') ?></th>
            <th><?= LangManager::translate('shop.views.items.manage.name') ?></th>
            <?php if ($allowReviews): ?>
                <th class="text-center"><?= LangManager::translate('shop.views.items.manage.review') ?></th>
            <?php endif; ?>
            <th><?= LangManager::translate('shop.views.items.manage.desc') ?></th>
            <th><?= LangManager::translate('shop.views.items.manage.cat') ?></th>
            <th><?= LangManager::translate('shop.views.items.manage.price') ?></th>
            <th><?= LangManager::translate('shop.views.items.manage.stock') ?></th>
            <th class="text-center"><?= LangManager::translate('shop.views.items.manage.cart') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <?php
            $itemType = $item->getType();
            $invalidType = (
                ($shopType === ShopType::VIRTUAL_ONLY && $itemType === ShopItemType::PHYSICAL) ||
                ($shopType === ShopType::PHYSICAL_ONLY && $itemType === ShopItemType::VIRTUAL)
            );
            ?>
            <tr style="<?= $invalidType ? 'opacity: 0.6;' : '' ?>">
            <td class="text-center" style="width: 6rem; height: 6rem;">
                    <?php
                    $getImagesItem = $imagesItem->getShopImagesByItem($item->getId());
                    $v = count($getImagesItem);
                    ?>
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
                    <h6 >
                        <?= mb_strimwidth($item->getName(), 0, 30, '...') ?>
                    </h6>
                    <span class="badge-info"><?= $item->getType()->label() ?></span>
                    <br>
                    <?php if ($item->isDraft()): ?>
                        <small class="cursor-pointer" data-tooltip-target="tooltip-draft-<?= $item->getId() ?>" data-tooltip-placement="top"><i
                                class="fa-solid fa-circle-info"></i> <?= LangManager::translate('shop.views.items.manage.draft') ?></small>
                        <div id="tooltip-draft-<?= $item->getId() ?>" role="tooltip" class="tooltip-content">
                            <?= LangManager::translate('shop.views.items.manage.draft-tooltip') ?>
                        </div>
                    <?php endif; ?>
                </td>
                <?php if ($allowReviews): ?>
                    <td class="text-center">
                        <a href="items/review/<?= $item->getId() ?>">
                            <?= $review->countTotalRatingByItemId($item->getId()) ?> <?= LangManager::translate('shop.views.items.manage.review') ?><br>
                            <?= $review->getStars($item->getId()) ?>
                        </a>
                    </td>
                <?php endif; ?>
                <td style="max-width: 5rem;">
                    <?= mb_strimwidth($item->getShortDescription(), 0, 60, '...') ?>
                </td>
                <td style="width: fit-content;">
                    <a class="link" data-bs-toggle="tooltip" title="Consulter cette catégorie" target="_blank"
                       href="<?= $item->getCategory()->getCatLink() ?>"><?= $item->getCategory()->getName() ?></a>
                </td>
                <td>
                    <b><?= $item->getPriceFormatted() ?></b>
                </td>
                <td>
                    <?= $item->getAdminFormattedStock() ?>
                </td>
                <td class="text-center">
                    <?= $item->getQuantityInCart() ?>
                </td>
                <td class="text-center space-x-2">
                    <?php if (!$invalidType): ?>
                    <a href="<?= $item->getItemLink() ?>" target="_blank">
                        <i data-bs-toggle="tooltip" title="Voir le rendue"
                           class="text-success fa-solid fa-up-right-from-square"></i>
                    </a>
                    <?php else: ?>
                        <i class="text-secondary fa-solid fa-up-right-from-square" data-tooltip-target="tooltip-noView-<?= $item->getId() ?>" data-tooltip-placement="top"></i>
                        <div id="tooltip-noView-<?= $item->getId() ?>" role="tooltip" class="tooltip-content">
                            Visualisation désactivée dans ce mode de boutique
                        </div>
                    <?php endif; ?>
                    <?php if (!$invalidType): ?>
                        <a href="items/edit/<?= $item->getId() ?>">
                            <i data-bs-toggle="tooltip" title="Éditer" class="text-info fa-solid fa-edit"></i>
                        </a>
                    <?php else: ?>
                        <i class="text-secondary fa-solid fa-edit" data-tooltip-target="tooltip-noEdit-<?= $item->getId() ?>" data-tooltip-placement="top"></i>
                        <div id="tooltip-noEdit-<?= $item->getId() ?>" role="tooltip" class="tooltip-content">
                            Édition désactivée dans ce mode de boutique
                        </div>
                    <?php endif; ?>
                    <?php if (!$invalidType): ?>
                    <button data-modal-toggle="modal-delete-<?= $item->getId() ?>" type="button">
                        <i data-bs-toggle="tooltip" title="Supprimé" class="text-danger fas fa-trash-alt"></i>
                    </button>
                    <?php else: ?>
                        <i class="text-secondary fas fa-trash-alt" data-tooltip-target="tooltip-noDelete-<?= $item->getId() ?>" data-tooltip-placement="top"></i>
                        <div id="tooltip-noDelete-<?= $item->getId() ?>" role="tooltip" class="tooltip-content">
                            Suppression désactivée dans ce mode de boutique
                        </div>
                    <?php endif; ?>
                </td>
            </tr>

            <?php if (!$invalidType): ?>
                <div id="modal-delete-<?= $item->getId() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header-danger">
                            <h6><?= LangManager::translate('shop.views.items.manage.remove', ['name' => $item->getName()]) ?></h6>
                            <button type="button" data-modal-hide="modal-delete-<?= $item->getId() ?>"><i
                                    class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <p><?= LangManager::translate('shop.views.items.manage.remove-warn') ?></p>
                        </div>
                        <div class="modal-footer">
                            <a href="items/delete/<?= $item->getId() ?>"
                               class="btn-danger"><?= LangManager::translate('core.btn.delete') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>