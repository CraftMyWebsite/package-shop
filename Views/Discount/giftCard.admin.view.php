<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = '';
$description = '';

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $upcomingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gift"></i> Carte cadeau</h3>
    <button data-bs-toggle="modal" data-modal-toggle="modal-generate" class="btn-primary">Générer une carte</button>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

<div id="modal-generate" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Générer une carte cadeau</h6>
            <button type="button" data-modal-hide="modal-generate"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="giftCard/generate">
            <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
        <div class="modal-body">
            <label for="amount">Montant :</label>
            <input placeholder="18.99" type="text" name="amount" id="amount" class="input" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-primary">
                <span class="">Générer</span>
            </button>
        </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h4>Carte active</h4>
        </div>
        <div class="table-container">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>CODE</th>
                    <th>Terminé dans</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ongoingDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= $discount->getCode() ?></td>
                        <td><?= $discount->getDuration() ?></td>
                        <td class="text-center">
                            <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </button>
                        </td>
                        <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6>Suppression de <?= $discount->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    Cette suppression est definitive.
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Carte passée ou utilisé</h4>
        </div>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th class="text-center">Nom</th>
                    <th class="text-center">CODE</th>
                    <th class="text-center">Nombre d'utilisation</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($pastDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= $discount->getCode() ?></td>
                        <td><?= $discount->getCurrentUses() ?? '∞' ?>/<?= $discount->getMaxUses() ?? '∞' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let inputElement = document.querySelector('input[name="amount"]');

    inputElement.addEventListener('input', function() {
        let inputValue = this.value;
        inputValue = inputValue.replace(/,/g, '.');
        inputValue = inputValue.replace(/[^\d.]/g, '');
        if (/\.\d{3,}/.test(inputValue)) {
            let decimalIndex = inputValue.indexOf('.');
            inputValue = inputValue.substring(0, decimalIndex + 3);
        }
        this.value = inputValue;
    });
</script>