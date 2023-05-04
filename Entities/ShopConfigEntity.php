<?php

namespace CMW\Entity\Shop;

class ShopConfigEntity
{
    /* @var \CMW\Entity\Shop\ShopConfigCurrenciesEntity|\CMW\Entity\Shop\ShopConfigCurrenciesEntity[] $currencies */
    private array $currencies;
    private bool $isDiscordWebHookEnable;
    private ?array $discordWebHook;
    /* @var \CMW\Entity\Shop\ShopConfigMail|\CMW\Entity\Shop\ShopConfigMail[] $configMail */
    private array $configMail;
    private bool $useBalance;
    private string $moneyName;

    /**
     * @param \CMW\Entity\Shop\ShopConfigCurrenciesEntity|\CMW\Entity\Shop\ShopConfigCurrenciesEntity[] $currencies
     * @param bool $isDiscordWebHookEnable
     * @param array|null $discordWebHook
     * @param \CMW\Entity\Shop\ShopConfigMail|\CMW\Entity\Shop\ShopConfigMail[] $configMail
     * @param bool $useBalance
     * @param string $moneyName
     */
    public function __construct(array $currencies, bool $isDiscordWebHookEnable, ?array $discordWebHook, array $configMail, bool $useBalance, string $moneyName)
    {
        $this->currencies = $currencies;
        $this->isDiscordWebHookEnable = $isDiscordWebHookEnable;
        $this->discordWebHook = $discordWebHook;
        $this->configMail = $configMail;
        $this->useBalance = $useBalance;
        $this->moneyName = $moneyName;
    }

    /**
     * @return \CMW\Entity\Shop\ShopConfigCurrenciesEntity|\CMW\Entity\Shop\ShopConfigCurrenciesEntity[]
     */
    public function getCurrencies(): ?array
    {
        return $this->currencies;
    }

    /**
     * @return bool
     */
    public function isDiscordWebHookEnable(): bool
    {
        return $this->isDiscordWebHookEnable;
    }

    /**
     * @return array|null
     */
    public function getDiscordWebHook(): ?array
    {
        return $this->discordWebHook;
    }

    /**
     * @return \CMW\Entity\Shop\ShopConfigMail|\CMW\Entity\Shop\ShopConfigMail[]
     */
    public function getMailConfig(): ?array
    {
        return $this->configMail;
    }

    /**
     * @return bool
     */
    public function isUseBalance(): bool
    {
        return $this->useBalance;
    }

    /**
     * @return string
     */
    public function getMoneyName(): string
    {
        return $this->moneyName;
    }
}
