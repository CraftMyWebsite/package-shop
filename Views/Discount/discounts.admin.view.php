<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

$title = LangManager::translate('shop.views.discount.discount.title');
$description = '';

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $upcomingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');

?>
<div class="page-title">
    <h3><i class="fa-solid fa-tag"></i> <?= LangManager::translate('shop.views.discount.discount.title') ?></h3>
    <a href="discounts/add" type="button" class="btn-primary"><?= LangManager::translate('shop.views.discount.discount.new') ?></a>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="card">
        <h6><?= LangManager::translate('shop.views.discount.discount.inProgress') ?></h6>
    <div class="table-container">
        <table class="table" id="table1">
            <thead>
            <tr>
                <th><?= LangManager::translate('shop.views.discount.discount.name') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.code') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.linked') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.impact') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.start') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.end') ?></th>
                <th><?= LangManager::translate('shop.views.discount.discount.uses') ?></th>
                <th class="text-center"><?= LangManager::translate('shop.views.discount.discount.manage') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ongoingDiscounts as $discount): ?>
                <tr>
                    <td><?= $discount->getName() ?></td>
                    <td><?= !empty($discount->getCode()) ? $discount->getCode() : LangManager::translate('shop.views.discount.discount.autoApply') ?></td>
                    <td><?= $discount->getLinkedFormatted() ?></td>
                    <td>
                        <?php if ($discount->getPrice()): ?>
                            <?= $discount->getPrice() ?> <?= $symbol ?>
                        <?php else: ?>
                            <?= $discount->getPercentage() ?> %
                        <?php endif; ?>
                    </td>
                    <td><?= $discount->getStartDateFormatted() ?></td>
                    <td><?= $discount->getDuration() ?></td>
                    <td><b><?= $discount->getCurrentUses() ?? '0' ?>/<?= $discount->getMaxUses() ?? '∞' ?></b></td>
                    <td class="space-x-2 text-center">
                        <a href="discounts/edit/<?= $discount->getId() ?>" title="Modifier">
                            <i class="text-info fa-solid fa-pen-to-square"></i>
                        </a>
                        <button data-modal-toggle="modal-report-<?= $discount->getId() ?>" title="Reporter">
                            <i class="text-warning fa-solid fa-forward"></i>
                        </button>
                        <button data-modal-toggle="modal-disable-<?= $discount->getId() ?>" title="Désactiver">
                            <i class="text-warning fa-solid fa-ban"></i>
                        </button>
                        <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                            <i class="text-danger fa-solid fa-trash"></i>
                        </button>
                        <!--Report-->
                        <div id="modal-report-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-warning">
                                    <h6><?= LangManager::translate('shop.views.discount.discount.report', ['name' => $discount->getName()]) ?></h6>
                                    <button type="button" data-modal-hide="modal-report-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <form action="discounts/report" method="post">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="modal-body">
                                    <div>
                                        <input hidden="" name="id" value="<?= $discount->getId() ?>">
                                        <label for="startDate"><?= LangManager::translate('shop.views.discount.discount.startDate') ?></label>
                                        <div class="input-group">
                                            <i class="fa-regular fa-clock"></i>
                                            <input type="datetime-local" id="startDate" step="1" name="startDate" value="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn-warning"><?= LangManager::translate('shop.views.discount.discount.reportBtn') ?></button>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!--Désactivation-->
                        <div id="modal-disable-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-warning">
                                    <h6><?= LangManager::translate('shop.views.discount.discount.disable', ['name' => $discount->getName()]) ?></h6>
                                    <button type="button" data-modal-hide="modal-disable-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate('shop.views.discount.discount.disableText') ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/stop/<?= $discount->getId() ?>" type="button" class="btn-warning"><?= LangManager::translate('shop.views.discount.discount.disableBtn') ?></a>
                                </div>
                            </div>
                        </div>
                        <!--SUPPRESSION-->
                        <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6><?= LangManager::translate('shop.views.discount.discount.delete', ['name' => $discount->getName()]) ?></h6>
                                    <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate('shop.views.discount.discount.deleteText') ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger"><?= LangManager::translate('shop.views.discount.discount.deleteBtn') ?></a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2 mt-6">
    <div class="card">
            <h6><?= LangManager::translate('shop.views.discount.discount.inComing') ?></h6>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.discount.discount.name') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.code') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.impact') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.linked') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.startIn') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($upcomingDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= !empty($discount->getCode()) ? $discount->getCode() : LangManager::translate('shop.views.discount.discount.autoApply') ?></td>
                        <td>
                            <?php if ($discount->getPrice()): ?>
                                <?= $discount->getPrice() ?> <?= $symbol ?>
                            <?php else: ?>
                                <?= $discount->getPercentage() ?> %
                            <?php endif; ?>
                        </td>
                        <td><?= $discount->getLinkedFormatted() ?></td>
                        <td><?= $discount->getDuration() ?></td>
                        <td class="space-x-2">
                            <a title="Activer maintenant" href="discounts/start/<?= $discount->getId() ?>">
                                <i class="text-success fa-solid fa-rocket"></i>
                            </a>
                            <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </button>
                            <!--SUPPRESSION-->
                            <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header-danger">
                                        <h6><?= LangManager::translate('shop.views.discount.discount.delete', ['name' => $discount->getName()]) ?></h6>
                                        <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <?= LangManager::translate('shop.views.discount.discount.deleteText') ?>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger"><?= LangManager::translate('shop.views.discount.discount.deleteBtn') ?></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h6><?= LangManager::translate('shop.views.discount.discount.passed') ?></h6>
        <div class="table-container">
            <table class="table" id="table3">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.discount.discount.name') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.code') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.impact') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.linked') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.discount.uses') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pastDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= !empty($discount->getCode()) ? $discount->getCode() : LangManager::translate('shop.views.discount.discount.autoApply') ?></td>
                        <td>
                            <?php if ($discount->getPrice()): ?>
                                <?= $discount->getPrice() ?> <?= $symbol ?>
                            <?php else: ?>
                                <?= $discount->getPercentage() ?> %
                            <?php endif; ?>
                        </td>
                        <td><?= $discount->getLinkedFormatted() ?></td>
                        <td><b><?= $discount->getCurrentUses() ?? '0' ?>/<?= $discount->getMaxUses() ?? '∞' ?></b></td>
                        <td>
                            <a title="Supprimé" href="discounts/delete/<?= $discount->getId() ?>">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>