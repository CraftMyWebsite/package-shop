<?php
/* @var string $varName */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Shipping\ShopShippingRequirementModel;

$requirement = ShopShippingRequirementModel::getInstance();

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

<section>
    <label for="<?= $varName ?>_global"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.object') ?></label>
    <input value="<?= $requirement->getSetting($varName . '_global') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting_title') ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
</section>
