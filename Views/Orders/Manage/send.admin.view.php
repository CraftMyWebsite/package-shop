<?php

/* @var \CMW\Entity\Shop\Orders\ShopOrdersEntity $order */
/* @var \CMW\Entity\Shop\Orders\ShopOrdersItemsEntity [] $orderItems */
/* @var CMW\Model\Shop\Order\ShopOrdersItemsVariantesModel $itemsVariantes */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getNumber() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Expédition</h4>
            </div>
            <div class="card-body">
                <h5>Type d'expédition :</h5>
                <p><?= $order->getShippingMethod()->getName() ?> - <b><?= $order->getShippingMethod()->getPrice() ?>€</b></p>
                <h5>Livrer à :</h5>
                <p>
                    <?= $order->getDeliveryAddress()->getFirstName() ?>
                    <?= $order->getDeliveryAddress()->getLastName() ?><br>
                    <?= $order->getDeliveryAddress()->getLine1() ?><br>
                    <?php if (!empty($order->getDeliveryAddress()->getLine2())) {echo $order->getDeliveryAddress()->getLine2()."<br>";} ?>
                    <?= $order->getDeliveryAddress()->getPostalCode() ?>
                    <?= $order->getDeliveryAddress()->getCity() ?><br>
                    <?= $order->getDeliveryAddress()->getCountry() ?><br>
                </p>
                <h5>Quelque information sur le déstinataire :</h5>
                <p>
                    Téléphone : <b><?= $order->getDeliveryAddress()->getPhone() ?></b><br>
                    @mail : <b><?= $order->getUser()->getMail() ?></b>
                </p>

            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Suivie</h4>
            </div>
            <div class="card-body">
                <form id="finish" action="finish/<?= $order->getOrderId() ?>" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                        <h6>Lien de suivie colis :</h6>
                        <input type="text" class="form-control" name="shipping_link">
                        <small>Si vous n'êtes pas en mesure de fournir de lien de suivie merci de ne pas remplir ce champ.</small>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Récap de commande</h4>
            </div>
            <div class="card-body row">
                <?php foreach ($orderItems as $orderItem):?>
                <div style="align-items: baseline" class="d-flex justify-between mb-2">
                    <?php if ($orderItem->getFirstImageItemUrl() !== "/Public/Uploads/Shop/0"): ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImageItemUrl() ?>" alt="Panier"></div>
                    <?php else: ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                    <?php endif; ?>
                    <p><?= $orderItem->getItem()->getName() ?> |
                         Quantité : <b><?= $orderItem->getOrderItemQuantity() ?></b> |
                        <?php foreach ($itemsVariantes->getShopItemVariantValueByOrderItemId($orderItem->getOrderItemId()) as $itemVariant): ?>
                            <?= $itemVariant->getVariantValue()->getVariant()->getName() ?> : <b><?= $itemVariant->getVariantValue()->getValue() ?></b>
                        <?php endforeach; ?>
                    </p>
                </div>

                <?php endforeach;?>
            </div>
        </div>
    </div>
</section>

<div class="text-center d-flex justify-content-between">
        <a href="../" class="btn btn-warning">Plus tard ...</a>
    <button form="finish" type="submit" class="btn btn-primary">Le colis est en route !</button>
</div>