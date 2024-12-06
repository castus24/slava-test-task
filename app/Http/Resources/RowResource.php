<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $developer_id
 * @property string $name
 * @property string $date
 */
class RowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'developer_id' => $this->developer_id,
            'name' => $this->name,
            'date' => $this->date,
        ];
    }
}
