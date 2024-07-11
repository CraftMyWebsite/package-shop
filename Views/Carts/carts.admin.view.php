<?php


use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersModel;

$title = "Paniers";
$description = "";

/* @var \CMW\Model\Shop\Cart\ShopCartModel $cartModel */
/* @var \CMW\Model\Shop\Cart\ShopCartItemModel $cartItemsModel */

?>
<h3><i class="fa-solid fa-cart-shopping"></i> Paniers</h3>

<div>

</div>
<h6>Paniers des utilisateurs</h6>
<div class="grid-7">


                <?php foreach ($cartModel->getShopCartsForConnectedUsers() as $connectedCart):
                    $user = UsersModel::getInstance()->getUserById($connectedCart->getUser()->getId());
                    ?>

                        <div class="card">

                                <img style="width: 80px; height: 80px" class="mx-auto" src="<?= $user->getUserPicture()->getImage() ?>"
                                     alt="...">
                            <h6 class="text-center"><?= $user->getPseudo() ?></h6>
                            <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId($user->getId(), "") ?></b> articles</p>
                            <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/shop/carts/user/<?= $user->getId() ?>" class="btn-center btn-primary-sm text-center">Voir le panier</a>
                        </div>

                <?php endforeach; ?>

</div>
<hr>
<div class="flex justify-between mt-6">
    <h6>Paniers des sessions</h6>
    <button type="button" data-modal-toggle="modal-delete-all" class="btn-danger">Supprimer tout</button>
</div>
<!--
--MODAL SUPPRESSION SESSION--
-->
<div id="modal-delete-all"" class="modal-container">
    <div class="modal">
        <div class="modal-header-danger">
            <h6>Suppression de toutes les sessions ?</h6>
            <button type="button" data-modal-hide="modal-delete-all""><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            Cette suppression est définitive.
        </div>
        <div class="modal-footer">
            <a type="button" href="carts/session/delete/all/sessions"
               class="btn btn-danger"><?= LangManager::translate("core.btn.delete") ?>
            </a>
        </div>
    </div>
</div>
<div class="alert alert-info mt-2">Les sessions sont des paniers temporaire.<br>Elle permet à vos utilisateurs non connecté de créer un panier.<br>Une fois connecté le panier sera automatique transmis vers un panier utilisateur, évitez de supprimez des sessions qui on moins de 24 heures.</div>
<div class="grid-7 mt-3.5">

        <?php foreach ($cartModel->getShopCartsForSessions() as $sessionCart):
            $session = $sessionCart->getSession();
            ?>
                <div class="card">
                    <small class="text-center"><?= $session ?></small>
                    <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId(null, $session) ?></b> articles</p>
                    <small class="text-center mb-2"> Créer le <?= $sessionCart->getCartCreated() ?></small>
                    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/shop/carts/session/<?= $session ?>" class="btn-center text-center btn-primary-sm">Voir le panier</a>
                    <a data-modal-toggle="modal-<?= $session ?>" class="cursor-pointer btn-center text-center btn-danger-sm">Supprimer</a>
                </div>
            <!--
            --MODAL SUPPRESSION SESSION--
            -->
            <div id="modal-<?= $session ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6>Suppression de
                            : <?= $session ?></h6>
                        <button type="button" data-modal-hide="modal-<?= $session ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>Cette suppression est définitive.</p>
                    </div>
                    <div class="modal-footer">
                        <a type="button" href="carts/session/delete/<?= $session ?>"
                           class="btn btn-danger ml-1"><?= LangManager::translate("core.btn.delete") ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
</div>
