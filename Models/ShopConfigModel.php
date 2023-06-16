<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopConfigCurrenciesEntity;
use CMW\Entity\Shop\ShopConfigEntity;
use CMW\Entity\Shop\ShopConfigMail;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractController;


/**
 * Class @ShopModel
 * @package shop
 * @author Teyir
 * @version 1.0
 */
class ShopConfigModel extends AbstractController
{
    public function getConfig(string $config): mixed
    {
        $sql = "SELECT shop_config_value FROM cmw_shop_config WHERE shop_config_name = ?";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        $res->execute(array($config));
        $option = $res->fetch();

        return $option['shop_config_value'] ?? "";
    }

    /**
     * @return array|\CMW\Entity\Shop\ShopConfigMail[]
     */
    public function getConfigMails(): array
    {
        $sql = "SELECT shop_config_mail_type FROM cmw_shop_config_mail";
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $toReturn = [];

        if (!$res->execute()) {
            return $toReturn;
        }

        while ($minecraft = $res->fetch()) {
            $toReturn[] = $this->getConfigMail($minecraft['minecraft_server_id']);
        }
        return $toReturn;
    }

    /**
     * @param string $type
     * @return \CMW\Entity\Shop\ShopConfigMail|null
     */
    public function getConfigMail(string $type): ?ShopConfigMail
    {
        $sql = "SELECT shop_config_mail_reply, shop_config_mail_object,  shop_config_mail_content,
                                    shop_config_mail_type, shop_config_mail_last_update
                                    FROM cmw_shop_config_mail WHERE shop_config_mail_type = ? LIMIT 1";
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array($type))) {
            return null;
        }
        $shop = $res->fetch();

        return new ShopConfigMail(
            $shop['shop_config_mail_reply'] ?? null,
                $shop['shop_config_mail_object'] ?? null,
                $shop['shop_config_mail_content'] ?? null,
                $shop['shop_config_mail_type'] ?? null,
                $shop['shop_config_mail_last_update'] ?? null,
        );
    }

    /**
     * @return array|\CMW\Entity\Shop\ShopConfigCurrenciesEntity[]
     */
    public function getConfigCurrencies(): array
    {
        $sql = "SELECT shop_config_currencies_code FROM cmw_shop_config_currencies";
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $toReturn = [];

        if (!$res->execute()) {
            return $toReturn;
        }

        while ($currencies = $res->fetch()) {
            $toReturn[] = $this->getConfigCurrency($currencies['shop_config_currencies_code']);
        }
        return $toReturn;
    }

    /**
     * @param string $code
     * @return \CMW\Entity\Shop\ShopConfigCurrenciesEntity|null
     */
    public function getConfigCurrency(string $code): ?ShopConfigCurrenciesEntity
    {
        $sql = "SELECT shop_config_currencies_code, shop_config_currencies_name, shop_config_currencies_date_added
                                    FROM cmw_shop_config_currencies WHERE shop_config_currencies_code = ? LIMIT 1";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array($code))) {
            return null;
        }
        $shop = $res->fetch();

        return new ShopConfigCurrenciesEntity(
            $shop['shop_config_currencies_code'] ?? "",
                $shop['shop_config_currencies_name'] ?? "",
                $shop['shop_config_currencies_date_added'] ?? ""
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopConfigEntity|null
     * @throws \JsonException
     */
    public function getConfigs(): ?ShopConfigEntity
    {
        $currencies = $this->getConfigCurrencies();
        $mails = $this->getConfigMails();

        return new ShopConfigEntity(
            $currencies,
            $this->getConfig("isDiscordWebhookEnable") ?? "",
            json_decode($this->getConfig("discordWebHook"), false, 512, JSON_THROW_ON_ERROR) ?? [],//discordWebHook n'existe pas enDB
            $mails ?? [],
            $this->getConfig("useBalance") ?? "",
            $this->getConfig("moneyName") ?? "",
        );
    }

}
