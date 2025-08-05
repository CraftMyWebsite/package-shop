CREATE TABLE IF NOT EXISTS cmw_shops_settings
(
    shop_settings_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_settings_key        VARCHAR(50) NOT NULL,
    shop_settings_value      VARCHAR(50) NOT NULL,
    shop_settings_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_global_implement_settings
(
    shop_global_implement_settings_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_global_implement_settings_key        VARCHAR(255) NOT NULL UNIQUE,
    shop_global_implement_settings_value      VARCHAR(255) NOT NULL,
    shop_global_implement_settings_varName    VARCHAR(255) NOT NULL,
    shop_global_implement_settings_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_shopping_session
(
    shop_shopping_session_id       INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id                   INT       NULL,
    shop_shopping_session_start_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_shopping_session_end_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_shopping_session FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_categories
(
    shop_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_sub_category_id      INT          NULL,
    shop_category_name        VARCHAR(50)  NOT NULL,
    shop_category_icon        VARCHAR(50)  NULL,
    shop_category_description TEXT         NULL,
    shop_category_slug        VARCHAR(255) NOT NULL,
    shop_category_created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_category_updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shops_categories_id FOREIGN KEY (shop_sub_category_id)
        REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items
(
    shop_item_id                INT AUTO_INCREMENT PRIMARY KEY,
    shop_category_id            INT          NULL,
    shop_item_name              VARCHAR(50)  NULL,
    shop_item_description       LONGTEXT     NOT NULL,
    shop_item_short_description TEXT         NOT NULL,
    shop_item_slug              VARCHAR(255) NOT NULL,
    shop_image_id               INT          NULL,
    shop_item_type              TINYINT      NOT NULL,
    shop_item_default_stock     INT          NULL,
    shop_item_current_stock     INT          NULL,
    shop_item_price             FLOAT(10, 2) NULL,
    shop_item_price_type        VARCHAR(50)  NOT NULL,
    shop_item_by_order_limit    INT          NULL,
    shop_item_global_limit      INT          NULL,
    shop_item_user_limit        INT          NULL,
    shop_item_archived          TINYINT      NOT NULL DEFAULT 0,
    shop_item_archived_reason   TINYINT      NOT NULL DEFAULT 0,
    shop_item_draft             TINYINT      NOT NULL DEFAULT 0,
    shop_item_created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_item_updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_items FOREIGN KEY (shop_category_id)
        REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_images
(
    shop_image_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_image_name       VARCHAR(50) NOT NULL,
    shop_category_id      INT         NULL,
    shop_item_id          INT         NULL,
    shop_default_image    INT         NOT NULL DEFAULT 0,
    shop_image_order      INT         NOT NULL DEFAULT 0,
    shop_image_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_image_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_category_id_images FOREIGN KEY (shop_category_id)
        REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_item_id_images FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_cart
(
    shop_cart_id           INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id           INT          NULL,
    shop_client_session_id VARCHAR(255) NULL,
    shop_created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_cart FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_cart_items
(
    shop_cart_item_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_cart_id              INT       NOT NULL,
    shop_item_id              INT       NULL,
    shop_discount_id          INT       NULL,
    shop_cart_item_quantity   INT       NOT NULL DEFAULT 1,
    shop_cart_item_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_cart_item_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    shop_cart_item_aside      TINYINT   NOT NULL DEFAULT 0,
    CONSTRAINT fk_cart_id_cart_items FOREIGN KEY (shop_cart_id)
        REFERENCES cmw_shops_cart (shop_cart_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_item_id_cart_items FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_requirement
(
    shop_item_requirement_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id                     INT       NOT NULL,
    required_shop_item_id            INT       NOT NULL,
    shop_item_requirement_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_item_requirement_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_requirement FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_required_shop_item_id_items_requirement FOREIGN KEY (required_shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_physical_requirement
(
    shop_item_physical_requirement_id    INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id                         INT          NOT NULL,
    shop_physical_requirement_weight     FLOAT(10, 2) NULL,
    shop_physical_requirement_length     FLOAT(10, 2) NULL,
    shop_physical_requirement_width      FLOAT(10, 2) NULL,
    shop_physical_requirement_height     FLOAT(10, 2) NULL,
    shop_physical_requirement_created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_physical_requirement_updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_physical_requirement FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_variants
(
    shop_variants_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id             INT         NOT NULL,
    shop_variants_name       VARCHAR(50) NOT NULL,
    shop_variants_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_variants_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_variants FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_variants_values
(
    shop_variants_values_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_variants_id                INT         NOT NULL,
    shop_variants_value             VARCHAR(50) NOT NULL,
    shop_variants_value_image       VARCHAR(255) DEFAULT NULL,
    shop_variants_values_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_variants_values_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_variants_values FOREIGN KEY (shop_variants_id)
        REFERENCES cmw_shops_items_variants (shop_variants_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_cart_items_variantes
(
    shop_cart_items_variantes_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_cart_item_id                    INT       NOT NULL,
    shop_variants_values_id              INT       NOT NULL,
    shop_cart_items_variantes_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_cart_items_variantes_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_cart_items_variantes FOREIGN KEY (shop_cart_item_id)
        REFERENCES cmw_shops_cart_items (shop_cart_item_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_variante_cart_items_variantes FOREIGN KEY (shop_variants_values_id)
        REFERENCES cmw_shops_items_variants_values (shop_variants_values_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_actions
(
    shop_item_action_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_id                INT       NOT NULL,
    shop_item_action            LONGTEXT  NOT NULL,
    shop_item_action_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_actions FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_tags
(
    shop_item_tag_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_name   VARCHAR(50) NOT NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_tags_items
(
    shop_item_tag_item_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_item_tag_id      INT NOT NULL,
    shop_item_id          INT NOT NULL,
    CONSTRAINT fk_shop_item_tag_id_items_tags_items FOREIGN KEY (shop_item_tag_id)
        REFERENCES cmw_shops_items_tags (shop_item_tag_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_item_id_items_tags_items FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_payment_history
(
    shop_payment_history_id     INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id                INT         NULL,
    shop_payment_history_type   VARCHAR(25) NOT NULL,
    shop_payment_history_status INT         NOT NULL DEFAULT 0,
    CONSTRAINT fk_user_id_payment_history FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_discount
(
    shop_discount_id                             INT AUTO_INCREMENT PRIMARY KEY,
    shop_discount_name                           VARCHAR(50)  NOT NULL,
    shop_discount_linked                         TINYINT      NOT NULL DEFAULT 0,
    shop_discount_start_date                     TIMESTAMP    NOT NULL,
    shop_discount_end_date                       TIMESTAMP    NULL,
    shop_discount_max_uses                       INT          NULL,
    shop_discount_current_uses                   INT          NULL,
    shop_discount_percent                        INT          NULL,
    shop_discount_price                          FLOAT(10, 2) NULL,
    shop_discount_use_multiple_per_users         TINYINT      NULL,
    shop_discount_status                         TINYINT      NULL,
    shop_discount_test                           TINYINT      NULL,
    shop_discount_code                           VARCHAR(500)  NULL UNIQUE,
    shop_discount_default_active                 TINYINT      NOT NULL DEFAULT 0,
    shop_discount_users_need_purchase_before_use TINYINT      NULL,
    shop_discount_quantity_impacted              TINYINT      NULL,
    shop_discount_created_at                     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_discount_updated_at                     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_discount_items
(
    shop_discount_items_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_discount_id       INT NULL,
    shop_item_id           INT NULL,
    CONSTRAINT fk_shop_item_id_discount_items FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_discount_id_discount_items FOREIGN KEY (shop_discount_id)
        REFERENCES cmw_shops_discount (shop_discount_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_discount_categories
(
    shop_discount_categories_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_discount_id            INT NULL,
    shop_category_id            INT NULL,
    CONSTRAINT fk_shop_item_id_discount_categories FOREIGN KEY (shop_category_id)
        REFERENCES cmw_shops_categories (shop_category_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_discount_id_discount_categories FOREIGN KEY (shop_discount_id)
        REFERENCES cmw_shops_discount (shop_discount_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_cart_discounts
(
    shop_cart_discount_id INT AUTO_INCREMENT PRIMARY KEY,
    shop_cart_id          INT NULL,
    shop_discount_id      INT NULL,
    CONSTRAINT fk_shop_discount_id_cart_discounts FOREIGN KEY (shop_discount_id)
        REFERENCES cmw_shops_discount (shop_discount_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cart_id_cart_discounts FOREIGN KEY (shop_cart_id)
        REFERENCES cmw_shops_cart (shop_cart_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_shipping_zone
(
    shops_shipping_zone_id      INT AUTO_INCREMENT PRIMARY KEY,
    shops_shipping_zone_name    VARCHAR(50) NOT NULL,
    shops_shipping_zone_country VARCHAR(5)  NOT NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_shipping_withdraw_point
(
    shops_shipping_withdraw_point_id                  INT AUTO_INCREMENT PRIMARY KEY,
    shops_shipping_withdraw_point_name                VARCHAR(50) NOT NULL,
    shops_shipping_withdraw_point_address_distance    INT         NULL,
    shops_shipping_withdraw_point_address_line        VARCHAR(500) NOT NULL,
    shops_shipping_withdraw_point_address_city        VARCHAR(500) NOT NULL,
    shops_shipping_withdraw_point_address_postal_code VARCHAR(500) NOT NULL,
    shops_shipping_withdraw_point_address_latitude    VARCHAR(500) NOT NULL,
    shops_shipping_withdraw_point_address_longitude   VARCHAR(500) NOT NULL,
    shops_shipping_withdraw_point_address_country     VARCHAR(50) NOT NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_shipping
(
    shops_shipping_id                   INT AUTO_INCREMENT PRIMARY KEY,
    shops_shipping_name                 VARCHAR(50)  NOT NULL,
    shops_shipping_price                FLOAT(10, 2) NULL,
    shops_shipping_zone_id              INT          NOT NULL,
    shops_shipping_type                 INT          NOT NULL,
    shops_shipping_always_displayed     INT          NULL,
    shops_shipping_withdraw_point_id    INT          NULL,
    shops_shipping_method_var_name      VARCHAR(50)  NULL,
    shops_shipping_max_total_weight     INT          NULL,
    shops_shipping_min_total_cart_price FLOAT(10, 2) NULL,
    shops_shipping_max_total_cart_price FLOAT(10, 2) NULL,
    CONSTRAINT fk_shops_shipping_zone_id_cmw_shops_shipping FOREIGN KEY (shops_shipping_zone_id)
        REFERENCES cmw_shops_shipping_zone (shops_shipping_zone_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shops_shipping_withdraw_point_id_cmw_shops_shipping FOREIGN KEY (shops_shipping_withdraw_point_id)
        REFERENCES cmw_shops_shipping_withdraw_point (shops_shipping_withdraw_point_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_delivery_user_address
(
    shop_delivery_user_address_id          INT AUTO_INCREMENT PRIMARY KEY,
    shop_delivery_is_fav                   INT         NOT NULL DEFAULT 0,
    shop_delivery_user_address_label       VARCHAR(50) NOT NULL,
    shop_user_id                           INT         NULL,
    shop_delivery_user_address_first_name  VARCHAR(50) NOT NULL,
    shop_delivery_user_address_last_name   VARCHAR(50) NOT NULL,
    shop_delivery_user_address_line_1      VARCHAR(500) NOT NULL,
    shop_delivery_user_address_line_2      VARCHAR(500) NOT NULL,
    shop_delivery_user_address_city        VARCHAR(500) NOT NULL,
    shop_delivery_user_address_postal_code VARCHAR(500) NOT NULL,
    shop_delivery_user_address_country     VARCHAR(50) NOT NULL,
    shop_delivery_user_address_phone       VARCHAR(500) NOT NULL,
    shop_delivery_user_address_latitude    VARCHAR(500) NOT NULL,
    shop_delivery_user_address_longitude   VARCHAR(500) NOT NULL,
    shop_delivery_user_address_created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_delivery_user_address_updated_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_delivery_user_address FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS cmw_shop_history_order
(
    shop_history_order_id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id                          INT          NULL,
    shop_history_order_status        INT          NOT NULL DEFAULT 0,
    shop_history_order_shipping_link VARCHAR(255) NULL,
    shop_history_order_number        VARCHAR(50)  NULL,
    shop_history_order_created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_history_order_updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_shop_history_order FOREIGN KEY (user_id)
    REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS cmw_shop_history_order_discount
(
    shop_history_order_discount_id      INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_id               INT          NOT NULL,
    shop_history_order_discount_name    VARCHAR(255) NULL,
    shop_history_order_discount_price   FLOAT(10, 2) NULL,
    shop_history_order_discount_percent INT          NULL,
    CONSTRAINT fk_cmw_shop_history_order_discount_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_shipping
(
    shop_history_order_shipping_id    INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_id             INT          NOT NULL,
    shops_shipping_id                 INT          NULL,
    shop_history_order_shipping_name  VARCHAR(255) NULL,
    shop_history_order_shipping_price FLOAT(10, 2) NULL,
    CONSTRAINT fk_cmw_shop_history_order_shipping_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cmw_shop_history_order_shipping_shops_shipping_id FOREIGN KEY (shops_shipping_id)
    REFERENCES cmw_shops_shipping (shops_shipping_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_user_address
(
    shop_history_order_user_address_id               INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_id                            INT         NOT NULL,
    shop_history_order_user_address_name             VARCHAR(50) NULL,
    shop_history_order_user_address_user_mail        VARCHAR(500) NULL,
    shop_history_order_user_address_user_last_name   VARCHAR(50) NULL,
    shop_history_order_user_address_user_first_name  VARCHAR(50) NULL,
    shop_history_order_user_address_user_line_1      VARCHAR(500) NULL,
    shop_history_order_user_address_user_line_2      VARCHAR(500) NULL,
    shop_history_order_user_address_user_city        VARCHAR(500) NULL,
    shop_history_order_user_address_user_postal_code VARCHAR(500) NULL,
    shop_history_order_user_address_user_country     VARCHAR(50) NULL,
    shop_history_order_user_address_user_phone       VARCHAR(500) NULL,
    CONSTRAINT fk_cmw_shop_history_order_user_address_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_payment
(
    shop_history_order_payment_id       INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_id               INT          NOT NULL,
    shop_history_order_payment_name     VARCHAR(255) NULL,
    shop_history_order_payment_var_name VARCHAR(50)  NULL,
    shop_history_order_payment_fee      FLOAT(10, 2) NULL,
    CONSTRAINT fk_cmw_shop_history_order_payment_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_items
(
    shop_history_order_items_id                          INT AUTO_INCREMENT PRIMARY KEY,
    item_id                                              INT          NOT NULL,
    shop_history_order_id                                INT          NOT NULL,
    shop_history_order_items_name                        VARCHAR(255) NULL,
    shop_history_order_items_img                         VARCHAR(255) NULL,
    shop_history_order_items_quantity                    INT          NULL,
    shop_history_order_items_price                       FLOAT(10, 2) NULL,
    shop_history_order_items_discount_name               VARCHAR(255) NULL,
    shop_history_order_items_total_price_before_discount FLOAT(10, 2) NULL,
    shop_history_order_items_total_price_after_discount  FLOAT(10, 2) NULL,
    CONSTRAINT fk_cmw_shop_history_order_items_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_items_variantes
(
    shop_history_order_items_variantes_id    INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_items_id              INT          NOT NULL,
    shop_history_order_items_variantes_name  VARCHAR(255) NULL,
    shop_history_order_items_variantes_value VARCHAR(255) NULL,
    CONSTRAINT fk_cmw_shop_history_order_items_variantes_history_order_items_id FOREIGN KEY (shop_history_order_items_id)
        REFERENCES cmw_shop_history_order_items (shop_history_order_items_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_afterSales
(
    shop_history_order_afterSales_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_afterSales_author     INT       NOT NULL,
    shop_history_order_afterSales_reason     INT       NOT NULL DEFAULT 0,
    shop_history_order_afterSales_status     INT       NOT NULL DEFAULT 0,
    shop_history_order_id                    INT       NOT NULL,
    shop_history_order_afterSales_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_history_order_afterSales_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cmw_shop_history_order_afterSales_history_order_id FOREIGN KEY (shop_history_order_id)
        REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_user_id_history_order_afterSales_user FOREIGN KEY (shop_history_order_afterSales_author)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_afterSales_message
(
    shop_history_order_afterSales_message_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_afterSales_id                 INT       NOT NULL,
    shop_history_order_afterSales_message            TEXT      NOT NULL,
    shop_history_order_afterSales_message_author     INT       NOT NULL,
    shop_history_order_afterSales_message_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_history_order_afterSales_message_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cmw_shop_history_order_afterSales_message_history_order_id FOREIGN KEY (shop_history_order_afterSales_id)
        REFERENCES cmw_shop_history_order_afterSales (shop_history_order_afterSales_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_user_id_history_order_afterSales_message_user FOREIGN KEY (shop_history_order_afterSales_message_author)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shop_history_order_invoice
(
    shop_history_order_invoice_id           INT AUTO_INCREMENT PRIMARY KEY,
    shop_history_order_id                   INT          NOT NULL UNIQUE,
    shop_history_order_invoice_link         VARCHAR(255) NULL,
    shop_history_order_invoice_created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cmw_shop_history_order_invoice_history_order_id FOREIGN KEY (shop_history_order_id)
    REFERENCES cmw_shop_history_order (shop_history_order_id) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_command_tunnel
(
    shop_command_tunnel_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_command_tunnel_step       INT         NOT NULL DEFAULT 0,
    shop_user_id                   INT         NULL UNIQUE,
    shops_shipping_id              INT         NULL,
    shop_delivery_user_address_id  INT         NULL,
    shop_payment_method_name       VARCHAR(50) NULL,
    shop_command_tunnel_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_command_tunnel_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_command_tunnel FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_command_tunnel_id_delivery_user_address FOREIGN KEY (shop_delivery_user_address_id)
        REFERENCES cmw_shops_delivery_user_address (shop_delivery_user_address_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_shop_command_tunnel_shipping_id FOREIGN KEY (shops_shipping_id)
        REFERENCES cmw_shops_shipping (shops_shipping_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_payment_method_settings
(
    shop_payment_method_settings_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_payment_method_settings_key        VARCHAR(50)  NOT NULL UNIQUE KEY,
    shop_payment_method_settings_value      VARCHAR(500) NOT NULL,
    shop_payment_method_settings_created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shop_payment_method_settings_updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_virtual_method
(
    shops_items_virtual_method_id              INT AUTO_INCREMENT PRIMARY KEY,
    shops_items_virtual_method_var_name        VARCHAR(50) NOT NULL,
    shop_item_id                               INT         NULL,
    shops_items_virtual_requirement_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shops_items_virtual_requirement_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_virtual_method FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_items_virtual_requirement
(
    shops_items_virtual_requirement_id         INT AUTO_INCREMENT PRIMARY KEY,
    shops_items_virtual_method_id              INT          NULL,
    shops_items_virtual_requirement_key        VARCHAR(50)  NOT NULL UNIQUE KEY,
    shops_items_virtual_requirement_value      VARCHAR(255) NOT NULL,
    shops_items_virtual_requirement_created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shops_items_virtual_requirement_updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_item_id_items_virtual_requirement FOREIGN KEY (shops_items_virtual_method_id)
        REFERENCES cmw_shops_items_virtual_method (shops_items_virtual_method_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_shipping_method_requirement
(
    shops_shipping_method_requirement_id         INT AUTO_INCREMENT PRIMARY KEY,
    shops_shipping_method_requirement_key        VARCHAR(50)  NOT NULL UNIQUE KEY,
    shops_shipping_method_requirement_value      VARCHAR(255) NOT NULL
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_shops_reviews
(
    shops_reviews_id         INT AUTO_INCREMENT PRIMARY KEY,
    shop_user_id             INT         NOT NULL,
    shop_item_id             INT         NOT NULL,
    shops_reviews_rating     INT         NOT NULL,
    shops_reviews_title      varchar(50) NOT NULL,
    shops_reviews_text       TEXT        NOT NULL,
    shops_reviews_created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shops_reviews_updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_shops_reviews FOREIGN KEY (shop_user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_shop_item_id_shops_reviews FOREIGN KEY (shop_item_id)
        REFERENCES cmw_shops_items (shop_item_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `cmw_shops_items`
    ADD
        CONSTRAINT fk_shop_image_id_items FOREIGN KEY (shop_image_id)
            REFERENCES cmw_shops_images (`shop_image_id`) ON DELETE SET NULL;

ALTER TABLE `cmw_shops_cart_items`
    ADD
        CONSTRAINT fk_shop_discount_id_cmw_shops_cart_items FOREIGN KEY (shop_discount_id)
            REFERENCES cmw_shops_discount (shop_discount_id) ON UPDATE CASCADE ON DELETE SET NULL;

INSERT INTO cmw_shops_settings (`shop_settings_key`, `shop_settings_value`)
VALUES ('currency', 'EUR'),
       ('symbol', '€'),
       ('after', '1'),
       ('reviews', '1'),
       ('stockAlert', '20'),
       ('perPage', '16'),
       ('shopType', 'both'),
       ('maintenance', '0'),
       ('maintenanceMessage', 'La boutique est en cours de maintenance.'),
       ('autoValidateVirtual', '1'),
       ('topBestBuyer', '3'),
       ('showPublicStock', '1');


INSERT INTO cmw_shops_images (`shop_image_name`, `shop_default_image`)
VALUES ('default', '1');

INSERT INTO cmw_shops_shipping_zone (`shops_shipping_zone_name`, `shops_shipping_zone_country`)
VALUES ('Reste du monde', 'ALL');