<?php

namespace App\Model;

class CategoryFortuneStats
{
    public function __construct(
        public int $fortunesPrinted,
        public int $fortunesAvereage,
        public string $categoryName
    ) {

    }
}
