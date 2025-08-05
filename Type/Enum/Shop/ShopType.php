<?php
namespace CMW\Type\Shop\Enum\Shop;

enum ShopType: string
{
    case VIRTUAL_ONLY = 'virtual';
    case PHYSICAL_ONLY = 'physical';
    case BOTH = 'both';

    public static function fromString(string $value): self
    {
        return match ($value) {
            'virtual' => self::VIRTUAL_ONLY,
            'physical' => self::PHYSICAL_ONLY,
            'both' => self::BOTH,
            default => throw new \InvalidArgumentException("Invalid shop config: $value")
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::VIRTUAL_ONLY => 'Virtuel',
            self::PHYSICAL_ONLY => 'Physique',
            self::BOTH => 'Virtuel et Physique',
        };
    }

}

