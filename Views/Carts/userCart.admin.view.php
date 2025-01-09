<?php

/* @var \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $carts */
/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.carts.viewCart.title', ['session_name' => $user->getPseudo()]);
$description = '';

?>

<h3><?= LangManager::translate('shop.views.carts.viewCart.title', ['session_name' => $user->getPseudo()]) ?></h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

    <div class="table-container table-container-striped">
        <table id="table1">
            <thead>
            <tr>
                <th><?= LangManager::translate('shop.views.carts.viewCart.item') ?></th>
                <th><?= LangManager::translate('shop.views.carts.viewCart.quantity') ?></th>
                <th><?= LangManager::translate('shop.views.carts.viewCart.pu') ?></th>
                <th><?= LangManager::translate('shop.views.carts.viewCart.pt') ?></th>
                <th><?= LangManager::translate('shop.views.carts.viewCart.date') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($carts as $cart): ?>
                <tr>
                    <td>
                        <a class="link" target="_blank" href="/shop/cat/<?= $cart->getItem()->getCategory()->getSlug() ?>/item/<?= $cart->getItem()->getSlug() ?>"><?= $cart->getItem()->getName() ?></a>
                    </td>
                    <td>
                        <?= $cart->getQuantity() ?>
                    </td>
                    <td>
                        <?= $cart->getItem()->getPriceFormatted() ?>
                    </td>
                    <td>
                        <?= $cart->getItemTotalPriceFormatted() ?>
                    </td>
                    <td>
                        <?= $cart->getCreated() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>