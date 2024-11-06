<?php
/* @var string $varName */

use CMW\Model\Shop\Setting\ShopSettingsModel;

?>
<section class="grid-2 mt-4">
    <div class="grid-2">
        <div>
            <label for="<?= $varName ?>_use">Utiliser la carte interactive</label>
            <select name="<?= $varName ?>_use" id="<?= $varName ?>_use">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use') == "1" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use') ?? 'selected' ?> value="1">Oui</option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use') == "0" ? 'selected' : '' ?> value="0">Non</option>
            </select>
        </div>
        <div>
            <label for="<?= $varName ?>_client_address">Nom de l'adresse du client dans le popup :</label>
            <input
                value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_address') ?? 'Votre adresse' ?>"
                type="text" name="<?= $varName ?>_client_address" id="<?= $varName ?>_client_address" class="input"
                required>
        </div>
    </div>
    <div>
        <label for="<?= $varName ?>_map_style">Style de la carte</label>
        <select id="<?= $varName ?>_map_style" name="<?= $varName ?>_map_style">
            <optgroup label="Simpliste">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png">CartoDB Voyager
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png">CartoDB Positron
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}">
                    Esri World Street Map
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}">
                    Esri World Topographic Map
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') ?? 'selected' ?>
                    value="https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png">OpenStreetMap FR - Humanitaire
                </option>
            </optgroup>
            <optgroup label="Simpliste détaillé">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png">OpenStreetMap Standard
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png">OpenStreetMap DE
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png">OpenTopoMap
                </option>
            </optgroup>
            <optgroup label="Satellite">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}">
                    Esri Satellite
                </option>
            </optgroup>
            <optgroup label="Dark Mode">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') == "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png">CartoDB Dark Matter
                </option>
            </optgroup>
        </select>
    </div>
</section>

<section class="mt-4">
    <h6>Apparences :</h6>
    <div class="grid-2">
        <div>
            <div class="icon-picker" data-id="for-<?= $varName ?>_client_icon" data-label="Icône du client"
                 data-name="<?= $varName ?>_client_icon" data-placeholder="Sélectionner un icon"
                 data-value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_icon') ?? 'fa-solid fa-house' ?>"></div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_client_color" name="<?= $varName ?>_client_color"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_color') ?? '#369127' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_client_color">Couleur de l'icône client</label>
            </div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_client_background" name="<?= $varName ?>_client_background"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_background') ?? '#000000' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_client_background">Fond de l'icône
                    client</label>
            </div>
            Résultat :
            <i class="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_icon') ?? 'fa-solid fa-house' ?>"
               style="color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_color') ?? '#369127' ?>; font-size: 1.1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_background') ?? '#000000' ?>; border-radius: 50%; padding: .4rem"></i>
        </div>
        <div>
            <div class="icon-picker" data-id="for-<?= $varName ?>_depot_icon" data-label="Icône des dépôts"
                 data-name="<?= $varName ?>_depot_icon" data-placeholder="Sélectionner un icon"
                 data-value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_icon') ?? 'fa-solid fa-store' ?>"></div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_depot_color" name="<?= $varName ?>_depot_color"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_color') ?? '#ffffff' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_depot_color">Couleur de l'icône depôt</label>
            </div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_depot_background" name="<?= $varName ?>_depot_background"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_background') ?? '#000000' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_depot_background">Fond de l'icône depôt</label>
            </div>
            Résultat :
            <i class="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_icon') ?? 'fa-solid fa-store' ?>"
               style="color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_color') ?? '#ffffff' ?>; font-size: 1.1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_background') ?? '#000000' ?>; border-radius: 50%; padding: .4rem"></i>
        </div>
    </div>
</section>
