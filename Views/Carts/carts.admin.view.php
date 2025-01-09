<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Users\UsersModel;

$title = 'Paniers';
$description = '';

/* @var \CMW\Model\Shop\Cart\ShopCartModel $cartModel */
/* @var \CMW\Model\Shop\Cart\ShopCartItemModel $cartItemsModel */

?>
<h3><i class="fa-solid fa-cart-shopping"></i> <?= LangManager::translate('shop.views.carts.carts.title') ?></h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<h6><?= LangManager::translate('shop.views.carts.carts.title2') ?></h6>
<div class="grid-7">


                <?php
                    foreach ($cartModel->getShopCartsForConnectedUsers() as $connectedCart):
                        $user = UsersModel::getInstance()->getUserById($connectedCart->getUser()->getId());
                        ?>

                        <div class="card">

                                <img style="width: 80px; height: 80px" class="mx-auto" src="<?= $user->getUserPicture()->getImage() ?>"
                                     alt="...">
                            <h6 class="text-center"><?= $user->getPseudo() ?></h6>
                            <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId($user->getId(), '') ?></b> <?= LangManager::translate('shop.views.carts.carts.items') ?></p>
                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/shop/carts/user/<?= $user->getId() ?>" class="btn-center btn-primary-sm text-center"><?= LangManager::translate('shop.views.carts.carts.view') ?></a>
                        </div>

                <?php endforeach; ?>

</div>
<hr>
<div class="flex justify-between mt-6">
    <h6><?= LangManager::translate('shop.views.carts.carts.cartSessions') ?></h6>
    <button type="button" data-modal-toggle="modal-delete-all" class="btn-danger"><?= LangManager::translate('shop.views.carts.carts.deleteAll') ?></button>
</div>
<!--
--MODAL SUPPRESSION SESSION--
-->
<div id="modal-delete-all"" class="modal-container">
    <div class="modal">
        <div class="modal-header-danger">
            <h6><?= LangManager::translate('shop.views.carts.carts.modal.titleAllSession') ?></h6>
            <button type="button" data-modal-hide="modal-delete-all""><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <?= LangManager::translate('shop.views.carts.carts.modal.textSession') ?>
        </div>
        <div class="modal-footer">
            <a type="button" href="carts/session/delete/all/sessions"
               class="btn btn-danger"><?= LangManager::translate('core.btn.delete') ?>
            </a>
        </div>
    </div>
</div>
<div class="alert alert-info mt-2"><?= LangManager::translate('shop.views.carts.carts.warning') ?></div>
<div class="grid-7 mt-3.5">

        <?php
            foreach ($cartModel->getShopCartsForSessions() as $sessionCart):
                $session = $sessionCart->getSession();
                ?>
                <div class="card">
                    <small class="text-center"><?= $session ?></small>
                    <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId(null, $session) ?></b> <?= LangManager::translate('shop.views.carts.carts.items') ?></p>
                    <small class="text-center mb-2"> Créer le <?= $sessionCart->getCartCreated() ?></small>
                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/shop/carts/session/<?= $session ?>" class="btn-center text-center btn-primary-sm"><?= LangManager::translate('shop.views.carts.carts.view') ?></a>
                    <a data-modal-toggle="modal-<?= $session ?>" class="cursor-pointer btn-center text-center btn-danger-sm"><?= LangManager::translate('shop.views.carts.carts.delete') ?></a>
                </div>
            <!--
            --MODAL SUPPRESSION SESSION--
            -->
            <div id="modal-<?= $session ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate('shop.views.carts.carts.modal.titleSession', ['session_name' => $session]) ?></h6>
                        <button type="button" data-modal-hide="modal-<?= $session ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p><?= LangManager::translate('shop.views.carts.carts.modal.textSession') ?></p>
                    </div>
                    <div class="modal-footer">
                        <a type="button" href="carts/session/delete/<?= $session ?>"
                           class="btn btn-danger ml-1"><?= LangManager::translate('core.btn.delete') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
</div>
