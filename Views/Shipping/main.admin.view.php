<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

/**
 * @var \CMW\Entity\Shop\Shippings\ShopShippingEntity [] $shippings
 * @var \CMW\Entity\Shop\Shippings\ShopShippingZoneEntity [] $shippingZones
 * @var CMW\Entity\Shop\Country\ShopCountryEntity[] $countries
 * @var \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity [] $withdrawPoints
 * @var \CMW\Interface\Shop\IShippingMethod [] $shippingMethods
 * @var \CMW\Interface\Shop\IShippingMethod [] $configShippingMethods
 * @var string $symbol
 * */

$title = LangManager::translate('shop.views.shipping.main.title');
$description = '';

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-truck"></i> <span class="m-lg-auto"><?= LangManager::translate('shop.views.shipping.main.title') ?></span></h3>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="card">
    <div class="tab-menu">
        <ul class="tab-horizontal" data-tabs-toggle="#tab-content-1">
            <li>
                <button data-tabs-target="#tab1" role="tab"><i class="fa-solid fa-truck-fast"></i> <?= LangManager::translate('shop.views.shipping.main.shipping_method') ?></button>
            </li>
            <li>
                <button data-tabs-target="#tab2" role="tab"><i class="fa-solid fa-boxes-packing"></i> <?= LangManager::translate('shop.views.shipping.main.withdraw_method') ?></button>
            </li>
        </ul>
    </div>
    <div id="tab-content-1">
        <div class="tab-content" id="tab1">
            <button data-modal-toggle="modal-add-shipping" type="button" class="btn-primary mb-4"><?= LangManager::translate('shop.views.shipping.main.add_shipping_method') ?></button>
            <div class="table-container">
                <table data-load-per-page="10">
                    <thead>
                    <tr>
                        <th><?= LangManager::translate('shop.views.shipping.main.name') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.action') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.price') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.min') ?> <?= $symbol ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.max') ?> <?= $symbol ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.weight_max') ?></th>
                        <th class="text-center"><?= LangManager::translate('shop.views.shipping.main.action_btn') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($shippings as $shipping): ?>
                        <?php if ($shipping->getType() == 0): ?>
                            <tr>
                                <td><?= $shipping->getName() ?></td>
                                <td><?= $shipping->getZone()->getName() ?></td>
                                <td><?= $shipping->getPriceFormatted() ?></td>
                                <td><?= $shipping->getMinTotalCartPrice() ? $shipping->getMinTotalCartPrice() .  $symbol : '∞' ?></td>
                                <td><?= $shipping->getMaxTotalCartPrice() ? $shipping->getMaxTotalCartPrice() . $symbol : '∞' ?></td>
                                <td><?= $shipping->getMaxTotalWeight() ? $shipping->getMaxTotalWeight() . 'g' : '∞' ?></td>
                                <td class="text-center">
                                    <button data-modal-toggle="modal-edit-delivery-<?= $shipping->getId() ?>" class="text-info mr-2"
                                            type="button"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button data-modal-toggle="modal-delete-delivery-<?= $shipping->getId() ?>" class="text-danger"
                                            type="button"><i class="fa-solid fa-trash"></i></button>
                                    <!--MODAL DELETE-->
                                    <div id="modal-delete-delivery-<?= $shipping->getId() ?>" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header-danger">
                                                <h6><?= LangManager::translate('shop.views.shipping.main.delete') ?> <?= $shipping->getName() ?></h6>
                                                <button type="button" data-modal-hide="modal-delete-delivery-<?= $shipping->getId() ?>"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <?= LangManager::translate('shop.views.shipping.main.sure') ?>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="shipping/method/delete/<?= $shipping->getId() ?>" type="button"
                                                   class="btn-danger"><?= LangManager::translate('shop.views.shipping.main.delete') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--MODAL EDIT-->
                                    <div id="modal-edit-delivery-<?= $shipping->getId() ?>" class="modal-container">
                                        <div class="modal-lg">
                                            <div class="modal-header">
                                                <h6><?= LangManager::translate('shop.views.shipping.main.edit') ?> <?= $shipping->getName() ?></h6>
                                                <button type="button" data-modal-hide="modal-edit-delivery-<?= $shipping->getId() ?>"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <form action="shipping/delivery/edit/<?= $shipping->getId() ?>" method="post">
                                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                                <div class="modal-body">
                                                    <div class="grid-2">
                                                        <div>
                                                            <label for="shipping_name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                                                            <input value="<?= $shipping->getName() ?>" required type="text" name="shipping_name" id="shipping_name" class="input" placeholder="Chronopost 24h">
                                                        </div>
                                                        <div>
                                                            <label for="shipping_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.price') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getPrice() ?>" type="text" name="shipping_price" id="shipping_price-<?= $shipping->getId() ?>" placeholder="9.99">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-2 mb-6">
                                                        <div>
                                                            <label for="shipping_zone"><?= LangManager::translate('shop.views.shipping.main.action') ?><span style="color: red">*</span> :</label>
                                                            <select required name="shipping_zone" id="shipping_zone">
                                                                <?php foreach ($shippingZones as $zone) : ?>
                                                                    <option <?= $shipping->getZone()->getId() == $zone->getId() ? 'selected' : '' ?> value="<?= $zone->getId() ?>">
                                                                        <?= $zone->getName() ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="shipping_method"><?= LangManager::translate('shop.views.shipping.main.method') ?></label>
                                                            <select name="shipping_method" id="shipping_method">
                                                                <?php foreach ($shippingMethods as $method) : ?>
                                                                    <?php if ($method->canUseInShippingMethod()): ?>
                                                                        <option <?= $shipping->getShippingMethod()->varName() == $method->varName() ? 'selected' : '' ?> value="<?= $method->varName() ?>">
                                                                            <?= $method->name() ?>
                                                                        </option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <p class="font-bold mt-6"><?= LangManager::translate('shop.views.shipping.main.conditions') ?></p>
                                                    <div class="grid-3">
                                                        <div>
                                                            <label for="shipping_weight"><?= LangManager::translate('shop.views.shipping.main.weight_max_g') ?></label>
                                                            <div class="input-group">
                                                                <i>g</i>
                                                                <input value="<?= $shipping->getMaxTotalWeight() ?>" type="number" name="shipping_weight" id="shipping_weight" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="shipping_min_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.mini_cart') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getMinTotalCartPrice() ?>" type="text" name="shipping_min_price" id="shipping_min_price-<?= $shipping->getId() ?>" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="shipping_max_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.maxi_cart') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getMaxTotalCartPrice() ?>" type="text" name="shipping_max_price" id="shipping_max_price-<?= $shipping->getId() ?>" placeholder="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small><?= LangManager::translate('shop.views.shipping.main.warning_cart', ['symbol' => $symbol]) ?></small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.edit_btn') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-content" id="tab2">
            <button data-modal-toggle="modal-add-withdraw" type="button" class="btn-primary mb-4"><?= LangManager::translate('shop.views.shipping.main.add_withdraw_method') ?></button>
            <div class="table-container">
                <table data-load-per-page="10">
                    <thead>
                    <tr>
                        <th><?= LangManager::translate('shop.views.shipping.main.name') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.deposit') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.action') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.price') ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.min') ?> <?= $symbol ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.max') ?> <?= $symbol ?></th>
                        <th><?= LangManager::translate('shop.views.shipping.main.weight_max') ?></th>
                        <th class="text-center"><?= LangManager::translate('shop.views.shipping.main.action_btn') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($shippings as $shipping): ?>
                        <?php if ($shipping->getType() == 1): ?>
                            <tr>
                                <td><?= $shipping->getName() ?></td>
                                <td>#<?= $shipping->getWithdrawPoint()->getId() ?>-<?= $shipping->getWithdrawPoint()->getName() ?></td>
                                <td><?= $shipping->getZone()->getName() ?></td>
                                <td><?= $shipping->getPriceFormatted() ?></td>
                                <td><?= $shipping->getMinTotalCartPrice() ? $shipping->getMinTotalCartPrice() . $symbol : '∞' ?></td>
                                <td><?= $shipping->getMaxTotalCartPrice() ? $shipping->getMaxTotalCartPrice() . $symbol : '∞' ?></td>
                                <td><?= $shipping->getMaxTotalWeight() ? $shipping->getMaxTotalWeight() . 'g' : '∞' ?></td>
                                <td class="text-center">
                                    <button data-modal-toggle="modal-edit-withdraw-<?= $shipping->getId() ?>" class="text-info mr-2"
                                            type="button"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button data-modal-toggle="modal-delete-withdraw-<?= $shipping->getId() ?>" class="text-danger"
                                            type="button"><i class="fa-solid fa-trash"></i></button>
                                    <!--MODAL DELETE-->
                                    <div id="modal-delete-withdraw-<?= $shipping->getId() ?>" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header-danger">
                                                <h6><?= LangManager::translate('shop.views.shipping.main.delete') ?> <?= $shipping->getName() ?></h6>
                                                <button type="button" data-modal-hide="modal-delete-withdraw-<?= $shipping->getId() ?>"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <?= LangManager::translate('shop.views.shipping.main.sure') ?>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="shipping/method/delete/<?= $shipping->getId() ?>" type="button"
                                                   class="btn-danger"><?= LangManager::translate('shop.views.shipping.main.delete') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--MODAL EDIT-->
                                    <div id="modal-edit-withdraw-<?= $shipping->getId() ?>" class="modal-container">
                                        <div class="modal-lg">
                                            <div class="modal-header">
                                                <h6><?= LangManager::translate('shop.views.shipping.main.edit') ?> <?= $shipping->getName() ?></h6>
                                                <button type="button" data-modal-hide="modal-edit-withdraw-<?= $shipping->getId() ?>"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <form action="shipping/withdraw/edit/<?= $shipping->getId() ?>" method="post">
                                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                                <div class="modal-body">
                                                    <div class="grid-2">
                                                        <div>
                                                            <label for="withdraw_name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                                                            <input value="<?= $shipping->getName() ?>"  required type="text" name="withdraw_name" id="withdraw_name" class="input" placeholder="Chronopost 24h">
                                                        </div>
                                                        <div>
                                                            <label for="withdraw_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.price') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getPrice() ?>"  type="text" name="withdraw_price" id="withdraw_price-<?= $shipping->getId() ?>" placeholder="9.99">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-3 mb-6">
                                                        <div>
                                                            <label for="withdraw_depot"><?= LangManager::translate('shop.views.shipping.main.deposit') ?><span style="color: red">*</span> :</label>
                                                            <select required name="withdraw_depot" id="withdraw_depot">
                                                                <?php foreach ($withdrawPoints as $withdrawPoint) : ?>
                                                                    <option <?= $shipping->getWithdrawPoint()->getId() == $withdrawPoint->getId() ? 'selected' : '' ?> value="<?= $withdrawPoint->getId() ?>">
                                                                        #<?= $withdrawPoint->getId() ?>-<?= $withdrawPoint->getName() ?> (<?= $withdrawPoint->getAddressDistance() ?>km)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="withdraw_zone"><?= LangManager::translate('shop.views.shipping.main.action') ?><span style="color: red">*</span> :</label>
                                                            <select required name="withdraw_zone" id="withdraw_zone">
                                                                <?php foreach ($shippingZones as $zone) : ?>
                                                                    <option <?= $shipping->getZone()->getId() == $zone->getId() ? 'selected': '' ?> value="<?= $zone->getId() ?>">
                                                                        <?= $zone->getName() ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="withdraw_method"><?= LangManager::translate('shop.views.shipping.main.method') ?></label>
                                                            <select name="withdraw_method" id="withdraw_method">
                                                                <?php foreach ($shippingMethods as $method) : ?>
                                                                    <?php if ($method->canUseInWithdrawalMethod()): ?>
                                                                        <option <?= $shipping->getShippingMethod()->varName() == $method->varName() ? 'selected' : '' ?> value="<?= $method->varName() ?>">
                                                                            <?= $method->name() ?>
                                                                        </option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <p class="font-bold mt-6"><?= LangManager::translate('shop.views.shipping.main.conditions') ?></p>
                                                    <div class="grid-3">
                                                        <div>
                                                            <label for="withdraw_weight"><?= LangManager::translate('shop.views.shipping.main.weight_max_g') ?></label>
                                                            <div class="input-group">
                                                                <i>g</i>
                                                                <input value="<?= $shipping->getMaxTotalWeight() ?>" type="number" name="withdraw_weight" id="withdraw_weight" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="withdraw_min_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.mini_cart') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getMinTotalCartPrice() ?>" type="text" name="withdraw_min_price" id="withdraw_min_price-<?= $shipping->getId() ?>" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="withdraw_max_price-<?= $shipping->getId() ?>"><?= LangManager::translate('shop.views.shipping.main.maxi_cart') ?></label>
                                                            <div class="input-group">
                                                                <i><?= $symbol ?></i>
                                                                <input value="<?= $shipping->getMaxTotalCartPrice() ?>" type="text" name="withdraw_max_price" id="withdraw_max_price-<?= $shipping->getId() ?>" placeholder="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small><?= LangManager::translate('shop.views.shipping.main.warning_cart', ['symbol' => $symbol]) ?></small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.edit_btn') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<h5 class="mt-6"><?= LangManager::translate('shop.views.shipping.main.settings') ?></h5>
