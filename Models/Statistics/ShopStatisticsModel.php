<?php

namespace CMW\Model\Shop\Statistics;

use CMW\Entity\Shop\Statistics\ShopBestBuyerEntity;
use CMW\Entity\Shop\Statistics\ShopBestSellerEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;

/**
 * Class @ShopStatisticsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopStatisticsModel extends AbstractModel
{
    public function countTotalOrdersThisMonth(): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders
            FROM cmw_shop_history_order
            WHERE MONTH(shop_history_order_created_at) = MONTH(CURRENT_DATE())
            AND YEAR(shop_history_order_created_at) = YEAR(CURRENT_DATE());';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders'] ?? 0;
    }

    public function countTotalOrders(): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders
            FROM cmw_shop_history_order;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders'] ?? 0;
    }

    public function countOrdersStatusThisMonth(int $status): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders_status
            FROM cmw_shop_history_order
            WHERE shop_history_order_status = :status
            AND MONTH(shop_history_order_created_at) = MONTH(CURRENT_DATE())
            AND YEAR(shop_history_order_created_at) = YEAR(CURRENT_DATE());';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array('status' => $status))) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders_status'] ?? 0;
    }

    public function countOrdersStatus(int $status): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders_status
            FROM cmw_shop_history_order
            WHERE shop_history_order_status = :status;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array('status' => $status))) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders_status'] ?? 0;
    }

    public function countActiveOrders(): int
    {
        $sql = 'SELECT shop_history_order_status, COUNT(*) AS order_count FROM cmw_shop_history_order WHERE shop_history_order_status IN (-1, 0, 1, 2);';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['order_count'] ?? 0;
    }

    public function gainThisMonth(): int
    {
        $sql = 'SELECT 
  SUM(order_totals.items_total 
      + COALESCE(shipping.shop_history_order_shipping_price, 0) 
      + COALESCE(payment.shop_history_order_payment_fee, 0)) AS monthly_gain
FROM (
    SELECT 
        o.shop_history_order_id,
        SUM(i.shop_history_order_items_total_price_after_discount) AS items_total
    FROM cmw_shop_history_order_items i
    JOIN cmw_shop_history_order o ON i.shop_history_order_id = o.shop_history_order_id
    JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
    WHERE o.shop_history_order_created_at >= DATE_FORMAT(CURRENT_DATE, \'%Y-%m-01\')
      AND o.shop_history_order_created_at < DATE_FORMAT(CURRENT_DATE + INTERVAL 1 MONTH, \'%Y-%m-01\')
      AND o.shop_history_order_status = 3
      AND s.shop_item_price_type = \'money\'
    GROUP BY o.shop_history_order_id
) AS order_totals
LEFT JOIN cmw_shop_history_order_shipping shipping ON order_totals.shop_history_order_id = shipping.shop_history_order_id
LEFT JOIN cmw_shop_history_order_payment payment ON order_totals.shop_history_order_id = payment.shop_history_order_id;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['monthly_gain'] ?? 0;
    }

    public function gainTotal(): int
    {
        $sql = 'SELECT 
  SUM(order_totals.items_total 
      + COALESCE(shipping.shop_history_order_shipping_price, 0) 
      + COALESCE(payment.shop_history_order_payment_fee, 0)) AS total_gain
FROM (
    SELECT 
      i.shop_history_order_id,
      SUM(i.shop_history_order_items_total_price_after_discount) AS items_total
    FROM cmw_shop_history_order_items i
    JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
    WHERE s.shop_item_price_type = \'money\'
    GROUP BY i.shop_history_order_id
) AS order_totals
JOIN cmw_shop_history_order o ON order_totals.shop_history_order_id = o.shop_history_order_id
LEFT JOIN cmw_shop_history_order_shipping shipping ON o.shop_history_order_id = shipping.shop_history_order_id
LEFT JOIN cmw_shop_history_order_payment payment ON o.shop_history_order_id = payment.shop_history_order_id
WHERE o.shop_history_order_status = 3;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['total_gain'] ?? 0;
    }

    public function lostTotal(): int
    {
        $sql = 'SELECT 
  SUM(order_totals.items_total 
      + COALESCE(shipping.shop_history_order_shipping_price, 0) 
      + COALESCE(payment.shop_history_order_payment_fee, 0)) AS total_lost
FROM (
    SELECT 
      i.shop_history_order_id,
      SUM(i.shop_history_order_items_total_price_after_discount) AS items_total
    FROM cmw_shop_history_order_items i
    JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
    WHERE s.shop_item_price_type = \'money\'
    GROUP BY i.shop_history_order_id
) AS order_totals
JOIN cmw_shop_history_order o ON order_totals.shop_history_order_id = o.shop_history_order_id
LEFT JOIN cmw_shop_history_order_shipping shipping ON o.shop_history_order_id = shipping.shop_history_order_id
LEFT JOIN cmw_shop_history_order_payment payment ON o.shop_history_order_id = payment.shop_history_order_id
WHERE o.shop_history_order_status IN (-1, -2);';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['total_lost'] ?? 0;
    }

    public function lostThisMonth(): int
    {
        $sql = 'SELECT 
  SUM(items_total + COALESCE(shipping_total, 0) + COALESCE(payment_total, 0)) AS monthly_lost
FROM (
    SELECT 
      o.shop_history_order_id,
      SUM(i.shop_history_order_items_total_price_after_discount) AS items_total,
      (SELECT shop_history_order_shipping_price 
       FROM cmw_shop_history_order_shipping shipping 
       WHERE shipping.shop_history_order_id = o.shop_history_order_id) AS shipping_total,
      (SELECT shop_history_order_payment_fee 
       FROM cmw_shop_history_order_payment payment 
       WHERE payment.shop_history_order_id = o.shop_history_order_id) AS payment_total
    FROM cmw_shop_history_order_items i
    JOIN cmw_shop_history_order o ON i.shop_history_order_id = o.shop_history_order_id
    JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
    WHERE o.shop_history_order_created_at >= DATE_FORMAT(CURRENT_DATE, \'%Y-%m-01\')
      AND o.shop_history_order_created_at < DATE_FORMAT(CURRENT_DATE + INTERVAL 1 MONTH, \'%Y-%m-01\')
      AND o.shop_history_order_status IN (-1, -2)
      AND s.shop_item_price_type = \'money\'
    GROUP BY o.shop_history_order_id
) AS orders';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['monthly_lost'] ?? 0;
    }

    /**
     * @param int $id
     * @return ShopBestBuyerEntity|null
     */
    public function getBestBuyer(int $userId, float $spent): ?ShopBestBuyerEntity
    {
        $settings = UserSettingsEntity::getInstance();
        $user = UsersModel::getInstance()?->getUserById($userId);
        $userPseudo = $user ? $user->getPseudo() : 'Utilisateur introuvable';
        $userImage = $user ? $user->getUserPicture()?->getImage() : EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/Default/' . $settings->getDefaultImage();

        return new ShopBestBuyerEntity(
            $userPseudo,
            $userImage,
            $spent
        );
    }

    /**
     * @return ShopBestBuyerEntity []
     */
    public function bestBuyersThisMonth(): array
    {
        $limit = ShopSettingsModel::getInstance()->getSettingValue('topBestBuyer');

        $sql = 'SELECT 
  o.user_id, 
  SUM(order_totals.order_total) AS total_spent
FROM cmw_shop_history_order o
JOIN (
  SELECT 
    i.shop_history_order_id,
    SUM(i.shop_history_order_items_total_price_after_discount) AS items_total,
    COALESCE(shipping.shop_history_order_shipping_price, 0) AS shipping_price,
    COALESCE(payment.shop_history_order_payment_fee, 0) AS payment_fee,
    SUM(i.shop_history_order_items_total_price_after_discount) 
    + COALESCE(shipping.shop_history_order_shipping_price, 0) 
    + COALESCE(payment.shop_history_order_payment_fee, 0) AS order_total
  FROM cmw_shop_history_order_items i
  LEFT JOIN cmw_shop_history_order_shipping shipping ON i.shop_history_order_id = shipping.shop_history_order_id
  LEFT JOIN cmw_shop_history_order_payment payment ON i.shop_history_order_id = payment.shop_history_order_id
  JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
  WHERE s.shop_item_price_type = \'money\'
  GROUP BY i.shop_history_order_id
) AS order_totals ON o.shop_history_order_id = order_totals.shop_history_order_id
WHERE o.shop_history_order_created_at >= DATE_FORMAT(CURRENT_DATE, \'%Y-%m-01\')
  AND o.shop_history_order_created_at < DATE_FORMAT(CURRENT_DATE + INTERVAL 1 MONTH, \'%Y-%m-01\')
  AND o.shop_history_order_status = 3
GROUP BY o.user_id
ORDER BY total_spent DESC 
LIMIT :limit;';
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('limit' => $limit))) {
            return [];
        }

        $toReturn = [];

        while ($buyer = $res->fetch()) {
            $toReturn[] = $this->getBestBuyer((int)$buyer['user_id'], (float)$buyer['total_spent']);
        }

        return $toReturn;
    }

    /**
     * @return ShopBestBuyerEntity []
     */
    public function bestBuyers(): array
    {
        $limit = ShopSettingsModel::getInstance()->getSettingValue('topBestBuyer');

        $sql = 'SELECT 
  o.user_id, 
  SUM(order_totals.order_total) AS total_spent
FROM cmw_shop_history_order o
JOIN (
  SELECT 
    i.shop_history_order_id,
    SUM(i.shop_history_order_items_total_price_after_discount) AS items_total,
    COALESCE(shipping.shop_history_order_shipping_price, 0) AS shipping_price,
    COALESCE(payment.shop_history_order_payment_fee, 0) AS payment_fee,
    SUM(i.shop_history_order_items_total_price_after_discount) 
    + COALESCE(shipping.shop_history_order_shipping_price, 0) 
    + COALESCE(payment.shop_history_order_payment_fee, 0) AS order_total
  FROM cmw_shop_history_order_items i
  LEFT JOIN cmw_shop_history_order_shipping shipping ON i.shop_history_order_id = shipping.shop_history_order_id
  LEFT JOIN cmw_shop_history_order_payment payment ON i.shop_history_order_id = payment.shop_history_order_id
  JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
  WHERE s.shop_item_price_type = \'money\'
  GROUP BY i.shop_history_order_id
) AS order_totals ON o.shop_history_order_id = order_totals.shop_history_order_id
WHERE o.shop_history_order_status = 3
GROUP BY o.user_id
ORDER BY total_spent DESC
LIMIT :limit;
';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('limit' => $limit))) {
            return [];
        }

        $toReturn = [];

        while ($buyer = $res->fetch()) {
            $toReturn[] = $this->getBestBuyer((int)$buyer['user_id'], (float)$buyer['total_spent']);
        }

        return $toReturn;
    }

    public function countActiveItems(): int
    {
        $sql = 'SELECT COUNT(*) AS item_count FROM cmw_shops_items WHERE shop_item_draft = 0 AND shop_item_archived = 0;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['item_count'] ?? 0;
    }

    public function countArchivedItems(): int
    {
        $sql = 'SELECT COUNT(*) AS item_count FROM cmw_shops_items WHERE shop_item_archived = 1;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['item_count'] ?? 0;
    }

    public function countDraftItems(): int
    {
        $sql = 'SELECT COUNT(*) AS item_count FROM cmw_shops_items WHERE shop_item_draft = 1;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['item_count'] ?? 0;
    }

    public function countItemsInCart(): int
    {
        $sql = 'SELECT COUNT(*) AS item_cart_count FROM cmw_shops_cart_items';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        return $res['item_cart_count'] ?? 0;
    }

    /**
     * @param int $id
     * @return ShopBestSellerEntity|null
     */
    public function getBestSeller(int $itemId, int $sales): ?ShopBestSellerEntity
    {
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);

        return new ShopBestSellerEntity(
            $item,
            $sales
        );
    }

    /**
     * @return ShopBestSellerEntity []
     */
    public function bestSellers(): array
    {
        $sql = 'SELECT 
  i.item_id, 
  SUM(i.shop_history_order_items_quantity) AS total_sales
FROM cmw_shop_history_order_items i
JOIN cmw_shop_history_order o ON i.shop_history_order_id = o.shop_history_order_id
WHERE o.shop_history_order_status = 3
GROUP BY i.item_id
ORDER BY total_sales DESC
LIMIT 5;';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($seller = $res->fetch()) {
            $toReturn[] = $this->getBestSeller((int)$seller['item_id'], (int)$seller['total_sales']);
        }

        return $toReturn;
    }

    public function gainByMonth(int $monthOffset): int
    {
        $sql = 'SELECT 
      SUM(order_totals.items_total 
          + COALESCE(shipping.shop_history_order_shipping_price, 0) 
          + COALESCE(payment.shop_history_order_payment_fee, 0)) AS monthly_gain
    FROM (
        SELECT 
            o.shop_history_order_id,
            SUM(i.shop_history_order_items_total_price_after_discount) AS items_total
        FROM cmw_shop_history_order_items i
        JOIN cmw_shop_history_order o ON i.shop_history_order_id = o.shop_history_order_id
        JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
        WHERE o.shop_history_order_created_at >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), \'%Y-%m-01\')
          AND o.shop_history_order_created_at < DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL (3 - 1) MONTH), \'%Y-%m-01\')
          AND o.shop_history_order_status = 3
          AND s.shop_item_price_type = \'money\'
        GROUP BY o.shop_history_order_id
    ) AS order_totals
    LEFT JOIN cmw_shop_history_order_shipping shipping ON order_totals.shop_history_order_id = shipping.shop_history_order_id
    LEFT JOIN cmw_shop_history_order_payment payment ON order_totals.shop_history_order_id = payment.shop_history_order_id;';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        // Utilisation de execute avec tableau associatif pour le paramètre
        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();
        return $res['monthly_gain'] ?? 0;
    }


    public function lostByMonth(int $monthOffset): int
    {
        $sql = 'SELECT 
      SUM(items_total + COALESCE(shipping_total, 0) + COALESCE(payment_total, 0)) AS monthly_lost
    FROM (
        SELECT 
          o.shop_history_order_id,
          SUM(i.shop_history_order_items_total_price_after_discount) AS items_total,
          (SELECT shop_history_order_shipping_price 
           FROM cmw_shop_history_order_shipping shipping 
           WHERE shipping.shop_history_order_id = o.shop_history_order_id) AS shipping_total,
          (SELECT shop_history_order_payment_fee 
           FROM cmw_shop_history_order_payment payment 
           WHERE payment.shop_history_order_id = o.shop_history_order_id) AS payment_total
        FROM cmw_shop_history_order_items i
        JOIN cmw_shop_history_order o ON i.shop_history_order_id = o.shop_history_order_id
        JOIN cmw_shops_items s ON i.item_id = s.shop_item_id
        WHERE o.shop_history_order_created_at >= DATE_FORMAT(CURRENT_DATE - INTERVAL 3 MONTH, \'%Y-%m-01\')
          AND o.shop_history_order_created_at < DATE_FORMAT(CURRENT_DATE - INTERVAL 3 - 1 MONTH, \'%Y-%m-01\')
          AND o.shop_history_order_status IN (-1, -2)
          AND s.shop_item_price_type = \'money\'
        GROUP BY o.shop_history_order_id
    ) AS orders';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();
        return $res['monthly_lost'] ?? 0;
    }


}
