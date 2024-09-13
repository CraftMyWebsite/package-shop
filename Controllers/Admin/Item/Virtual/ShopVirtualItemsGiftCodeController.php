<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MailController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
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
     * @throws \Exception
     */
    public function sedMailWithGiftCode(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        $giftCodePrefix = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_prefix') ?? 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_global') ?? 'Carte cadeau';
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
            MailController::getInstance()->sendMail($user->getMail(), $websiteName . " - $globalName " . $amount, "Nous n'avons pas réussi à créer votre bon cadeau de" . $amount . ". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = CoreController::formatDate($endDateTime);
        $titre = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Félicitations !';
        $message = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous avez reçu une carte cadeau d'une valeur de";
        $use = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_use_mail') ?? 'Utilisez ou partager ce code lors de votre prochain achat sur';
        $timeLeft = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_time_mail') ?? "Ce code est valable jusqu'au" . ' ' . $formattedEndDate;
        $url = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_url_mail') ?? Website::getUrl() . 'shop';

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
            <body style="background-color: %MAINBG%">

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

        $cardBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa';
        $titleColor = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f';
        $textColor = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565';
        $codeText = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff';
        $codeBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef';
        $mainBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff';

        $body = str_replace(['%TITRE%', '%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $amount, $code, $url, $websiteName, $use, $timeLeft, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);
        $object = $websiteName . " - $globalName " . $amount;
        MailController::getInstance()->sendMail($user->getMail(), $object, $body);
    }

    public function adminGenerateCode($amountGiven): void
    {
        $user = UsersModel::getCurrentUser();
        $varName = 'gift_code';
        $giftCodePrefix = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_prefix') ?? 'GC_';
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();
        $globalName = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_global') ?? 'Carte cadeau';
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
            MailController::getInstance()->sendMail($user->getMail(), $websiteName . " - $globalName " . $amount, "Nous n'avons pas réussi à créer votre bon cadeau de" . $amount . ". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = CoreController::formatDate($endDateTime);
        $titre = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Félicitations !';
        $message = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous avez reçu une carte cadeau d'une valeur de";
        $use = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_use_mail') ?? 'Utilisez ou partager ce code lors de votre prochain achat sur';
        $timeLeft = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_time_mail') ?? "Ce code est valable jusqu'au" . ' ' . $formattedEndDate;
        $url = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_url_mail') ?? Website::getUrl() . 'shop';

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
            <body style="background-color: %MAINBG%">

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

        $cardBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa';
        $titleColor = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f';
        $textColor = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565';
        $codeText = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff';
        $codeBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef';
        $mainBG = ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff';

        $body = str_replace(['%TITRE%', '%MESSAGE%', '%AMOUNT%', '%CODE%', '%URL%', '%WEBSITENAME%', '%USE%', '%TIME_LEFT%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $amount, $code, $url, $websiteName, $use, $timeLeft, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);
        $object = $websiteName . " - $globalName " . $amount;
        MailController::getInstance()->sendMail($user->getMail(), $object, $body);
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