<div class="grid-3">
    <div class="col-span-2">
        <div class="card">
            <div class="card-title">
                <h6><i class="fa-solid fa-warehouse"></i> <?= LangManager::translate('shop.views.shipping.main.deposit_address') ?></h6>
                <button data-modal-toggle="modal-add-depot" type="button" class="btn-primary-sm"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
            </div>
            <div class="grid-3">
                <?php foreach ($withdrawPoints as $withdrawPoint): ?>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between">
                            <h6>#<?= $withdrawPoint->getId() ?>-<?= $withdrawPoint->getName() ?></h6>
                            <div>
                                <button data-modal-toggle="modal-edit-depot-<?= $withdrawPoint->getId() ?>" class="btn-primary-sm mr-2"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button data-modal-toggle="modal-delete-depot-<?= $withdrawPoint->getId() ?>" class="btn-danger-sm"><i class="fa-solid fa-trash"></i></button>
                            </div>

                        </div>
                        <p><?= $withdrawPoint->getAddressLine() ?></p>
                        <p><?= $withdrawPoint->getAddressPostalCode() ?> <?= $withdrawPoint->getAddressCity() ?></p>
                        <p><?= $withdrawPoint->getFormattedCountry() ?></p>
                        <hr>
                        <p><?= LangManager::translate('shop.views.shipping.main.max_dist_allowed') ?> <?= $withdrawPoint->getAddressDistance() ?> km</p>
                    </div>
                    <!--MODAL DELETE-->
                    <div id="modal-delete-depot-<?= $withdrawPoint->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6><?= LangManager::translate('shop.views.shipping.main.delete') ?> #<?= $withdrawPoint->getId() ?>-<?= $withdrawPoint->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-delete-depot-<?= $withdrawPoint->getId() ?>"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <?= LangManager::translate('shop.views.shipping.main.sure') ?>
                            </div>
                            <div class="modal-footer">
                                <a href="shipping/depot/delete/<?= $withdrawPoint->getId() ?>" type="button"
                                   class="btn-danger"><?= LangManager::translate('shop.views.shipping.main.delete') ?></a>
                            </div>
                        </div>
                    </div>
                    <!--MODAL EDIT-->
                    <div id="modal-edit-depot-<?= $withdrawPoint->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6><?= LangManager::translate('shop.views.shipping.main.edit') ?> #<?= $withdrawPoint->getId() ?>-<?= $withdrawPoint->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-edit-depot-<?= $withdrawPoint->getId() ?>"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="shipping/depot/edit/<?= $withdrawPoint->getId() ?>" method="post">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="modal-body">
                                    <label for="name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                                    <input value="<?= $withdrawPoint->getName() ?>" required type="text" name="name" id="name" class="input" placeholder="Entreprise">
                                    <label for="distance"><?= LangManager::translate('shop.views.shipping.main.max_dist_km') ?><span style="color: red">*</span></label>
                                    <input value="<?= $withdrawPoint->getAddressDistance() ?>" required type="number" name="distance" id="distance" class="input" placeholder="100">
                                    <small><?= LangManager::translate('shop.views.shipping.main.warn_dist') ?></small>
                                    <label for="address"><?= LangManager::translate('shop.views.shipping.main.address') ?><span style="color: red">*</span> :</label>
                                    <input value="<?= $withdrawPoint->getAddressLine() ?>" required type="text" name="address" id="address" class="input" placeholder="9 rue du paradis">
                                    <div class="grid-2">
                                        <div>
                                            <label for="city"><?= LangManager::translate('shop.views.shipping.main.city') ?><span style="color: red">*</span> :</label>
                                            <input value="<?= $withdrawPoint->getAddressCity() ?>" required type="text" name="city" id="city" class="input" placeholder="Ciel">
                                        </div>
                                        <div>
                                            <label for="postalCode"><?= LangManager::translate('shop.views.shipping.main.cp') ?><span style="color: red">*</span> :</label>
                                            <input value="<?= $withdrawPoint->getAddressPostalCode() ?>" required type="text" name="postalCode" id="postalCode" class="input" placeholder="66999">
                                        </div>
                                    </div>
                                    <label for="country"><?= LangManager::translate('shop.views.shipping.main.country') ?><span style="color: red">*</span></label>
                                    <select required name="country" id="country">
                                        <option value="ALL" selected><?= LangManager::translate('shop.views.shipping.main.all_the_world') ?></option>
                                        <?php foreach ($countries as $country) : ?>
                                            <option <?= $withdrawPoint->getAddressCountry() == $country->getCode() ? 'selected' : '' ?> value="<?= $country->getCode() ?>">
                                                <?= $country->getName() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.edit_btn') ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-title">
            <h6><i class="fa-solid fa-earth-europe"></i> <?= LangManager::translate('shop.views.shipping.main.zone') ?></h6>
            <button data-modal-toggle="modal-add-zone" type="button" class="btn-primary-sm"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
        </div>

        <div class="table-container">
            <table id="table1" data-load-per-page="10">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.shipping.main.name') ?></th>
                    <th><?= LangManager::translate('shop.views.shipping.main.action') ?></th>
                    <th class="text-center"><?= LangManager::translate('shop.views.shipping.main.action_btn') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($shippingZones as $zone): ?>
                    <tr>
                        <td><?= $zone->getName() ?></td>
                        <td><?= $zone->getFormattedCountry() ?></td>
                        <td class="text-center">
                            <button data-modal-toggle="modal-edit-zone-<?= $zone->getId() ?>" class="text-info mr-2"
                                    type="button"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button data-modal-toggle="modal-delete-zone-<?= $zone->getId() ?>" class="text-danger"
                                    type="button"><i class="fa-solid fa-trash"></i></button>
                            <!--MODAL DELETE-->
                            <div id="modal-delete-zone-<?= $zone->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header-danger">
                                        <h6><?= LangManager::translate('shop.views.shipping.main.delete') ?> <?= $zone->getName() ?></h6>
                                        <button type="button" data-modal-hide="modal-delete-zone-<?= $zone->getId() ?>"><i
                                                class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <?= LangManager::translate('shop.views.shipping.main.sure') ?>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="shipping/zone/delete/<?= $zone->getId() ?>" type="button"
                                           class="btn-danger"><?= LangManager::translate('shop.views.shipping.main.delete') ?></a>
                                    </div>
                                </div>
                            </div>
                            <!--MODAL EDIT-->
                            <div id="modal-edit-zone-<?= $zone->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header">
                                        <h6><?= LangManager::translate('shop.views.shipping.main.edit') ?> <?= $zone->getName() ?></h6>
                                        <button type="button" data-modal-hide="modal-edit-zone-<?= $zone->getId() ?>"><i
                                                class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <form action="shipping/zone/edit/<?= $zone->getId() ?>" method="post">
                                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                        <div class="modal-body">
                                            <label for="default-input"><?= LangManager::translate('shop.views.shipping.main.name') ?></label>
                                            <input type="text" name="name" value="<?= $zone->getName() ?>"
                                                   id="default-input" class="input" placeholder="Default">
                                            <label for="select"><?= LangManager::translate('shop.views.shipping.main.action') ?></label>
                                            <select name="zone" id="select">
                                                <option value="ALL"><?= LangManager::translate('shop.views.shipping.main.all_the_world') ?></option>
                                                <?php foreach ($countries as $country) : ?>
                                                    <option <?= $zone->getCountry() === $country->getCode() ? 'selected' : '' ?>
                                                        value="<?= $country->getCode() ?>">
                                                        <?= $country->getName() ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.edit_btn') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<hr>
