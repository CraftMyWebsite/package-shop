<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>

<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <button data-modal-toggle="modal-danger" type="button" class="btn btn-danger">Non réalisable</button>
        <button data-modal-toggle="modal-success" type="button" class="btn btn-primary">Commande prête</button>
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

<div id="modal-danger" class="modal-container">
    <div class="modal">
        <div class="modal-header-danger">
            <h6>Non réalisable</h6>
            <button type="button" data-modal-hide="modal-danger"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                Vous devrez rembourser votre client !
            </p>
        </div>
        <div class="modal-footer">
            <button form="cancel" type="submit" class="btn btn-danger">Cette commande n'est pas réalisable</button>
        </div>
    </div>
</div>

<!--MODAL SUCCESS-->
<div id="modal-success" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6>Tout est dans la boite ?</h6>
            <button type="button" data-modal-hide="modal-success"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <?php if ($order->getShippingMethod()?->getShipping()->getType() === 0): ?>
                    Tout est prêt à partir ?
                <?php elseif($order->getShippingMethod()?->getShipping()->getType() === 1): ?>
                    La validation permettra au client de venir récupérer son colis, tout est bon ?
                <?php else: ?>
                    Tout vos articles virtuels sont prêts ?
                <?php endif; ?>
            </p>
        </div>
        <div class="modal-footer">
            <button data-modal-hide="modal-success" type="button" class="btn-danger">Fermer</button>
            <button form="send" type="submit" class="btn btn-success">Tout est prêt !</button>
        </div>
    </div>
</div>


<div class="alert-info">
    <?php if ($order->getShippingMethod()?->getShipping()->getType() === 0): ?>
        <p>Cette commande devra être expédiée</p>
    <?php elseif ($order->getShippingMethod()?->getShipping()->getType() === 1): ?>
        <p>Cette commande sera récupérer par le client au dépot :</p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressLine() ?></p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressPostalCode() ?> <?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressCity() ?></p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getFormattedCountry() ?></p>
    <?php else: ?>
        <p>Cette commande ne contient que des articles virtuels</p>
    <?php endif; ?>
</div>
<h6>Articles à préparer</h6>

<div class="grid-4">
    <?php foreach ($order->getOrderedItems() as $orderItem): ?>
        <div class="card">
            <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto" src="<?= $orderItem->getFirstImg() ?>" alt="Panier">
            <?php else: ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto"  src="<?= $defaultImage ?>" alt="..."/>
            <?php endif; ?>
            <h4 class="text-center"><?= $orderItem->getName() ?></h4>
            <?php if ($reviewEnabled): ?>
                <div class="flex justify-center">
                    <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?>
                </div>
            <?php endif; ?>
            <?php if ($orderItem->getItem()->getType() == 1):
                $virtualMethod = ShopItemsVirtualMethodModel::getInstance()?->getVirtualItemMethodByItemId($orderItem->getItem()->getId())->getVirtualMethod()->name(); ?>
                <p>Article virtuel, Méthode : <?= $virtualMethod ?></p>
            <?php endif; ?>
            <?php if ($orderItem->getItem()->getType() == 0): ?>
                <p>Article Physique</p>
            <?php endif; ?>
            <p style="font-size: 1.2rem">
                - Quantité : <b><?= $orderItem->getQuantity() ?></b><br>
                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                    - <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b><br>
                <?php endforeach; ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<form id="cancel" action="cancel/<?= $order->getId() ?>" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
</form>
<form id="send" action="send/<?= $order->getId() ?>" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
</form>