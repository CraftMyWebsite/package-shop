<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Controller\Shop\Admin\Notify\ShopNotifyController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Model\Core\MailModel;
use CMW\Utils\Date;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;
use DateTime;

/**
 * Class: @ShopVirtualItemsGiftCodeController
 * @package Shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopVirtualItemsGiftCodeController extends AbstractController
{
    /**
     * @param ShopItemEntity $item
     * @param UserEntity $user
     */
    public function sedMailWithGiftCode(ShopItemEntity $item, UserEntity $user): void
    {
        $giftCodePrefix = 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = LangManager::translate('shop.views.elements.global.giftCode.title-default');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            $amount = $item->getPrice() . $symbol;
        } else {
            $amount = $symbol . $item->getPrice();
        }

        $timestamp = time();
        $dateTime = date('Y-m-d H:i:s', $timestamp);
        $date = new DateTime($dateTime);
        $date->modify('+1 year');
        $endDateTime = $date->format('Y-m-d H:i:s');

        if (!ShopDiscountModel::getInstance()->createDiscount($globalName . ' ' . $amount, 3, $dateTime, $endDateTime, 1, 0, null, $item->getPrice(), null, 1, 0, $code, 0, 0, 0)) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de créer la carte cadeau de ' . $amount . '!');
            ShopNotifyController::getInstance()->notifyUser($user->getMail(), $globalName . $amount, $globalName . $amount, "Nous n'avons pas réussi à créer votre bon cadeau de" . $amount . ". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = Date::formatDate($endDateTime);
        $message = LangManager::translate('shop.views.elements.global.giftCode.message-default');
        $use = LangManager::translate('shop.views.elements.global.giftCode.footer-default');
        $timeLeft = LangManager::translate('shop.views.elements.global.giftCode.time-default') . ' ' . $formattedEndDate;
        $url = Website::getUrl() . 'shop';

        $htmlTemplate = <<<HTML
              <p>%MESSAGE% <strong>%AMOUNT%</strong></p>
              <div style="text-align: center; font-family: monospace; font-size: 15px; background-color: #f4f4f4; color: #222; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 90%;">
                  %CODE%
              </div>
              <br>
              <p>%USE% <a href="%URL%">%WEBSITENAME%</a>.<br>
              %TIME_LEFT%</p>
            HTML;

        $body = str_replace(['%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',],
            [$message, $amount, $code, $url, $websiteName, $use, $timeLeft], $htmlTemplate);
        $object = $websiteName . " - $globalName " . $amount;
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            ShopNotifyController::getInstance()->notifyUser($user->getMail(), $globalName . $amount, $globalName . $amount, $body);
            Flash::send(Alert::SUCCESS, 'Carte cadeau', $user->getPseudo() . ' à reçu sa carte cadeau par mail !');
        } else {
            Flash::send(Alert::ERROR, 'Carte cadeau','Nous n\'avons pas réussi à envoyer le mail au client ! Mais la carte cadeau à été créer !');
        }
    }

    public function adminGenerateCode($amountGiven, $receiver): void
    {
        $giftCodePrefix = 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = LangManager::translate('shop.views.elements.global.giftCode.title-default');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            $amount = $amountGiven . $symbol;
        } else {
            $amount = $symbol . $amountGiven;
        }

        $timestamp = time();
        $dateTime = date('Y-m-d H:i:s', $timestamp);
        $date = new DateTime($dateTime);
        $date->modify('+1 year');
        $endDateTime = $date->format('Y-m-d H:i:s');

        if (!ShopDiscountModel::getInstance()->createDiscount($globalName . ' ' . $amount, 3, $dateTime, $endDateTime, 1, 0, null, $amountGiven, null, 1, 0, $code, 0, 0, 0)) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de créer la carte cadeau de ' . $amount . '!');
        }

        $formattedEndDate = Date::formatDate($endDateTime);
        $message = LangManager::translate('shop.views.elements.global.giftCode.message-default');
        $use = LangManager::translate('shop.views.elements.global.giftCode.footer-default');
        $timeLeft = LangManager::translate('shop.views.elements.global.giftCode.time-default') . ' ' . $formattedEndDate;
        $url = Website::getUrl() . 'shop';

        $htmlTemplate = <<<HTML
              <p>%MESSAGE% <strong>%AMOUNT%</strong></p>
              <div style="text-align: center; font-family: monospace; font-size: 15px; background-color: #f4f4f4; color: #222; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 90%;">
                  %CODE%
              </div>
              <br>
              <p>%USE% <a href="%URL%">%WEBSITENAME%</a>.<br>
              %TIME_LEFT%</p>
            HTML;

        $body = str_replace(['%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',],
            [$message, $amount, $code, $url, $websiteName, $use, $timeLeft], $htmlTemplate);

        ShopNotifyController::getInstance()->notifyUser($receiver, "Carte cadeau " . $amount, "Votre carte cadeau", $body);
    }

    private function createCode($giftCodePrefix): string
    {
        $dateComponents = getdate();
        $random1 = rand(0, 9);
        $random2 = rand(0, 9);
        $yearLastTwoDigits = $dateComponents['year'] % 100;
        $code = sprintf(
            '%s%d%d%d%d%d%d%d',
            $giftCodePrefix,
            $random1,
            $dateComponents['mday'],
            $dateComponents['mon'],
            $random2,
            $dateComponents['minutes'],
            $dateComponents['hours'],
            $yearLastTwoDigits,
        );
        return $code;
    }
}
