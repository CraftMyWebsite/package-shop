<?php

/* @var \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $carts */
/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\MailModel;

$title = 'Paniers de ' . $user->getPseudo();
$description = '';

?>

<h3>Panier de <?= $user->getPseudo() ?></h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

    <div class="table-container table-container-striped">
        <table id="table1">
            <thead>
            <tr>
                <th>Article</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
                <th>Date d'ajout</th>
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