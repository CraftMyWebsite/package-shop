<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;

class ShopCategoryEntity
{

    private int $categoryId;
    private string $categoryName;
    private string $categoryDescription;
    private string $categorySlug;
    private ?int $categoryImage;
    private string $categoryCreated;
    private string $categoryUpdated;


    public function __construct(int $categoryId, string $categoryName, string $categoryDescription, string $categorySlug, ?int $categoryImage, string $categoryCreated, string $categoryUpdated)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->categoryDescription = $categoryDescription;
        $this->categorySlug = $categorySlug;
        $this->categoryImage = $categoryImage;
        $this->categoryCreated = $categoryCreated;
        $this->categoryUpdated = $categoryUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->categoryName;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->categoryDescription;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->categorySlug;
    }

    /**
     * @return int
     */
    public function getImage(): int
    {
        return $this->categoryImage;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->categoryCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->categoryUpdated);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return "shop/$this->categorySlug";
    }

}