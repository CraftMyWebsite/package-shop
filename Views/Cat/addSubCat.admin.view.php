<?php

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity $category */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.cat.addSubCat.title', ['cat_name' => $category->getName()]);
$description = '';
?>
<div class="page-title">
    <h3><i class="fa-solid fa-book"></i> <?= LangManager::translate('shop.views.cat.addSubCat.title', ['cat_name' => $category->getName()]) ?></h3>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="center-flex">
    <div class="flex-content">
        <form class="card" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <label for="name"><?= LangManager::translate('shop.views.cat.addSubCat.name') ?><span style="color: red">*</span> :</label>
            <div class="input-group">
                <i class="fa-solid fa-heading"></i>
                <input type="text" id="name" name="name" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.placeholderName') ?>">
            </div>
            <label><?= LangManager::translate('shop.views.cat.addSubCat.icon') ?></label>
            <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.iconPlaceholder') ?>" data-value=""></div>
            <label for="description"><?= LangManager::translate('shop.views.cat.addSubCat.desc') ?></small></label>
            <div class="input-group">
                <i class="fa-solid fa-paragraph"></i>
                <input type="text" id="description" name="description" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.descPlaceholder') ?>">
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-center btn-primary">
                    <?= LangManager::translate('core.btn.add') ?>
                </button>
            </div>

        </form>
    </div>
</div>