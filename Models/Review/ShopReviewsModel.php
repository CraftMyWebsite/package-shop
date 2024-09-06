<?php

namespace CMW\Model\Shop\Review;

use CMW\Entity\Shop\Reviews\ShopRatingPercentageEntity;
use CMW\Entity\Shop\Reviews\ShopReviewsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopReviewsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopReviewsModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ?ShopReviewsEntity
     */
    public function getShopReviewById(int $id): ?ShopReviewsEntity
    {
        $sql = 'SELECT * FROM cmw_shops_reviews WHERE shops_reviews_id = :shops_reviews_id AND shops_reviews_created_at = shops_reviews_updated_at ORDER BY shops_reviews_id DESC';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_reviews_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res['shop_user_id']) ? null : UsersModel::getInstance()->getUserById($res['shop_user_id']);
        $item = is_null($res['shop_item_id']) ? null : ShopItemsModel::getInstance()->getShopItemsById($res['shop_item_id']);

        return new ShopReviewsEntity(
            $res['shops_reviews_id'],
            $user,
            $item,
            $res['shops_reviews_rating'],
            $res['shops_reviews_title'],
            $res['shops_reviews_text'],
            $res['shops_reviews_created_at'],
            $res['shops_reviews_updated_at']
        );
    }

    /**
     * @return ShopReviewsEntity []
     */
    public function getShopReviewByItemId(int $id): array
    {
        $sql = 'SELECT shops_reviews_id FROM cmw_shops_reviews WHERE shop_item_id = :shop_item_id AND shops_reviews_created_at = shops_reviews_updated_at';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_id' => $id))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopReviewById($items['shops_reviews_id']);
        }

        return $toReturn;
    }

    public function createReview(int $itemId, int $userId, int $rating, string $title, string $text): ?int
    {
        $data = [
            'shop_item_id' => $itemId,
            'shop_user_id' => $userId,
            'shops_reviews_rating' => $rating,
            'shops_reviews_title' => $title,
            'shops_reviews_text' => $text
        ];

        $sql = 'INSERT INTO cmw_shops_reviews(shop_user_id, shop_item_id, shops_reviews_rating, shops_reviews_title, shops_reviews_text) VALUES (:shop_user_id, :shop_item_id, :shops_reviews_rating, :shops_reviews_title, :shops_reviews_text)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->getShopReviewById($id);
            return $id;
        }

        return null;
    }

    /**
     * @param int $itemId
     * @return int
     */
    public function getAverageRatingByItemId(int $itemId): int
    {
        $data = [
            'shop_item_id' => $itemId,
        ];

        $sql = 'SELECT AVG(shops_reviews_rating) AS average_rating FROM cmw_shops_reviews WHERE shop_item_id = :shop_item_id AND shops_reviews_created_at = shops_reviews_updated_at';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return 0;
        }
        $res = $req->fetch();
        if (!$res) {
            return 0;
        }
        return $res['average_rating'] ?? 0;
    }

    /**
     * @param int $itemId
     * @return int
     */
    public function countTotalRatingByItemId(int $itemId): int
    {
        $data = [
            'shop_item_id' => $itemId,
        ];

        $sql = 'SELECT shop_item_id, COUNT(shops_reviews_id) AS total_reviews FROM cmw_shops_reviews WHERE shop_item_id = :shop_item_id AND shops_reviews_created_at = shops_reviews_updated_at';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return 0;
        }
        $res = $req->fetch();
        if (!$res) {
            return 0;
        }
        return $res['total_reviews'] ?? 0;
    }

    /**
     * @param int $itemId
     * @param ?string $faIcon
     * @param ?string $faSize
     * @desc leave empty $faIcon $faSize for using default
     * @return string
     */
    public function getStars(int $itemId, ?string $faIcon = 'fa-star', ?string $faSize = 'fa-sm'): string
    {
        $averageRating = $this->getAverageRatingByItemId($itemId);
        $fullStars = floor($averageRating);
        $fullStarColor = '#FFD700';
        $emptyStarColor = '#a49b9b';

        $starsHtml = '';

        for ($i = 1; $i <= 5; $i++) {
            $icon = $faIcon;
            $style = 'color: ';

            if ($i <= $fullStars) {
                $style .= $fullStarColor;
            } else {
                $style .= $emptyStarColor;
            }

            $faSizeClass = !empty($faSize) ? ' ' . $faSize : '';

            $starsHtml .= '<i style="' . $style . ';" class="fa-solid ' . $icon . $faSizeClass . "\"></i>\u{00A0}";
        }

        return $starsHtml;
    }

    /**
     * @param ?string $faIcon
     * @desc leave empty $faIcon $faSize for using default
     * @return string
     */
    public function getInputStars(?string $faIcon = 'fa-star'): string
    {
        // CSS pour la notation par étoiles
        $css = '<style>
            .rating { direction: rtl; text-align: left; }
            .rating-input { display: none; }
            .rating-star { font-size: 20px; color: #ddd; cursor: pointer; }
            .rating-input:checked ~ .rating-star,
            .rating-star:hover,
            .rating-star:hover ~ .rating-star { color: #FFD700; }
            </style>';

        // HTML pour les étoiles
        $html = '<div class="rating">';

        for ($i = 5; $i >= 1; $i--) {
            $required = ($i === 5) ? ' required' : '';
            $html .= '<input id="star' . $i . '" name="rating" type="radio" value="' . $i . '" class="rating-input"' . $required . '/>
                      <label for="star' . $i . '" class="rating-star"><i class="fa ' . $faIcon . '"></i></label>';
        }

        $html .= '</div>';

        return $css . $html;
    }

    /**
     * @param int $itemId
     * @return \CMW\Entity\Shop\Reviews\ShopRatingPercentageEntity[]
     */
    public function getRatingsPercentageByItemId(int $itemId): array
    {
        $sql = 'SELECT rating_table.rating, 
        COALESCE(ROUND(COUNT(cmr.shops_reviews_id) * 100.0 / total.total_reviews, 2), 0) AS percentage
        FROM (SELECT 1 AS rating UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) AS rating_table
        LEFT JOIN cmw_shops_reviews cmr ON cmr.shops_reviews_rating = rating_table.rating AND cmr.shop_item_id = :shop_item_id
        CROSS JOIN (SELECT COUNT(*) AS total_reviews FROM cmw_shops_reviews WHERE shop_item_id = :shop_item_id_2) AS total
        GROUP BY rating_table.rating
        ORDER BY rating_table.rating DESC ;';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['shop_item_id' => $itemId, 'shop_item_id_2' => $itemId]);
        $results = $req->fetchAll();

        $ratingsPercentages = [];
        foreach ($results as $row) {
            $ratingsPercentages[] = new ShopRatingPercentageEntity($row['rating'], $row['percentage']);
        }

        return $ratingsPercentages;
    }

    public function deleteReview(int $reviewId): bool
    {
        $data = ['shops_reviews_id' => $reviewId];

        $sql = 'DELETE FROM cmw_shops_reviews WHERE shops_reviews_id = :shops_reviews_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }
}
