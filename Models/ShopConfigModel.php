<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopConfigCurrenciesEntity;
use CMW\Entity\Shop\ShopConfigEntity;
use CMW\Entity\Shop\ShopConfigMail;
use CMW\Manager\Database\DatabaseManager;


/**
 * Class @ShopModel
 * @package shop
 * @author Teyir
 * @version 1.0
 */
class ShopConfigModel extends DatabaseManager
{
    public function getConfig(string $config): mixed
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT shop_config_value FROM cmw_shop_config WHERE shop_config_name = ?');
        $req->execute(array($config));
        $option = $req->fetch();

        return $option['shop_config_value'] ?? "";
    }

    /**
     * @return array|\CMW\Entity\Shop\ShopConfigMail[]
     */
    public function getConfigMails(): array
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT shop_config_mail_type FROM cmw_shop_config_mail');

        $toReturn = [];

        if (!$req->execute()) {
            return $toReturn;
        }

        while ($res = $req->fetch()) {
            $toReturn[] = $this->getConfigMail($res['minecraft_server_id']);
        }
        return $toReturn;
    }

    /**
     * @param string $type
     * @return \CMW\Entity\Shop\ShopConfigMail|null
     */
    public function getConfigMail(string $type): ?ShopConfigMail
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT shop_config_mail_reply, shop_config_mail_object,  shop_config_mail_content,
                                    shop_config_mail_type, shop_config_mail_last_update
                                    FROM cmw_shop_config_mail WHERE shop_config_mail_type = ? LIMIT 1');

        if (!$req->execute(array($type))) {
            return null;
        }
        $res = $req->fetch();

        return new ShopConfigMail(
            $res['shop_config_mail_reply'] ?? null,
            $res['shop_config_mail_object'] ?? null,
            $res['shop_config_mail_content'] ?? null,
            $res['shop_config_mail_type'] ?? null,
            $res['shop_config_mail_last_update'] ?? null,
        );
    }

    /**
     * @return array|\CMW\Entity\Shop\ShopConfigCurrenciesEntity[]
     */
    public function getConfigCurrencies(): array
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT shop_config_currencies_code FROM cmw_shop_config_currencies');

        $toReturn = [];

        if (!$req->execute()) {
            return $toReturn;
        }

        while ($res = $req->fetch()) {
            $toReturn[] = $this->getConfigCurrency($res['shop_config_currencies_code']);
        }
        return $toReturn;
    }

    /**
     * @param string $code
     * @return \CMW\Entity\Shop\ShopConfigCurrenciesEntity|null
     */
    public function getConfigCurrency(string $code): ?ShopConfigCurrenciesEntity
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT shop_config_currencies_code, shop_config_currencies_name, shop_config_currencies_date_added
                                    FROM cmw_shop_config_currencies WHERE shop_config_currencies_code = ? LIMIT 1');

        if (!$req->execute(array($code))) {
            return null;
        }
        $res = $req->fetch();

        return new ShopConfigCurrenciesEntity(
            $res['shop_config_currencies_code'] ?? "",
            $res['shop_config_currencies_name'] ?? "",
            $res['shop_config_currencies_date_added'] ?? ""
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopConfigEntity|null
     */
    public function getConfigs(): ?ShopConfigEntity
    {
        $currencies = $this->getConfigCurrencies();
        $mails = $this->getConfigMails();

        return new ShopConfigEntity(
            $currencies,
            $this->getConfig("isDiscordWebhookEnable") ?? "",
            json_decode($this->getConfig("discordWebHook"), false, 512, JSON_THROW_ON_ERROR) ?? [],
            $mails ?? [],
            $this->getConfig("useBalance") ?? "",
            $this->getConfig("moneyName") ?? "",
        );
    }

}
