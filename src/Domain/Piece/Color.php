<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

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

    /**
     * Color constructor.
     *
     * @param string $colorName
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(string $colorName)
    {
        if (!in_array($colorName, self::VALID_COLORS)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid color in the game of chess.', $colorName));
        }
        $this->colorName = $colorName;
    }

    /**
     * Create White color.
     *
     * @return Color
     */
    public static function white(): Color
    {
        return new Color(self::WHITE);
    }

    /**
     * Create Black color.
     *
     * @return Color
     */
    public static function black(): Color
    {
        return new Color(self::BLACK);
    }

    /**
     * Create color from string.
     *
     * @param string $colorName
     *
     * @return Color
     */
    public static function fromString(string $colorName): Color
    {
        return new Color(ucfirst(strtolower($colorName)));
    }

    /**
     * Compare if color is the same as another one.
     *
     * @param Color $anotherColor
     *
     * @return bool
     */
    public function isSameAs(Color $anotherColor): bool
    {
        return $this->colorName === $anotherColor->colorName;
    }
}
