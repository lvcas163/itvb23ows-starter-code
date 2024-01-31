<?php

namespace Lucas\Hive;

class Hand
{
    public static array $DEFAULT_HAND = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
    private array $hand;

    public function __construct(array $hand = null)
    {
        if (!isset($hand)) {
            $hand = self::$DEFAULT_HAND;
        }
        $this->hand = $hand;
    }

    public function hasPiece(string $piece): bool
    {
        return $this->hand[$piece] > 0;
    }

    public function removePiece(string $piece): void
    {
        $this->hand[$piece]--;
    }

    public function sum(): int
    {
        return array_sum($this->hand);
    }

    public function getHand(): array
    {
        return $this->hand;
    }
}
