<?php

namespace App\Service;

class JsonApi
{
    /**
     * @param array<string, mixed> $attributes
     *
     * @return array{data: array{type: string, id: string, attributes: array<string, mixed>}}
     */
    public function item(string $type, int|string $id, array $attributes): array
    {
        return [
            'data' => [
                'type' => $type,
                'id' => (string) $id,
                'attributes' => $attributes,
            ],
        ];
    }

    /**
     * @param array<int, array{type: string, id: string, attributes: array<string, mixed>}> $items
     *
     * @return array{data: array<int, array{type: string, id: string, attributes: array<string, mixed>}>}
     */
    public function collection(array $items): array
    {
        return ['data' => $items];
    }
}
