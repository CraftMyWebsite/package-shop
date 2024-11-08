<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopHistoryOrdersModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersEntity|null
     */
    public function getHistoryOrdersById(int $id): ?ShopHistoryOrdersEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_history_order_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res['user_id']) ? null : UsersModel::getInstance()?->getUserById($res['user_id']) ?? null;

        return new ShopHistoryOrdersEntity(
            $res['shop_history_order_id'],
            $user,
            $res['shop_history_order_status'],
            $res['shop_history_order_shipping_link'] ?? null,
            $res['shop_history_order_number'] ?? null,
            $res['shop_history_order_created_at'] ?? null,
            $res['shop_history_order_updated_at'] ?? null
        );
    }

    /**
     * @return ShopHistoryOrdersEntity []
     */
    public function getHistoryOrdersByUserId(int $userId): array
    {
        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order WHERE user_id = :user_id ORDER BY shop_history_order_created_at DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['user_id' => $userId])) {
            return [];
        }

        $toReturn = [];

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersById($order['shop_history_order_id']);
        }

        return $toReturn;
    }

    /**
     * @param string $orderId
     * @return ShopHistoryOrdersEntity|null
     */
    public function getHistoryOrdersByOrderNumber(string $orderId): ?ShopHistoryOrdersEntity
    {
        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order WHERE shop_history_order_number = :orderId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['orderId' => $orderId])) {
            return null;
        }

        $res = $res->fetch();

        return $this->getHistoryOrdersById($res['shop_history_order_id']);
    }

    /**
     * @return ShopHistoryOrdersEntity []
     */
    public function getInProgressOrders(): array
    {
        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order WHERE shop_history_order_status IN (1, 2, 0, -1) ORDER BY shop_history_order_status ASC;';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersById($order['shop_history_order_id']);
        }

        return $toReturn;
    }

    /**
     * @return ShopHistoryOrdersEntity []
     */
    public function getFinishedOrders(): array
    {
        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order WHERE shop_history_order_status = 3 ORDER BY shop_history_order_id DESC;';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersById($order['shop_history_order_id']);
        }

        return $toReturn;
    }

    /**
     * @return ShopHistoryOrdersEntity []
     */
    public function getErrorOrders(): array
    {
        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order WHERE shop_history_order_status = -2;';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersById($order['shop_history_order_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $userId
     * @param int $itemId
     * @return int
     */
    public function countOrderByUserIdAndItemId(int $userId, int $itemId): int
    {
        $sql = 'SELECT SUM(soi.shop_history_order_items_quantity) AS total_quantity FROM cmw_shop_history_order_items soi
                JOIN cmw_shop_history_order so ON soi.shop_history_order_id = so.shop_history_order_id
                WHERE so.user_id = :user_id AND soi.item_id = :item_id;';

        $data = ['item_id' => $itemId, 'user_id' => $userId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['total_quantity'] ?? 0;
    }

    public function createHistoryOrder(int $userId, int $status): ?ShopHistoryOrdersEntity
    {
        $var = [
            'user_id' => $userId,
            'shop_history_order_status' => $status
        ];

        $sql = 'INSERT INTO cmw_shop_history_order (user_id, shop_history_order_status) VALUES (:user_id, :shop_history_order_status)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            $this->generateOrderNumber($id);
            return $this->getHistoryOrdersById($id);
        }

        return null;
    }

    public function generateOrderNumber(int $orderId): void
    {
        $number = date('njy') . $orderId;
        $data = ['shop_history_order_id' => $orderId, 'number' => $number];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_number = :number WHERE shop_history_order_id = :shop_history_order_id';
        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function toSendStep(int $orderId): void
    {
        $var = [
            'shop_history_order_id' => $orderId,
        ];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_status = 1 WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function toFinalStep(int $orderId, ?string $shippingLink): void
    {
        $var = [
            'shop_history_order_id' => $orderId,
            'shop_history_order_shipping_link' => $shippingLink,
        ];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_status = 2, shop_history_order_shipping_link = :shop_history_order_shipping_link WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function endOrder(int $orderId): void
    {
        $var = [
            'shop_history_order_id' => $orderId,
        ];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_status = 3 WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function toCancelStep(int $orderId): void
    {
        $var = [
            'shop_history_order_id' => $orderId,
        ];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_status = -1 WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function refundStep(int $orderId): void
    {
        $var = [
            'shop_history_order_id' => $orderId,
        ];

        $sql = 'UPDATE cmw_shop_history_order SET shop_history_order_status = -2 WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }
}
