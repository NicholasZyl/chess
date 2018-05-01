<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class Color
{
    /**
     * @var string
     */
    private $colorName;

    private function __construct(string $colorName)
    {
        $this->colorName = $colorName;
    }

    public static function fromString(string $colorName)
    {
        return new Color($colorName);
    }

    public function isSameAs(Color $anotherColor)
    {
        return $this->colorName === $anotherColor->colorName;
    }
}
