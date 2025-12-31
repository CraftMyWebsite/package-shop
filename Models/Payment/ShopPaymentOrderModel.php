<?php

namespace CMW\Model\Shop\Payment;

use CMW\Entity\Shop\Payments\ShopPaymentOrderEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopPaymentOrderModel
 * @package Shop
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class ShopPaymentOrderModel extends AbstractModel
{
    public function getPaymentOrderById(int $id): ?ShopPaymentOrderEntity
    {
        $sql = 'SELECT * FROM cmw_shop_payment_order WHERE order_id = :order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['order_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = is_null($res['user_id']) ? null : UsersModel::getInstance()->getUserById($res['user_id']);

        return new ShopPaymentOrderEntity(
            $res['order_id'],
            $user,
            $res['amount_cents'],
            $res['currency'],
            $res['status'],
            $res['nonce'],
            $res['session_id'] ?? null,
            $res['payment_intent'] ?? null,
            $res['order_created'],
            $res['paid_at'] ?? null,
            $res['updated_at'] ?? null,
        );
    }

    public function createPending(int $userId, int $amount, string $currency, string $nonce): ?ShopPaymentOrderEntity
    {
        $var = [
            'user_id' => $userId,
            'amount_cents' => $amount,
            'currency' => $currency,
            'nonce' => $nonce,
        ];

        $sql = 'INSERT INTO cmw_shop_payment_order (user_id, amount_cents, currency, nonce, status)
                    VALUES (:user_id, :amount_cents, :currency, :nonce, "PENDING")';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($var)) {
            return null;
        }

        return $this->getPaymentOrderById($db->lastInsertId());
    }

    public function attachPaymentSession(int $orderId, string $sessionId): void
    {
        $sql = 'UPDATE cmw_shop_payment_order SET session_id = :session_id WHERE order_id = :order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);
        $res->execute(['session_id' => $sessionId, 'order_id' => $orderId]);
    }

    public function markPaid(int $orderId, string $sessionId, ?string $paymentIntent, string $paidAt): void
    {
        $sql = 'UPDATE cmw_shop_payment_order SET payment_intent = :payment_intent, paid_at = :paid_at, status = "PAID" WHERE order_id = :order_id AND session_id = :session_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);
        $res->execute(['payment_intent' => $paymentIntent, 'paid_at' => $paidAt, 'session_id' => $sessionId, 'order_id' => $orderId]);
    }

    public function getPaymentBySessionId(string $sessionId): ?ShopPaymentOrderEntity
    {
        $sql = 'SELECT order_id FROM cmw_shop_payment_order WHERE session_id = :sid';
        $db  = DatabaseManager::getInstance();

        $res  = $db->prepare($sql);
        if (!$res->execute(['sid' => $sessionId])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getPaymentOrderById($res['order_id']);
    }

    public function markCanceled(string $sessionId): void
    {
        $sql = 'UPDATE cmw_shop_payment_order SET status = "CANCELLED" WHERE session_id = :session_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);
        $res->execute(['session_id' => $sessionId]);
    }
}
