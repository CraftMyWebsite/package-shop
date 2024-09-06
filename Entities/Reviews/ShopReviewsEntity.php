<?php

namespace CMW\Entity\Shop\Reviews;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Review\ShopReviewsModel;

class ShopReviewsEntity
{
    private int $reviewsId;
    private UserEntity $user;
    private ShopItemEntity $item;
    private int $rate;
    private string $reviewTitle;
    private string $reviewText;
    private string $reviewCreated;
    private string $reviewUpdated;

    /**
     * @param int $reviewsId
     * @param UserEntity $user
     * @param ShopItemEntity $item
     * @param int $rate
     * @param string $reviewTitle
     * @param string $reviewText
     * @param string $reviewCreated
     * @param string $reviewUpdated
     */
    public function __construct(int $reviewsId, UserEntity $user, ShopItemEntity $item, int $rate, string $reviewTitle, string $reviewText, string $reviewCreated, string $reviewUpdated)
    {
        $this->reviewsId = $reviewsId;
        $this->user = $user;
        $this->item = $item;
        $this->rate = $rate;
        $this->reviewTitle = $reviewTitle;
        $this->reviewText = $reviewText;
        $this->reviewCreated = $reviewCreated;
        $this->reviewUpdated = $reviewUpdated;
    }

    public function getId(): int
    {
        return $this->reviewsId;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }

    public function countItemTotalRating(): int
    {
        return ShopReviewsModel::getInstance()->countTotalRatingByItemId($this->item->getId());
    }

    public function getItemAverageRating(): int
    {
        return ShopReviewsModel::getInstance()->getAverageRatingByItemId($this->item->getId());
    }

    public function getReviewRating(): int
    {
        return $this->rate;
    }

    /**
     * @param ?string $faIcon
     * @param ?string $faSize
     * @desc leave empty $faIcon $faSize for using default
     * @return string
     */
    public function getStarsReview(?string $faIcon = 'fa-star', ?string $faSize = 'fa-sm'): string
    {
        $averageRating = $this->getReviewRating();
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

    public function getReviewTitle(): string
    {
        return $this->reviewTitle;
    }

    public function getReviewText(): string
    {
        return $this->reviewText;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->reviewCreated);
    }

    public function getUpdated(): string
    {
        return $this->reviewUpdated;
    }
}
