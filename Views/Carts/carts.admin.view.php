<?php


use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersModel;

$title = "Paniers";
$description = "";

/* @var \CMW\Model\Shop\Cart\ShopCartModel $cartModel */
/* @var \CMW\Model\Shop\Cart\ShopCartItemModel $cartItemsModel */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cart-shopping"></i> <span class="m-lg-auto">Paniers</span></h3>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Paniers des utilisateurs</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($cartModel->getShopCartsForConnectedUsers() as $connectedCart):
                        $user = UsersModel::getInstance()->getUserById($connectedCart->getUser()->getId());
                        ?>
                        <div class="col-12 col-lg-2">
                            <div href="ss" class="card-in-card p-2">
                                <div class="text-center">
                                    <img style="width: 80px; height: 80px" src="<?= $user->getUserPicture()->getImage() ?>"
                                         alt="...">
                                </div>
                                <h6 class="text-center"><?= $user->getPseudo() ?></h6>
                                <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId($user->getId(), "") ?></b> articles</p>
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/shop/carts/user/<?= $user->getId() ?>" class="btn btn-primary">Voir le panier</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Paniers des sessions</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">Les sessions sont des paniers temporaire.<br>Elle permet à vos utilisateurs non connecté de créer un panier.<br>Une fois connecté le panier sera automatique transmis vers un panier utilisateur, évitez de supprimez des sessions qui on moins de 24 heures.</div>
                <div class="row">
                    <?php foreach ($cartModel->getShopCartsForSessions() as $sessionCart):
                        $session = $sessionCart->getSession();
                        ?>
                        <div class="col-12 col-lg-2">
                            <div href="ss" class="card-in-card p-2">
                                <small class="text-center"><?= $session ?></small>
                                <p class="text-center"><b style="font-size: large"><?= $cartItemsModel->countItemsByUserId(null, $session) ?></b> articles</p>
                                <small class="text-center mb-2"> Créer le <?= $sessionCart->getCartCreated() ?></small>
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/shop/carts/session/<?= $session ?>" class="btn btn-primary mb-2">Voir le panier</a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#delete-<?= $session ?>" class="btn btn-danger">Supprimer</a>
                            </div>
                        </div>
                        <!--
                        --MODAL SUPPRESSION SESSION--
                        -->
                        <div class="modal fade text-left" id="delete-<?= $session ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                 role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Suppression de
                                            : <?= $session ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Cette suppression est définitive.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">
                                            <i class="bx bx-x"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="carts/session/delete/<?= $session ?>"
                                           class="btn btn-danger ml-1">
                                            <i class="bx bx-check"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
