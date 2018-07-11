<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class Color
{
    public const WHITE = 'White';
    public const BLACK = 'Black';
    private const VALID_COLORS = [
        self::WHITE,
        self::BLACK,
    ];

    /**
     * @var string
     */
    private $colorName;

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
     * Compare if color is the same as another one.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function is(Color $color): bool
    {
        return $this->colorName === $color->colorName;
    }

    /**
     * Get string representation of color.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->colorName;
    }

    /**
     * Get opponents color.
     *
     * @return Color
     */
    public function opponent(): Color
    {
        return self::WHITE === $this->colorName ? Color::black() : Color::white();
    }
}
