<?php

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity $category */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = 'Catégorie';
$description = 'Ajouter une sous-catégorie';
?>
<div class="page-title">
    <h3><i class="fa-solid fa-book"></i> Ajout d'une sous catégorie dans <?= $category->getName() ?></h3>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

<div class="center-flex">
    <div class="flex-content">
        <form class="card" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <label for="name">Nom<span style="color: red">*</span> :</label>
            <div class="input-group">
                <i class="fa-solid fa-heading"></i>
                <input type="text" id="name" name="name" placeholder="Pantalon">
            </div>
            <label>Icon : <small>(Optionnel)</small></label>
            <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="Sélectionner un icon" data-value=""></div>
            <label for="description">Description : <small>(Optionnel)</small></label>
            <div class="input-group">
                <i class="fa-solid fa-paragraph"></i>
                <input type="text" id="description" name="description" placeholder="Des vêtements">
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-center btn-primary">
                    <?= LangManager::translate('core.btn.add') ?>
                </button>
            </div>

        </form>
    </div>
</div>