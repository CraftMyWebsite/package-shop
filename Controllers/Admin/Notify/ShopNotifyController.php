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

            $object = Website::getWebsiteName() . ' - ' . $object;

            $htmlTemplate = <<<HTML
            <h2>%TITLE_MAIL%</h2>
            <div>
                %MESSAGE-TRANSMITTED%
            </div>
            HTML;

            $body = str_replace(['%TITLE_MAIL%', '%MESSAGE-TRANSMITTED%'],
                [$title, $messageTransmitted], $htmlTemplate);

            MailManager::getInstance()->sendMail($receiver, $object, $body);
            return true;
        }
        return false;
    }
}
