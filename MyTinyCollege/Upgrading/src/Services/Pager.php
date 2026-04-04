<?php

declare(strict_types=1);

namespace App\Services;

final class Pager
{
    public int $pageNumber;
    public int $pageSize;
    public int $totalItems;

    public function __construct(int $pageNumber, int $pageSize, int $totalItems)
    {
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize > 0 ? $pageSize : 3;
        $this->totalItems = $totalItems;
    }

    public function pageCount(): int
    {
        if ($this->totalItems === 0) {
            return 1;
        }
        return (int) ceil($this->totalItems / $this->pageSize);
    }
}