<h5 class="mt-6"><?= LangManager::translate('shop.views.shipping.main.method_settings') ?></h5>
<div class="tab-vertical-container mt-2">
    <div class="tab-vertical" data-tabs-toggle="#tab-content-2">
        <?php foreach ($configShippingMethods as $method): ?>
            <button class="tab-button" data-tabs-target="#tab<?= $method->varName() ?>" role="tab"><?= $method->name() ?></button>
        <?php endforeach; ?>
    </div>
    <div id="tab-content-2" class="tab-container">
        <?php foreach ($configShippingMethods as $method): ?>
            <div class="tab-content" id="tab<?= $method->varName() ?>">
                <div class="card">
                    <h6><?= $method->name() ?></h6>
                    <form id="virtualGlobal" action="shipping/method" method="post">
                        <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
                        <?php $method->includeGlobalConfigWidgets(); ?>
                        <div class="d-flex justify-content-center mt-4">
                            <button form="virtualGlobal" type="submit"
                                    class="btn btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!--MODAL ADD ZONE-->
<div id="modal-add-zone" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.shipping.main.add_zone') ?></h6>
            <button type="button" data-modal-hide="modal-add-zone"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="shipping/zone" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="default-input"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                <input required type="text" name="name" id="default-input" class="input" placeholder="France">
                <label for="select"><?= LangManager::translate('shop.views.shipping.main.action') ?><span style="color: red">*</span></label>
                <select required name="zone" id="select">
                    <option value="ALL" selected><?= LangManager::translate('shop.views.shipping.main.all_the_world') ?></option>
                    <?php foreach ($countries as $country) : ?>
                        <option value="<?= $country->getCode() ?>">
                            <?= $country->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
            </div>
        </form>
    </div>
