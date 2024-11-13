<?php

namespace CMW\Entity\Shop\Categories;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Utils\Website;

class ShopCategoryEntity extends AbstractEntity
{
    private int $categoryId;
    private string $categoryName;
    private ?string $categoryIcon;
    private ?string $categoryDescription;
    private string $categorySlug;
    private ?ShopCategoryEntity $categoryParent;
    private string $categoryCreated;
    private string $categoryUpdated;

    public function __construct(int $categoryId, string $categoryName, ?string $categoryIcon, ?string $categoryDescription, string $categorySlug, ?ShopCategoryEntity $categoryParent, string $categoryCreated, string $categoryUpdated)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->categoryIcon = $categoryIcon;
        $this->categoryDescription = $categoryDescription;
        $this->categorySlug = $categorySlug;
        $this->categoryParent = $categoryParent;
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
     * @return ?string
     */
    public function getDescription(): ?string
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
     * @param string|null $param
     * @return ?string
     */
    public function getFontAwesomeIcon(?string $param = null): ?string
    {
        return '<i class="' . $this->categoryIcon . '  ' . $param . '"></i>';
    }

    /**
     * @return ?string
     */
    public function getIcon(): ?string
    {
        return $this->categoryIcon;
    }

    public function getParent(): ?ShopCategoryEntity
    {
        return $this->categoryParent;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return Date::formatDate($this->categoryCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->categoryUpdated);
    }

    /**
     * @return string
     */
    public function getCatLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/cat/$this->categorySlug";
    }

    /**
     * @return int
     */
    public function countItemsInCat(): int
    {
        return ShopCategoriesModel::getInstance()->countItemsByCatId($this->categoryId);
    }
}
