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
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?> ANNULÉ</h3>
    <div>
        <a href="../" type="button" class="btn btn-warning">Plus tard ...</a>
        <button data-modal-toggle="modal-avoir" class="btn-warning" type="button">Créer un avoir</button>
        <button data-modal-toggle="modal-refunded" class="btn-success" type="button">Remboursée</button>
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

<div id="modal-avoir" class="modal-container">
    <div class="modal">
        <div class="modal-header-warning">
            <h6>Créer un avoir</h6>
            <button type="button" data-modal-hide="modal-avoir"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="avoir" action="refunded/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="alert-info">
                    Créer un avoir (code promo) applicable sur toute la boutique du montant total de la commande incluant faire de livraison et frais de paiement si applicable.
                </div>
                <label for="name">Nom<span style="color: red">*</span> :</label>
                <input type="text" id="name" name="name" class="input" placeholder="Credit : Avoir">
                <small>A titre indicatif pour votre historique</small>
            </div>
            <div class="modal-footer">
                <button form="avoir" type="submit" class="btn btn-warning-sm">Créer un avoir</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-refunded" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6>Commande remboursée</h6>
            <button type="button" data-modal-hide="modal-refunded"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="refunded" action="endFailed/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="modal-body">
            Avez-vous remboursé votre client ? cette commande est donc maintenant terminé ?
        </div>
        <div class="modal-footer">
            <button form="refunded" type="submit" class="btn btn-success">Commande remboursée</button>
        </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h6>Remboursement</h6>
        <p>Votre client a déjà payé l'intégralité de
            <?= "<b style='color: #6f6fad'>" . $order->getOrderTotalFormatted() . '</b>' ?>
            avec <?= $order->getPaymentMethod()->getName() ?>.</p>
        <p>Il ne vous reste plus qu'à le rembourser pour finaliser le traitement de cette commande.</p>
    </div>
    <div class="card">
            <h6>Récap de commande</h6>
            <?php foreach ($order->getOrderedItems() as $orderItem): ?>
                <div class="flex items-start mb-2">
                    <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                    <?php else: ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                    <?php endif; ?>
                    <p><?= $orderItem->getName() ?> - <?= $orderItem->getPriceFormatted() ?> <br>
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
