<?php
/* @var string $varName */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;

?>
<section class="grid-2 mt-4">
    <div class="grid-2">
        <div>
            <label for="<?= $varName ?>_use"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.use') ?></label>
            <select name="<?= $varName ?>_use" id="<?= $varName ?>_use">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) == "1" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) ?? 'selected' ?> value="1"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.yes') ?></option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) == "0" ? 'selected' : '' ?> value="0"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.no') ?></option>
            </select>
        </div>
        <div>
            <label for="<?= $varName ?>_client_address"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.popup') ?></label>
            <input
                value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_address', $varName) ?? LangManager::translate('shop.views.elements.global.withdrawPointMap.popup-default') ?>"
                type="text" name="<?= $varName ?>_client_address" id="<?= $varName ?>_client_address" class="input"
                required>
        </div>
    </div>
    <div>
        <label for="<?= $varName ?>_map_style"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.map') ?></label>
        <select id="<?= $varName ?>_map_style" name="<?= $varName ?>_map_style">
            <optgroup label="<?= LangManager::translate('shop.views.elements.global.withdrawPointMap.simplistic') ?>">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png">CartoDB Voyager
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png">CartoDB Positron
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}">
                    Esri World Street Map
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}">
                    Esri World Topographic Map
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) ?? 'selected' ?>
                    value="https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png">OpenStreetMap FR - Humanitaire
                </option>
            </optgroup>
            <optgroup label="<?= LangManager::translate('shop.views.elements.global.withdrawPointMap.simplistic+') ?>">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png">OpenStreetMap Standard
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png">OpenStreetMap DE
                </option>
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png">OpenTopoMap
                </option>
            </optgroup>
            <optgroup label="<?= LangManager::translate('shop.views.elements.global.withdrawPointMap.satellite') ?>">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}" ? 'selected' : '' ?>
                    value="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}">
                    Esri Satellite
                </option>
            </optgroup>
            <optgroup label="Dark Mode">
                <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style', $varName) === "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png" ? 'selected' : '' ?>
                    value="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png">CartoDB Dark Matter
                </option>
            </optgroup>
        </select>
    </div>
</section>

<section class="mt-4">
    <h6><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.appearances') ?></h6>
    <div class="grid-2">
        <div>
            <div class="icon-picker" data-id="for-<?= $varName ?>_client_icon" data-label="<?= LangManager::translate('shop.views.elements.global.withdrawPointMap.client-icon') ?>"
                 data-name="<?= $varName ?>_client_icon" data-placeholder="Sélectionner un icon"
                 data-value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_icon', $varName) ?? 'fa-solid fa-house' ?>"></div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_client_color" name="<?= $varName ?>_client_color"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_color', $varName) ?? '#369127' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_client_color"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.client-icon-color') ?></label>
            </div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_client_background" name="<?= $varName ?>_client_background"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_background', $varName) ?? '#000000' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_client_background"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.back-icon') ?>
                    client</label>
            </div>
            <?= LangManager::translate('shop.views.elements.global.withdrawPointMap.result') ?>
            <i class="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_icon', $varName) ?? 'fa-solid fa-house' ?>"
               style="color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_color', $varName) ?? '#369127' ?>; font-size: 1.1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_background', $varName) ?? '#000000' ?>; border-radius: 50%; padding: .4rem"></i>
        </div>
        <div>
            <div class="icon-picker" data-id="for-<?= $varName ?>_depot_icon" data-label="<?= LangManager::translate('shop.views.elements.global.withdrawPointMap.depot-icon') ?>"
                 data-name="<?= $varName ?>_depot_icon" data-placeholder="Sélectionner un icon"
                 data-value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_icon', $varName) ?? 'fa-solid fa-store' ?>"></div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_depot_color" name="<?= $varName ?>_depot_color"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_color', $varName) ?? '#ffffff' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_depot_color"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.depot-icon-color') ?></label>
            </div>
            <div class="flex items-center">
                <input type="color" id="<?= $varName ?>_depot_background" name="<?= $varName ?>_depot_background"
                       value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_background', $varName) ?? '#000000' ?>">
                <label style="margin-left: 0.5rem" for="<?= $varName ?>_depot_background"><?= LangManager::translate('shop.views.elements.global.withdrawPointMap.depot-back') ?></label>
            </div>
            <?= LangManager::translate('shop.views.elements.global.withdrawPointMap.result') ?>
            <i class="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_icon', $varName) ?? 'fa-solid fa-store' ?>"
               style="color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_color', $varName) ?? '#ffffff' ?>; font-size: 1.1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_background', $varName) ?? '#000000' ?>; border-radius: 50%; padding: .4rem"></i>
        </div>
    </div>
</section>
