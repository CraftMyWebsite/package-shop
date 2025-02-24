<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

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
 * @version 1.0
 */
class ShopVirtualItemsGiftCodeController extends AbstractController
{
    /**
     * @param ShopItemEntity $item
     * @param UserEntity $user
     */
    public function sedMailWithGiftCode(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        $giftCodePrefix = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_prefix', $varName) ?? 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.title-default');
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
            MailManager::getInstance()->sendMail($user->getMail(), $websiteName . " - $globalName " . $amount, "Nous n'avons pas réussi à créer votre bon cadeau de" . $amount . ". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = Date::formatDate($endDateTime);
        $titre = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.mail-default');
        $message = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.message-default');
        $use = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.footer-default');
        $timeLeft = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_time_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.time-default') . ' ' . $formattedEndDate;
        $url = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_url_mail', $varName) ?? Website::getUrl() . 'shop';

        $htmlTemplate = <<<HTML
            <html>
            <head>
            <style>
              .gift-card {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: %CARDBG%;
                border: 1px solid #ddd;
                border-radius: 10px;
                text-align: center;
              }

              .gift-card h1 {
                color: %TITLECOLOR%;
              }

              .gift-card p {
                color: %TEXTCOLOR%;
              }

              .code {
                font-size: 18px;
                color: %CODETEXT%;
                margin: 20px 0;
                padding: 10px;
                background-color: %CODEBG%;
                border-radius: 5px;
                display: inline-block;
              }
            </style>
            </head>
            <body style="background-color: %MAINBG%; padding-top: 3rem; padding-bottom: 3rem;">

            <div class="gift-card">
              <h1>%TITRE%</h1>
              <p>%MESSAGE% <strong>%AMOUNT%</strong></p>
              <div class="code">%CODE%</div><br>
              <p>%USE% <a href="%URL%">%WEBSITENAME%</a>.<br>
              %TIME_LEFT%</p>
            </div>
            </body>
            </html>
            HTML;

        $cardBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color', $varName) ?? '#f8f9fa';
        $titleColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title', $varName) ?? '#2f2f2f';
        $textColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p', $varName) ?? '#656565';
        $codeText = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color', $varName) ?? '#007bff';
        $codeBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color', $varName) ?? '#e9ecef';
        $mainBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e';

        $body = str_replace(['%TITRE%', '%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $amount, $code, $url, $websiteName, $use, $timeLeft, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);
        $object = $websiteName . " - $globalName " . $amount;
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
            Flash::send(Alert::SUCCESS, 'Carte cadeau', $user->getPseudo() . ' à reçu sa carte cadeau par mail !');
        } else {
            Flash::send(Alert::ERROR, 'Carte cadeau','Nous n\'avons pas réussi à envoyer le mail au client ! Mais la carte cadeau à été créer !');
        }
    }

    public function adminGenerateCode($amountGiven): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();
        $varName = 'gift_code';
        $giftCodePrefix = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_prefix', $varName) ?? 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.title-default');
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
            MailManager::getInstance()->sendMail($user->getMail(), $websiteName . " - $globalName " . $amount, "Nous n'avons pas réussi à créer votre bon cadeau de" . $amount . ". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = Date::formatDate($endDateTime);
        $titre = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.mail-default');
        $message = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.message-default');
        $use = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.footer-default');
        $timeLeft = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_time_mail', $varName) ?? LangManager::translate('shop.views.elements.global.giftCode.time-default') . ' ' . $formattedEndDate;
        $url = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_url_mail', $varName) ?? Website::getUrl() . 'shop';

        $htmlTemplate = <<<HTML
            <html>
            <head>
            <style>
              .gift-card {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: %CARDBG%;
                border: 1px solid #ddd;
                border-radius: 10px;
                text-align: center;
              }

              .gift-card h1 {
                color: %TITLECOLOR%;
              }

              .gift-card p {
                color: %TEXTCOLOR%;
              }

              .code {
                font-size: 18px;
                color: %CODETEXT%;
                margin: 20px 0;
                padding: 10px;
                background-color: %CODEBG%;
                border-radius: 5px;
                display: inline-block;
              }
            </style>
            </head>
            <body style="background-color: %MAINBG%; padding-top: 3rem; padding-bottom: 3rem;">

            <div class="gift-card">
              <h1>%TITRE%</h1>
              <p>%MESSAGE% <strong>%AMOUNT%</strong></p>
              <div class="code">%CODE%</div><br>
              <p>%USE% <a href="%URL%">%WEBSITENAME%</a>.<br>
              %TIME_LEFT%</p>
            </div>
            </body>
            </html>
            HTML;

        $cardBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color', $varName) ?? '#f8f9fa';
        $titleColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title', $varName) ?? '#2f2f2f';
        $textColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p', $varName) ?? '#656565';
        $codeText = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color', $varName) ?? '#007bff';
        $codeBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color', $varName) ?? '#e9ecef';
        $mainBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e';

        $body = str_replace(['%TITRE%', '%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $amount, $code, $url, $websiteName, $use, $timeLeft, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);
        $object = $websiteName . " - $globalName " . $amount;
        MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
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
