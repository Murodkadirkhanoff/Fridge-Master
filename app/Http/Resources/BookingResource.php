<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'access_code' => $this->access_code,
            'product_volume' => $this->product_volume,
            'reserved_blocks_count' => count($this->blocks)
        ];
    }
}
