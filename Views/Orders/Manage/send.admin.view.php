<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <a type="button" href="../" class="btn btn-warning">Plus tard ...</a>
        <button form="finish" type="submit" class="btn btn-primary">Le colis est en route !</button>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h5>Expédition</h5>
        <hr>
        <h6>Type d'expédition :</h6>
        <p><?= $order->getShippingMethod()->getName() ?> - <b><?= $order->getShippingMethod()->getPriceFormatted() ?></b></p>
        <hr>
        <h6>Livrer à :</h6>
        <p>
            <?= $order->getUserAddressMethod()->getUserFirstName() ?>
            <?= $order->getUserAddressMethod()->getUserLastName() ?><br>
            <?= $order->getUserAddressMethod()->getUserLine1() ?><br>
            <?php if (!empty($order->getUserAddressMethod()->getUserLine2())) { echo $order->getUserAddressMethod()->getUserLine2() . '<br>'; } ?>
            <?= $order->getUserAddressMethod()->getUserPostalCode() ?>
            <?= $order->getUserAddressMethod()->getUserCity() ?><br>
            <?= $order->getUserAddressMethod()->getUserCountry() ?><br>
        </p>
        <hr>
        <h6>Informations supplémentaire :</h6>
        <p>
            Téléphone : <b><?= $order->getUserAddressMethod()->getUserPhone() ?></b><br>
            @mail : <b><?= $order->getUserAddressMethod()->getUserMail() ?></b>
        </p>
    </div>

    <div>
        <div class="card">
            <h6>Suivie</h6>
            <form id="finish" action="finish/<?= $order->getId() ?>" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <h6>Lien de suivie colis :</h6>
                <input type="text" class="input" name="shipping_link">
                <small>Si vous n'êtes pas en mesure de fournir de lien de suivie merci de ne pas remplir ce champ.</small>
            </form>
        </div>
        <div class="card mt-6">
            <h6>Récap de commande</h6>
            <div>
                <?php foreach ($order->getOrderedItems() as $orderItem): ?>
                    <div class="flex items-start mb-2">
                        <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                        <?php else: ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                        <?php endif; ?>
                        <p><?= $orderItem->getName() ?> <br>
                            Quantité : <b><?= $orderItem->getQuantity() ?></b> <br>
                            <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                                <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
