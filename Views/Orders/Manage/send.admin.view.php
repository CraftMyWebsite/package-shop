<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getOrderNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getOrderNumber() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Expédition</h4>
            </div>
            <div class="card-body">
                <h5>Type d'expédition :</h5>
                <p><?= $order->getShippingMethod()->getName() ?> - <b><?= $order->getShippingMethod()->getPriceFormatted() ?></b></p>
                <h5>Livrer à :</h5>
                <p>
                    <?= $order->getUserAddressMethod()->getUserFirstName() ?>
                    <?= $order->getUserAddressMethod()->getUserLastName() ?><br>
                    <?= $order->getUserAddressMethod()->getUserLine1() ?><br>
                    <?php if (!empty($order->getUserAddressMethod()->getUserLine2())) {echo $order->getUserAddressMethod()->getUserLine2()."<br>";} ?>
                    <?= $order->getUserAddressMethod()->getUserPostalCode() ?>
                    <?= $order->getUserAddressMethod()->getUserCity() ?><br>
                    <?= $order->getUserAddressMethod()->getUserCountry() ?><br>
                </p>
                <h5>Quelques informations sur le déstinataire :</h5>
                <p>
                    Téléphone : <b><?= $order->getUserAddressMethod()->getUserPhone() ?></b><br>
                    @mail : <b><?= $order->getUserAddressMethod()->getUserMail() ?></b>
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
                <form id="finish" action="finish/<?= $order->getId() ?>" method="post">
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
                <?php foreach ($order->getOrderedItems() as $orderItem):?>
                <div style="align-items: baseline" class="d-flex justify-between mb-2">
                    <?php if ($orderItem->getFirstImg() !== "/Public/Uploads/Shop/0"): ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                    <?php else: ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                    <?php endif; ?>
                    <p><?= $orderItem->getName() ?> |
                         Quantité : <b><?= $orderItem->getQuantity() ?></b> |
                        <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                            <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
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