</div>

<!--MODAL ADD DEPOT-->
<div id="modal-add-depot" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.shipping.main.add_depot') ?></h6>
            <button type="button" data-modal-hide="modal-add-depot"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="shipping/depot" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                <input required type="text" name="name" id="name" class="input" placeholder="Entreprise">
                <label for="distance"><?= LangManager::translate('shop.views.shipping.main.max_dist_km') ?><span style="color: red">*</span></label>
                <input required type="number" name="distance" id="distance" class="input" placeholder="100">
                <small><?= LangManager::translate('shop.views.shipping.main.warn_dist') ?></small>
                <label for="address"><?= LangManager::translate('shop.views.shipping.main.address') ?><span style="color: red">*</span> :</label>
                <input required type="text" name="address" id="address" class="input" placeholder="9 rue du paradis">
                <div class="grid-2">
                    <div>
                        <label for="city"><?= LangManager::translate('shop.views.shipping.main.city') ?><span style="color: red">*</span> :</label>
                        <input required type="text" name="city" id="city" class="input" placeholder="Ciel">
                    </div>
                    <div>
                        <label for="postalCode"><?= LangManager::translate('shop.views.shipping.main.cp') ?><span style="color: red">*</span> :</label>
                        <input required type="text" name="postalCode" id="postalCode" class="input" placeholder="66999">
                    </div>
                </div>
                <label for="country"><?= LangManager::translate('shop.views.shipping.main.country') ?><span style="color: red">*</span></label>
                <select required name="country" id="country">
                    <option value="ALL" selected><?= LangManager::translate('shop.views.shipping.main.all_the_world') ?></option>
                    <?php foreach ($countries as $country) : ?>
                        <option value="<?= $country->getCode() ?>">
                            <?= $country->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
            </div>
        </form>
    </div>
