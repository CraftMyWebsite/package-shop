CREATE TABLE IF NOT EXISTS `cmw_shop_config`
(
    `shop_config_name`   VARCHAR(255)                          NOT NULL,
    `shop_config_value`  MEDIUMTEXT                            NOT NULL,
    `shop_config_update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`shop_config_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `cmw_shop_config` (`shop_config_name`, `shop_config_value`)
VALUES ('useBalance', '1'),
       ('moneyName', 'Token'),
       ('isDiscordWebhookEnable', '0'),
       ('discordWebHookData', 'Token');

CREATE TABLE IF NOT EXISTS `cmw_shop_config_mail`
(
    `shop_config_mail_reply`       VARCHAR(255)                          NOT NULL,
    `shop_config_mail_object`      VARCHAR(255)                          NOT NULL,
    `shop_config_mail_content`     VARCHAR(255)                          NOT NULL,
    `shop_config_mail_type`        VARCHAR(255)                          NOT NULL,
    `shop_config_mail_last_update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`shop_config_mail_type`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_shop_config_currencies`
(
    `shop_config_currencies_code`       VARCHAR(10)                           NOT NULL,
    `shop_config_currencies_name`       VARCHAR(50)                           NOT NULL,
    `shop_config_currencies_date_added` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`shop_config_currencies_code`),
    UNIQUE (`shop_config_currencies_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `cmw_shop_config_currencies` (shop_config_currencies_code, shop_config_currencies_name)
VALUES ('EUR', 'Euro'),
       ('USD', 'United States Dollar')