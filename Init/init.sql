CREATE TABLE IF NOT EXISTS cmw_shops_settings
(
    shop_settings_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_settings_key     VARCHAR(50) NOT NULL,
    shop_settings_value   VARCHAR(50) NOT NULL,
    shop_settings_updated TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_shopping_session
(
    shop_shopping_session_id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id                           INT NOT NULL,
    shop_shopping_session_start_date  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_shopping_session_end_date    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_shopping_session FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_categories
(
    shop_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_category_name        VARCHAR(50) NOT NULL,
    shop_category_description TEXT NULL,
    shop_sub_category_id      INT NULL,
    shop_category_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_category_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_categories FOREIGN KEY (shop_sub_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items
(
    shop_item_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_category_id        INT NOT NULL,
    shop_item_name VARCHAR(50) NULL,
    shop_item_description LONGTEXT     NOT NULL,
    shop_item_type TINYINT NOT NULL,
    shop_item_default_stock INT NULL,
    shop_item_current_stock INT NULL,
    shop_item_price FLOAT(10,2) NULL,
    shop_item_global_limit INT NULL,
    shop_item_user_limit INT NULL,
    shop_item_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_item_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_categories FOREIGN KEY (shop_sub_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_images
(
    shop_image_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_image_name        VARCHAR(50) NOT NULL,
    shop_category_id       INT NULL,
    shop_item_id           INT NULL,
    shop_image_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_image_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_images FOREIGN KEY (shop_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE
    CONSTRAINT fk_shop_item_id_images FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;