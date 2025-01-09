<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <a type="button" href="../" class="btn btn-warning">Plus tard ...</a>
        <button form="finish" type="submit" class="btn btn-primary">Colis en route</button>
    </div>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="grid-2">
    <div class="card">
        <h5>Expédition</h5>
        <hr>
        <h6>Type d'expédition (<?= $order->getShippingMethod()->getShipping()->getFormattedType() ?>) :</h6>
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
            <?= $order->getUserAddressMethod()->getUserFormattedCountry() ?><br>
        </p>
        <hr>
        <h6>Informations supplémentaires :</h6>
        <p>
            Téléphone : <b><?= $order->getUserAddressMethod()->getUserPhone() ?></b><br>
            @mail : <b><?= $order->getUserAddressMethod()->getUserMail() ?></b>
        </p>
    </div>

    <div>
        <div class="card">
            <h6>Suivie</h6>
            <form id="finish" action="finish/<?= $order->getId() ?>" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
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
                            <?php if ($reviewEnabled): ?>
                            <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?><br>
                    <?php endif; ?>
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