</div>

<!--MODAL ADD shipping delivery-->
<div id="modal-add-shipping" class="modal-container">
    <div class="modal-lg">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.shipping.main.add_shipping_method') ?></h6>
            <button type="button" data-modal-hide="modal-add-shipping"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="shipping/delivery" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div>
                        <label for="shipping_name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                        <input required type="text" name="shipping_name" id="shipping_name" class="input" placeholder="Chronopost 24h">
                    </div>
                    <div>
                        <label for="shipping_price"><?= LangManager::translate('shop.views.shipping.main.price') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="shipping_price" id="shipping_price" placeholder="9.99">
                        </div>
                    </div>
                </div>
                <div class="grid-2 mb-6">
                    <div>
                        <label for="shipping_zone"><?= LangManager::translate('shop.views.shipping.main.action') ?><span style="color: red">*</span> :</label>
                        <select required name="shipping_zone" id="shipping_zone">
                            <?php foreach ($shippingZones as $zone) : ?>
                                <option value="<?= $zone->getId() ?>">
                                    <?= $zone->getName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="shipping_method"><?= LangManager::translate('shop.views.shipping.main.method') ?></label>
                        <select name="shipping_method" id="shipping_method">
                            <?php foreach ($shippingMethods as $method) : ?>
                                <?php if ($method->canUseInShippingMethod()): ?>
                                    <option value="<?= $method->varName() ?>">
                                        <?= $method->name() ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p class="font-bold mt-6"><?= LangManager::translate('shop.views.shipping.main.conditions') ?></p>
                <div class="grid-3">
                    <div>
                        <label for="shipping_weight"><?= LangManager::translate('shop.views.shipping.main.weight_max_g') ?></label>
                        <div class="input-group">
                            <i>g</i>
                            <input type="number" name="shipping_weight" id="shipping_weight" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="shipping_min_price"><?= LangManager::translate('shop.views.shipping.main.mini_cart') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="shipping_min_price" id="shipping_min_price" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="shipping_max_price"><?= LangManager::translate('shop.views.shipping.main.maxi_cart') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="shipping_max_price" id="shipping_max_price" placeholder="0">
                        </div>
                    </div>
                </div>
                <small><?= LangManager::translate('shop.views.shipping.main.warning_cart', ['symbol' => $symbol]) ?></small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
            </div>
        </form>
    </div>
