<?php
namespace CMW\Entity\Shop\Enum\Item;

enum ShopItemType: int
{
    case PHYSICAL = 0;
    case VIRTUAL = 1;

    public static function fromDb(int $value): self
    {
        return match ($value) {
            0 => self::PHYSICAL,
            1 => self::VIRTUAL,
            default => throw new \InvalidArgumentException("Invalid shop item type: $value")
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PHYSICAL => 'Physique',
            self::VIRTUAL => 'Virtuel',
        };
    }
}
