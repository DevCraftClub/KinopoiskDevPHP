<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Studio;

class SearchStudioResponseDto
{
    /**
     * @var Studio[]
     */
    public array $docs;
    public int $total;
    public int $limit;
    public int $page;
    public int $pages;

    public function __construct(array $docs, int $total, int $limit, int $page, int $pages)
    {
        $this->docs = $docs;
        $this->total = $total;
        $this->limit = $limit;
        $this->page = $page;
        $this->pages = $pages;
    }

    public static function fromArray(array $data): self
    {
        $docs = array_map(fn($item) => Studio::fromArray($item), $data['docs'] ?? []);
        return new self(
            $docs,
            $data['total'] ?? 0,
            $data['limit'] ?? 0,
            $data['page'] ?? 1,
            $data['pages'] ?? 1
        );
    }

    public function toArray(): array
    {
        return [
            'docs' => array_map(fn($studio) => $studio->toArray(), $this->docs),
            'total' => $this->total,
            'limit' => $this->limit,
            'page' => $this->page,
            'pages' => $this->pages,
        ];
    }
} 