</div>

<!--MODAL ADD shipping delivery-->
<div id="modal-add-withdraw" class="modal-container">
    <div class="modal-lg">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.shipping.main.add_shipping_withdraw') ?></h6>
            <button type="button" data-modal-hide="modal-add-withdraw"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="shipping/withdraw" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div>
                        <label for="withdraw_name"><?= LangManager::translate('shop.views.shipping.main.name') ?><span style="color: red">*</span></label>
                        <input required type="text" name="withdraw_name" id="withdraw_name" class="input" placeholder="Chronopost 24h">
                    </div>
                    <div>
                        <label for="withdraw_price"><?= LangManager::translate('shop.views.shipping.main.price') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="withdraw_price" id="withdraw_price" placeholder="9.99">
                        </div>
                    </div>
                </div>
                <div class="grid-3 mb-6">
                    <div>
                        <label for="withdraw_depot"><?= LangManager::translate('shop.views.shipping.main.deposit') ?><span style="color: red">*</span> :</label>
                        <select required name="withdraw_depot" id="withdraw_depot">
                            <?php foreach ($withdrawPoints as $withdrawPoint) : ?>
                                <option value="<?= $withdrawPoint->getId() ?>">
                                    #<?= $withdrawPoint->getId() ?>-<?= $withdrawPoint->getName() ?> (<?= $withdrawPoint->getAddressDistance() ?>km)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="withdraw_zone"><?= LangManager::translate('shop.views.shipping.main.action') ?><span style="color: red">*</span> :</label>
                        <select required name="withdraw_zone" id="withdraw_zone">
                            <?php foreach ($shippingZones as $zone) : ?>
                                <option value="<?= $zone->getId() ?>">
                                    <?= $zone->getName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="withdraw_method"><?= LangManager::translate('shop.views.shipping.main.method') ?></label>
                        <select name="withdraw_method" id="withdraw_method">
                            <?php foreach ($shippingMethods as $method) : ?>
                                <?php if ($method->canUseInWithdrawalMethod()): ?>
                                    <option value="<?= $method->varName() ?>">
                                        <?= $method->name() ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p class="font-bold mt-6"><?= LangManager::translate('shop.views.shipping.main.conditions') ?></p>
                <div class="grid-3">
                    <div>
                        <label for="withdraw_weight"><?= LangManager::translate('shop.views.shipping.main.weight_max_g') ?></label>
                        <div class="input-group">
                            <i>g</i>
                            <input type="number" name="withdraw_weight" id="withdraw_weight" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="withdraw_min_price"><?= LangManager::translate('shop.views.shipping.main.mini_cart') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="withdraw_min_price" id="withdraw_min_price" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="withdraw_max_price"><?= LangManager::translate('shop.views.shipping.main.maxi_cart') ?></label>
                        <div class="input-group">
                            <i><?= $symbol ?></i>
                            <input type="text" name="withdraw_max_price" id="withdraw_max_price" placeholder="0">
                        </div>
                    </div>
                </div>
                <small><?= LangManager::translate('shop.views.shipping.main.warning_cart', ['symbol' => $symbol]) ?></small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary"><?= LangManager::translate('shop.views.shipping.main.add') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    let inputElements = document.querySelectorAll('input[name="withdraw_min_price"], input[name="withdraw_max_price"], input[name="withdraw_price"], input[name="shipping_max_price"], input[name="shipping_min_price"], input[name="shipping_price"]');

    inputElements.forEach(inputElement => {
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
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('input[id^="withdraw_price-"], input[id^="withdraw_max_price-"], input[id^="withdraw_min_price-"], input[id^="shipping_min_price-"], input[id^="shipping_max_price-"], input[id^="shipping_price-"]').forEach(input => {

            input.addEventListener("input", function(e) {
                // Remplace les virgules par des points
                this.value = this.value.replace(',', '.');

                // Supprime tous les caractères non numériques sauf le point et la virgule
                this.value = this.value.replace(/[^0-9.,]/g, '');
            });

            // Autorise uniquement les touches numériques, la virgule et le point
            input.addEventListener("keypress", function(e) {
                if ((e.key < '0' || e.key > '9') && e.key !== '.' && e.key !== ',') {
                    e.preventDefault();
                }
            });
        });
    });

</script>