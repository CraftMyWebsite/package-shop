<?php

namespace CMW\Controller\Shop\Admin\Notify;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

/**
 * Class: @ShopNotifyHistoryOrderController
 * @package Shop
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 * @author Zomblard
 * @version 0.0.1
 */
class ShopNotifyController extends AbstractController
{
    public function notifyUser(string $receiver, string $object, string $title, string $messageTransmitted): bool
    {
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            $varName = "mail_notification";
            $useHeader = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_header', $varName) ?? "0";
            $header = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_header', $varName);
            $useBottom = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_bottom', $varName) ?? "0";
            $bottom = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_bottom', $varName);
            $useFooter = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_footer', $varName) ?? "1";
            $footer = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer', $varName) ?? Website::getWebsiteName() . LangManager::translate('shop.views.elements.global.mailNotification.footer-default');
            $bodyColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e';
            $font = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_font', $varName) ?? "'Arial', sans-serif";
            $containerColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_container_color', $varName) ?? '#ffffff';
            $headBGColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_head_color', $varName) ?? '#214e7e';
            $headColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_color', $varName) ?? '#ffffff';
            $textColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_color', $varName) ?? '#000000';
            $footerColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_color', $varName) ?? '#777';

            $useHeaderHtml = ($useHeader === "1") ? '<div class="summary-item-recap"><p>' . $header . '</p></div>' : '';
            $useBottomHtml = ($useBottom === "1") ? '<div class="summary-item-recap"><p>' . $bottom . '</p></div>' : '';
            $useFooterHtml = ($useFooter === "1") ? '<div class="footer-recap"><p>' . $footer . '</p></div>' : '';

            $useLogo = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_logo', $varName) ?? "0";
            $useWebsite = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use_website', $varName) ?? "0";
            $logo = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl() . "App/Package/Shop/Views/Settings/Images/default.png";
            $websiteTitle = Website::getWebsiteName();
            $useLogoOrWebsite = ($useLogo === "1" || $useWebsite === "1") ? '<div class="container-recap" style="display: flex; align-items: center">' : '';
            $useLogoOrWebsiteClose = ($useLogo === "1" || $useWebsite === "1") ? '</div>' : '';
            $useLogoHtml = ($useLogo === "1") ? '<img width="75px" src="' . $logo . '" alt="Logo"">' : '';
            $useWebsiteTitle = ($useWebsite === "1") ? '<h1>' . $websiteTitle . '</h1>' : '';

            $object = Website::getWebsiteName() . ' - ' . $object;

            $htmlTemplate = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                .container-recap {
                    font-family: %FONT%;
                    width: 600px;
                    margin: 20px auto;
                    background: %CONTAINER_COLOR%;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header-recap {
                    background-color: %HEAD_BG%;
                    color: %HEAD_COLOR%;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px;
                }
                .summary-item-recap {
                    border-bottom: 1px solid #eee;
                    color: %TEXT_COLOR%;
                    padding: 10px 0;
                }
                h1 {
                    color: %TEXT_COLOR%;
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
                    color: %FOOTER_COLOR%;
                }
            </style>
            </head>
            <body style="background: %BODY_COLOR%; padding-top: 3rem; padding-bottom: 5rem;">
            %DIV_OPEN_LOGO%
            %USE_LOGO%
            %USE_WEBSITE%
            %DIV_CLOSE_LOGO%
            <div class="container-recap">
                <div class="header-recap">
                    <h2>%TITLE_MAIL%</h2>
                </div>
                <div class="summary-recap">
                    %USE_HEADER%
                    <div class="summary-item-recap">
                        %MESSAGE-TRANSMITTED%
                    </div>
                    %USE_BOTTOM%
                </div>
                %USE_FOOTER%
            </div>
            </body>
            </html>
            HTML;

            $body = str_replace(['%FONT%', '%TITLE_MAIL%', '%MESSAGE-TRANSMITTED%', '%HEAD_BG%', '%HEAD_COLOR%', '%TEXT_COLOR%', '%FOOTER_COLOR%', '%BODY_COLOR%', '%CONTAINER_COLOR%', '%USE_HEADER%', '%USE_BOTTOM%', '%USE_FOOTER%', '%DIV_OPEN_LOGO%', '%DIV_CLOSE_LOGO%', '%USE_LOGO%', '%USE_WEBSITE%'],
                [$font, $title, $messageTransmitted, $headBGColor, $headColor, $textColor, $footerColor, $bodyColor, $containerColor, $useHeaderHtml, $useBottomHtml, $useFooterHtml, $useLogoOrWebsite ,$useLogoOrWebsiteClose,$useLogoHtml, $useWebsiteTitle], $htmlTemplate);

            MailManager::getInstance()->sendMail($receiver, $object, $body);
            return true;
        }
        return false;
    }
}
