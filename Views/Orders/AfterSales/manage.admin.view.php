<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity $afterSale */
/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesMessagesEntity [] $afterSaleMessages */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = 'Services après ventes';
$description = 'SAV';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-headset"></i> Services après-ventes</h3>
    <?php if ($afterSale->getStatus() !== 2): ?>
    <button data-modal-toggle="modal-close" type="button" class="btn-success">Clôturer</button>
    <?php else: ?>
        <a href=".." type="button" class="btn-primary">Retour au SAV</a>
    <?php endif; ?>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<!--MODAL CLOSE-->
<div id="modal-close" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Clôturer ce S.A.V</h6>
            <button type="button" data-modal-hide="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            Êtes vous sur ?
        </div>
        <div class="modal-footer">
            <a href="../close/<?= $afterSale->getId() ?>" type="button" class="btn-success">Clore</a>
        </div>
    </div>
</div>

<div class="grid-4">
    <div class="card">
        <div class="flex gap-2 items-center">
            <img class="avatar-rounded" src="<?= $afterSale->getAuthor()->getUserPicture()->getImage() ?>">
            <?= $afterSale->getAuthor()->getPseudo() ?>
        </div>
        <p><b>Status :</b> <?= $afterSale->getFormattedStatus() ?></p>
        <p><b>Raison :</b> <?= $afterSale->getFormattedReason() ?></p>
        <p><b>Commande :</b> <a class="link" href="../../orders/view/<?= $afterSale->getOrder()->getId() ?>">#<?= $afterSale->getOrder()->getOrderNumber() ?></a></p>
        <p><b>Date :</b> <?= $afterSale->getCreated() ?></p>
    </div>
    <div class="col-span-3">
        <div class="card">
            <?php foreach ($afterSaleMessages as $message): ?>
                <?php if ($afterSale->getAuthor()->getId() === $message->getAuthor()->getId()): ?>
                <div class="flex">
                    <div class="max-w-2xl flex gap-2">
                        <img class="avatar-rounded" src="<?= $message->getAuthor()->getUserPicture()->getImage() ?>">
                        <div class="alert">
                            <div class="flex justify-between">
                                <p><span class="font-bold"><?= $message->getAuthor()->getPseudo() ?></span> <small><?= $message->getCreated() ?></small></p>
                                <span class="badge ml-12">Client</span>
                            </div>
                            <p><?= $message->getMessage() ?></p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="flex justify-end">
                        <div class="max-w-2xl flex gap-2">
                            <div class="alert">
                                <div class="flex justify-between">
                                    <span class="badge">S.A.V</span>
                                    <p><span class="font-bold"><?= $message->getAuthor()->getPseudo() ?></span> <small><?= $message->getCreated() ?></small></p>
                                </div>
                                <p><?= $message->getMessage() ?></p>
                            </div>
                            <img class="avatar-rounded" src="<?= $message->getAuthor()->getUserPicture()->getImage() ?>">
                        </div>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>
        <?php if ($afterSale->getStatus() !== 2): ?>
        <div class="card mt-6">
            <form method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <label for="message">Votre réponse :</label>
                <textarea id="message" name="message" minlength="3" required class="textarea"></textarea>
                <button type="submit" class="btn-center btn-success mt-4">Envoyer</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>