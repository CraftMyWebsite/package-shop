<?php

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;

/* @var ?int $itemId */
/* @var string $varName */
?>


<label for="<?=$varName?>code">texte :</label>
<input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getSetting($varName.'code',$itemId) ?>"
       placeholder="CODE"
       type="text"
       name="<?=$varName?>code"
       id="<?=$varName?>code"
       class="form-control"
       required
>