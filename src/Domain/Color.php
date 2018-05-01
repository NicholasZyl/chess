<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class Color
{
    private const WHITE = 'White';
    private const BLACK = 'Black';
    private const VALID_COLORS = [
        self::WHITE,
        self::BLACK,
    ];

    /**
     * @var string
     */
    private $colorName;

    private function __construct(string $colorName)
    {
        if (!in_array($colorName, self::VALID_COLORS)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid color in the game of chess.', $colorName));
        }
        $this->colorName = $colorName;
    }

    public static function black(): Color
    {
        return new Color(self::BLACK);
    }

    public static function white(): Color
    {
        return new Color(self::WHITE);
    }

    public static function fromString(string $colorName): Color
    {
        return new Color(ucfirst(strtolower($colorName)));
    }

    public function isSameAs(Color $anotherColor): bool
    {
        return $this->colorName === $anotherColor->colorName;
    }
}
