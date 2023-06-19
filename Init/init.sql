CREATE TABLE IF NOT EXISTS cmw_shops_settings
(
    shop_settings_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_settings_key     VARCHAR(50) NOT NULL,
    shop_settings_value   VARCHAR(50) NOT NULL,
    shop_settings_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_shopping_session
(
    shop_shopping_session_id       INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id                   INT NULL,
    shop_shopping_session_start_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_shopping_session_end_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_shopping_session FOREIGN KEY (shop_user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_categories
(
    shop_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_category_name        VARCHAR(50) NOT NULL,
    shop_category_description TEXT NULL,
    shop_image_id             INT NULL,
    shop_sub_category_id      INT NULL,
    shop_category_created_at     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_category_updated_at     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_categories FOREIGN KEY (shop_sub_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items
(
    shop_item_id            INT AUTO_INCREMENT PRIMARY KEY,
    shop_category_id        INT NULL,
    shop_item_name          VARCHAR(50) NULL,
    shop_item_description   LONGTEXT NOT NULL,
    shop_image_id           INT NULL,
    shop_item_type          TINYINT NOT NULL,
    shop_item_default_stock INT NULL,
    shop_item_current_stock INT NULL,
    shop_item_price         FLOAT(10,2) NULL,
    shop_item_global_limit  INT NULL,
    shop_item_user_limit    INT NULL,
    shop_item_created_at       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_item_updated_at       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_items FOREIGN KEY (shop_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_images
(
    shop_image_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_image_name    VARCHAR(50) NOT NULL,
    shop_category_id   INT NULL,
    shop_item_id       INT NULL,
    shop_image_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_image_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_images FOREIGN KEY (shop_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_item_id_images FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_cart_items
(
    shop_cart_item_id        INT AUTO_INCREMENT PRIMARY KEY,
    shop_shopping_session_id INT NULL,
    shop_item_id             INT NULL,
    shop_cart_item_quantity  INT NOT NULL DEFAULT 1,
    shop_cart_item_created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_cart_item_updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_cart_items FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_shopping_session_id_cart_items FOREIGN KEY (shop_shopping_session_id) REFERENCES cmw_shops_shopping_session (shop_shopping_session_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items_requirement
(
    shop_item_requirement_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id                  INT NOT NULL,
    required_shop_item_id         INT NOT NULL,
    shop_item_requirement_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_item_requirement_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_requirement FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_required_shop_item_id_items_requirement FOREIGN KEY (required_shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items_actions
(
    shop_item_action_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id             INT NOT NULL,
    shop_item_action         LONGTEXT NOT NULL,
    shop_item_action_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_actions FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items_tags
(
    shop_item_tag_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_name   VARCHAR(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_items_tags_items
(
    shop_item_tag_item_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_tag_id      INT NOT NULL,
    shop_item_id          INT NOT NULL,
    CONSTRAINT fk_shop_item_tag_id_items_tags_items FOREIGN KEY (shop_item_tag_id) REFERENCES cmw_shops_items_tags (shop_item_tag_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_item_id_items_tags_items FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_payment_history
(
    shop_payment_history_id     INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id                     INT NULL,
    shop_payment_history_type   VARCHAR(25) NOT NULL,
    shop_payment_history_status INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_user_id_payment_history FOREIGN KEY (shop_user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_payment_discount
(
    shop_payment_discount_id                             INT AUTO_INCREMENT PRIMARY KEY,
    shop_payment_discount_name                           VARCHAR(50) NOT NULL,
    shop_payment_discount_description                    VARCHAR(50) NOT NULL,
    shop_payment_discount_start_date                     TIMESTAMP   NOT NULL,
    shop_payment_discount_end_date                       TIMESTAMP NULL,
    shop_payment_discount_default_uses                   INT NULL,
    shop_payment_discount_uses_left                      INT NULL,
    shop_payment_discount_percent                        INT NULL,
    shop_payment_discount_price                          FLOAT(10,2) NULL,
    shop_payment_discount_use_multiple_per_users         TINYINT NULL,
    shop_payment_discount_cumulative                     TINYINT NULL,
    shop_payment_discount_status                         TINYINT NULL,
    shop_item_id                                         INT NULL,
    shop_category_id                                     INT NULL,
    shop_payment_discount_code                           INT NULL,
    shop_payment_discount_default_active                 INT NOT NULL DEFAULT 0,
    shop_payment_discount_users_need_purchase_before_use INT NULL,
    shop_payment_discount_created_at                        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_payment_discount_updated_at                        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_payment_discount FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_category_id_payment_discount FOREIGN KEY (shop_category_id) REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_orders
(
    shop_order_id            INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id INT NULL,
    shop_order_created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_order_updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_orders FOREIGN KEY (shop_user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_orders_items
(
    shop_order_item_id            INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id INT NULL,
    shop_item_name VARCHAR(50) NULL,
    shop_order_id INT NULL,
    shop_payment_discount_id INT NULL,
    shop_order_item_quantity INT NULL,
    shop_order_item_status INT NOT NULL DEFAULT 1,
    shop_order_item_price FLOAT(10,2) NULL,
    shop_order_item_created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_order_item_updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_item_id_orders_items FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_order_id_orders_items FOREIGN KEY (shop_order_id) REFERENCES cmw_shops_orders (shop_order_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_payment_discount_id_orders_items FOREIGN KEY (shop_payment_discount_id) REFERENCES cmw_shops_payment_discount (shop_payment_discount_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_delivery
(
    shop_delivery_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id          INT NULL,
    shop_item_id          INT NULL,
    shop_delivery_first_name  VARCHAR(50) NULL,
    shop_delivery_last_name   VARCHAR(50) NULL,
    shop_order_id         INT NULL,
    shop_delivery_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_delivery_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_delivery FOREIGN KEY (shop_user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_item_id_delivery FOREIGN KEY (shop_item_id) REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_order_id_delivery FOREIGN KEY (shop_order_id) REFERENCES cmw_shops_orders (shop_order_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_shops_delivery_user_address
(
    shop_delivery_user_address_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_delivery_user_address_label       VARCHAR(50) NOT NULL,
    shop_user_id                           INT NULL,
    shop_delivery_user_address_first_name  VARCHAR(50) NOT NULL,
    shop_delivery_user_address_last_name   VARCHAR(50) NOT NULL,
    shop_delivery_user_address_line_1      VARCHAR(50) NOT NULL,
    shop_delivery_user_address_line_2      VARCHAR(50) NOT NULL,
    shop_delivery_user_address_city        VARCHAR(50) NOT NULL,
    shop_delivery_user_address_postal_code VARCHAR(50) NOT NULL,
    shop_delivery_user_address_country     VARCHAR(50) NOT NULL,
    shop_delivery_user_address_phone       VARCHAR(50) NOT NULL,
    shop_delivery_user_address_created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_delivery_user_address_updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_delivery_user_address FOREIGN KEY (shop_user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

ALTER TABLE `cmw_shops_items` ADD
    CONSTRAINT fk_shop_image_id_items FOREIGN KEY (shop_image_id) REFERENCES cmw_shops_images (`shop_image_id`) ON DELETE SET NULL;
ALTER TABLE `cmw_shops_categories` ADD
    CONSTRAINT fk_shop_image_id_categories FOREIGN KEY (shop_image_id) REFERENCES cmw_shops_images (`shop_image_id`) ON DELETE SET NULL;