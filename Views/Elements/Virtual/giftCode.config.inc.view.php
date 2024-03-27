<?php

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;

?>

<div class="row">
    <div class="form-group">
        <label for="gift_code_code">texte :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getSetting('gift_code_code') ?>"
               placeholder="CODE"
               type="text"
               name="gift_code_code"
               id="gift_code_code"
               class="form-control"
               >
    </div>
</div>

