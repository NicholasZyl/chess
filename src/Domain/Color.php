<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class Color
{
    const WHITE = 'white';
    const BLACK = 'black';

    /**
     * @var string
     */
    private $colorName;

    private function __construct(string $colorName)
    {
        $this->colorName = $colorName;
    }

    public static function black()
    {
        return new Color(self::BLACK);
    }

    public static function white()
    {
        return new Color(self::WHITE);
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
