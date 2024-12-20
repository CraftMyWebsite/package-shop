<?php
/* @var string $varName */

use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

?>
<style>
    input[type='color'] {
        -webkit-appearance: none;
        border: black solid 1px;
        width: 20px;
        height: 20px;
        cursor: pointer;
        padding: 0;
    }

    input[type='color']::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    input[type='color']::-webkit-color-swatch {
        border: none;
    }
    input[type='color']::-moz-color-swatch {
        border: none;
    }
</style>

<div class="alert-info">
    <p>Ces notifications servent à envoyer des mails aux utilisateurs pour les tenir informer de :
    <ul>
        <li>- Envoyer le récapitulatif de commandes</li>
        <li>- L'état d'advancement de leurs commandes (En cours, Traité, Irréalisable ...)</li>
        <li>- L'état sur des articles dans leurs paniers (Hors stock, Changé ...)</li>
    </ul>
    </p>
</div>

<section class="grid-3">
    <div>
        <h5 class="text-center">Paramétrage du mail :</h5>
        <div>
            <label for="<?= $varName ?>_use_website">Afficher le nom du site ?</label>
            <select name="<?= $varName ?>_use_website" id="<?= $varName ?>_use_website">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) === "0" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) ?? 'selected' ?> value="0">Non</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) === "1" ? 'selected' : '' ?> value="1">Oui</option>
            </select>
        </div>

        <div>
            <label for="<?= $varName ?>_use_logo">Afficher le logo ?</label>
            <select name="<?= $varName ?>_use_logo" id="<?= $varName ?>_use_logo">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) === "0" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) ?? 'selected' ?> value="0">Non</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) === "1" ? 'selected' : '' ?> value="1">Oui</option>
            </select>
        </div>
        <label for="<?= $varName ?>_logo">Logo (url) :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl()."App/Package/Shop/Views/Settings/Images/default.png" ?>" type="text" name="<?= $varName ?>_logo" id="<?= $varName ?>_logo" class="input" required>

        <div>
            <label for="<?= $varName ?>_use_header">Utiliser l'en tête personnalisée ?</label>
            <select name="<?= $varName ?>_use_header" id="<?= $varName ?>_use_header">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_header', $varName) === "0" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_header', $varName) ?? 'selected' ?> value="0">Non</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_header', $varName) === "1" ? 'selected' : '' ?> value="1">Oui</option>
            </select>
        </div>
        <label for="<?= $varName ?>_header">En tête personnalisée :</label>
        <textarea id="<?= $varName ?>_header" name="<?= $varName ?>_header" class="textarea"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_header', $varName) ?></textarea>

        <div>
            <label for="<?= $varName ?>_use_bottom">Utiliser le pied de page personnalisée ?</label>
            <select name="<?= $varName ?>_use_bottom" id="<?= $varName ?>_use_bottom">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_bottom', $varName) === "0" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_bottom', $varName) ?? 'selected' ?> value="0">Non</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_bottom', $varName) === "1" ? 'selected' : '' ?> value="1">Oui</option>
            </select>
        </div>
        <label for="<?= $varName ?>_bottom">Pied de page personnalisée :</label>
        <textarea id="<?= $varName ?>_bottom" name="<?= $varName ?>_bottom" class="textarea"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_bottom', $varName) ?></textarea>

        <div>
            <label for="<?= $varName ?>_use_footer">Utiliser le footer ?</label>
            <select name="<?= $varName ?>_use_footer" id="<?= $varName ?>_use_footer">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName) === "0" ? 'selected' : '' ?> value="0">Non</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName) === "1" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName) ?? 'selected' ?> value="1">Oui</option>
            </select>
        </div>
        <label for="<?= $varName ?>_footer">Footer :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer', $varName) ?? Website::getWebsiteName() . " - Merci de votre confiance et à bientôt sur notre site !" ?>" type="text" name="<?= $varName ?>_footer" id="<?= $varName ?>_footer" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center">Aperçu du mail :</h5>
        <div style="background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e' ?>" class="border dark:border-gray-700">
            <style>
                .container-recap {
                    font-family: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) ?? "'Arial', sans-serif" ?>;
                    width: 80%;
                    margin: 20px auto;
                    background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_container_color', $varName) ?? '#ffffff' ?>;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header-recap {
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_head_color', $varName) ?? '#214e7e' ?>;
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_color', $varName) ?? '#ffffff' ?>;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px;
                }
                .summary-item-recap {
                    border-bottom: 1px solid #eee;
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_color', $varName) ?? '#000000' ?>;
                    padding: 10px 0;
                }
                h1 {
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_color', $varName) ?? '#000000' ?>;
                }
                .summary-item:last-child-recap {
                    border-bottom: none;
                }
                .summary-title-recap {
                    font-weight: bold;
                }
                .footer-recap {
                    text-align: center;
                    margin-top: 20px;
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_color', $varName) ?? '#777' ?>;
                }
            </style>

            <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) === "1" || ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) === "1"): ?>
            <div class="container-recap" style="display: flex; align-items: center">
                <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) === "1"): ?>
                <img width="90px" src="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl()."App/Package/Shop/Views/Settings/Images/default.png" ?>" alt='Logo'">
                <?php endif;?>
                <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) === "1"): ?>
                    <h1><?= Website::getWebsiteName() ?></h1>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="container-recap">
                <div class="header-recap">
                    <h2>Titre de notification</h2>
                </div>
                <div class="summary-recap">
                    <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_header', $varName) === "1"): ?>
                    <div class="summary-item-recap">
                        <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_header', $varName) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="summary-item-recap">
                        <p>>>> Le texte de la notification sera automatiquement insérer à cet emplacement. <<<</p>
                    </div>
                    <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_bottom', $varName) === "1"): ?>
                    <div class="summary-item-recap">
                        <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_bottom', $varName) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName) === "1" || is_null(ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName))): ?>
                <div class="footer-recap">
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer', $varName) ?? Website::getWebsiteName() . " - Merci de votre confiance et à bientôt sur notre site !" ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <section class="mt-4">
            <h5>Apparences :</h5>
            <label for="<?= $varName ?>_font">Police d'écriture :</label>
            <select name="<?= $varName ?>_font" id="<?= $varName ?>_font">
                <optgroup label="Polices sans-serif">
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Arial', sans-serif" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) ?? 'selected' ?> value="'Arial', sans-serif">Arial</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Helvetica', sans-serif" ? 'selected' : '' ?> value="'Helvetica', sans-serif">Helvetica</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Verdana', sans-serif" ? 'selected' : '' ?> value="'Verdana', sans-serif">Verdana</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Tahoma', sans-serif" ? 'selected' : '' ?> value="'Tahoma', sans-serif">Tahoma</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Trebuchet MS', sans-serif" ? 'selected' : '' ?> value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                </optgroup>
                <optgroup label="Polices serif">
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Times New Roman', serif" ? 'selected' : '' ?> value="'Times New Roman', serif">Times New Roman</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Georgia', serif" ? 'selected' : '' ?> value="'Georgia', serif">Georgia</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Garamond', serif" ? 'selected' : '' ?> value="'Garamond', serif">Garamond</option>
                </optgroup>
                <optgroup label="Polices monospace">
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Courier New', monospace" ? 'selected' : '' ?> value="'Courier New', monospace">Courier New</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Lucida Console', monospace" ? 'selected' : '' ?> value="'Lucida Console', monospace">Lucida Console</option>
                    <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) === "'Consolas', monospace" ? 'selected' : '' ?> value="'Consolas', monospace">Consolas</option>
                </optgroup>
            </select>
            <div class="grid-3 mt-4">
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color">Couleur du fond</label>
                </div>
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_container_color" name="<?= $varName ?>_container_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_container_color', $varName) ?? '#ffffff' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_container_color">Couleur du conteneur</label>
                </div>
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_head_color" name="<?= $varName ?>_head_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_head_color', $varName) ?? '#214e7e' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_head_color">Couleur de l'en tête</label>
                </div>
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_text_color" name="<?= $varName ?>_text_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_color', $varName) ?? '#000000' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_text_color">Couleur des textes</label>
                </div>
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_title_color" name="<?= $varName ?>_title_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_color', $varName) ?? '#ffffff' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_title_color">Couleur du titre</label>
                </div>
                <div class="flex items-center">
                    <input type="color" id="<?= $varName ?>_footer_color" name="<?= $varName ?>_footer_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_color', $varName) ?? '#777' ?>">
                    <label style="margin-left: 0.5rem" for="<?= $varName ?>_footer_color">Couleur du footer</label>
                </div>
            </div>
        </section>
    </div>
</section>
