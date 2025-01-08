<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder */
/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity $afterSales */
/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesMessagesEntity[] $afterSalesMessages */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

Website::setTitle("Boutique - Serve après ventes");
Website::setDescription('Déclarer un incident sur l\'article ' . $historyOrder->getOrderNumber());

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center; margin-bottom: 1rem">Service après-vente</h3>
        <div class="shop-cart-card-45854">
            <div class="shop-flex-45127" style="justify-content: space-between">
                <h4>Incident sur la commande N°<?=$historyOrder->getOrderNumber()?></h4>
                <?php if ($afterSales->getStatus() !== 2): ?>
                    <a href="<?= $historyOrder->getOrderNumber() ?>/close"
                       class="shop-button-48751">Clôturer</a>
                <?php endif; ?>
            </div>

            <div style="margin-top: 1rem">
                <div class="shop-flex-45127" style="justify-content: space-between">
                    <p><?= $afterSales->getFormattedStatus() ?></p>
                    <p style="font-weight: bolder"><?= $afterSales->getFormattedReason() ?></p>
                    <p><?= $afterSales->getCreated() ?></p>
                </div>
            </div>
            <div class="shop-cart-card-45854" style="margin-top: 1.5rem">
                <?php foreach ($afterSalesMessages as $message): ?>
                    <?php if ($afterSales->getAuthor()->getId() === $message->getAuthor()->getId()): ?>
                        <div style="display: flex">
                            <div style="max-width: 42rem; display: flex; gap: 1rem">
                                <img alt="user picture" style="width: 3rem; height: fit-content; border-radius: 100%" src="<?= $message->getAuthor()->getUserPicture()->getImage() ?>"/>
                                <div style="padding: .5rem" class="shop-item-ordered-456744">
                                    <div style="display: flex; justify-content: space-between; gap: 1rem">
                                        <p><span style="font-weight: bolder"><?= $message->getAuthor()->getPseudo() ?></span> <small><?= $message->getCreated() ?></small></p>
                                        <small style="border-radius: .3rem; background: #7ea8ec; color: white; padding: .1rem .3rem">Vous</small>
                                    </div>
                                    <p><?= $message->getMessage() ?></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; justify-content: end">
                            <div style="max-width: 42rem; display: flex; gap: 1rem">
                                <div style="padding: .5rem" class="shop-item-ordered-456744">
                                    <div style="display: flex; justify-content: space-between; gap: 1rem">
                                        <small style="border-radius: .3rem; background: #5bac4c; color: white; padding: .1rem .3rem">S.A.V</small>
                                        <p><span style="font-weight: bolder"><?= $message->getAuthor()->getPseudo() ?></span> <small><?= $message->getCreated() ?></small></p>
                                    </div>
                                    <p><?= $message->getMessage() ?></p>
                                </div>
                                <img alt="user picture" style="width: 3rem; height: fit-content; border-radius: 100%" src="<?= $message->getAuthor()->getUserPicture()->getImage() ?>"/>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php if ($afterSales->getStatus() !== 2): ?>
                <form method="post" action="">
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                    <div style="margin-top: 1rem">
                        <label for="content" class="shop-label-478541">Réponse<span class="text-red-500">*</span> :</label>
                        <textarea name="content" id="content" required minlength="20" class="shop-input-45753"></textarea>
                    </div>
                    <div style="display: flex; justify-content: center">
                        <button type="submit" class="shop-button-48751">Soumettre <i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
</section>