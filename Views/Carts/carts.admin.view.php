<?php


use CMW\Manager\Env\EnvManager;
use CMW\Model\Users\UsersModel;

$title = "Paniers";
$description = "";


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
                    <?php foreach (CMW\Model\Shop\ShopCartsModel::getInstance()->countItemsInCartByUser() as $cart):
                        $user = UsersModel::getInstance()->getUserById($cart['shop_user_id'])?>
                        <div class="col-12 col-lg-2">
                            <div href="ss" class="card-in-card p-2">
                                <div class="text-center">
                                    <img style="width: 80px; height: 80px" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Public/Uploads/Users/<?= $user->getUserPicture()->getImageName() ?>"
                                         alt="...">
                                </div>
                                <h6 class="text-center"><?= $user->getPseudo() ?></h6>
                                <p class="text-center"><b style="font-size: large"><?= $cart['shop_item_in_cart'] ?></b> articles</p>
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/shop/carts/<?= $user->getId() ?>" class="btn btn-primary">Voir le panier</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
