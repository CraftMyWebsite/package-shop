<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;

class ShopImageEntity
{

    private int $imageId;
    private string $imageName;
    private ?int $imageCategoryId;
    private ?int $imageItemId;
    private string $imageCreated;
    private string $imageUpdated;


    public function __construct(int $imageId, string $imageName, ?int $imageCategoryId, ?int $imageItemId, string $imageCreated, string $imageUpdated)
    {
        $this->imageId = $imageId;
        $this->imageName = $imageName;
        $this->imageCategoryId = $imageCategoryId;
        $this->imageItemId = $imageItemId;
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
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Shop/" . $this->imageName;
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
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->imageCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->imageUpdated);
    }


}