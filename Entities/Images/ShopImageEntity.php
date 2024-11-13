<?php

namespace CMW\Entity\Shop\Images;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Manager\Env\EnvManager;

class ShopImageEntity extends AbstractEntity
{
    private int $imageId;
    private string $imageName;
    private ?int $imageCategoryId;
    private ?int $imageItemId;
    private int $imageOrder;
    private string $imageCreated;
    private string $imageUpdated;

    public function __construct(int $imageId, string $imageName, ?int $imageCategoryId, ?int $imageItemId, int $imageOrder, string $imageCreated, string $imageUpdated)
    {
        $this->imageId = $imageId;
        $this->imageName = $imageName;
        $this->imageCategoryId = $imageCategoryId;
        $this->imageItemId = $imageItemId;
        $this->imageOrder = $imageOrder;
        $this->imageCreated = $imageCreated;
        $this->imageUpdated = $imageUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->imageId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->imageName;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Shop/' . $this->imageName;
    }

    /**
     * @return ?int
     */
    public function getImageCategoryId(): ?int
    {
        return $this->imageCategoryId;
    }

    /**
     * @return ?int
     */
    public function getImageItemId(): ?int
    {
        return $this->imageItemId;
    }

    /**
     * @return int
     */
    public function getImageOrder(): int
    {
        return $this->imageOrder;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return Date::formatDate($this->imageCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->imageUpdated);
    }
}
