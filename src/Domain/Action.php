<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

interface Action
{
    /**
     * Which player is making the action.
     *
     * @return Color
     */
    public function player(): Color;
}