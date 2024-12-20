<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <a href="../" type="button" class="btn btn-warning">Pas pour l'instant</a>
        <button data-modal-toggle="modal-finish-him" class="btn-success" type="button">Terminé</button>
    </div>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>


<div id="modal-finish-him" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6>Commande terminé</h6>
            <button type="button" data-modal-hide="modal-finish-him"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="end/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                Parfait ! bonne vente à vous.
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Commande terminé</button>
            </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div>
        <div class="card">
            <?php if ($order->getShippingMethod()->getShipping()->getType() === 0): ?>
                <h6>Commande reçu ?</h6>
                <p>Si votre client a bien reçu sa commande, il est conseiller de la clôturer pour un meilleur suivi.</p>
                <?php if (!empty($order->getShippingLink())): ?>
                    <p>Vous pouvez suivre l'avancée de la livraison ici : <a href="<?= $order->getShippingLink() ?>" target="_blank" class="link">Suivre le colis</a></p>
                <?php endif; ?>
            <?php else: ?>
                <h6>Commande récupérée ?</h6>
                <p>Votre client est venu chercher sont colis dans votre centre ? Il est conseiller de la clôturer pour un meilleur suivi.</p>
            <?php endif; ?>
        </div>
        <div class="card mt-4">
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