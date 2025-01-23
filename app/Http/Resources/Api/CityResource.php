<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Resource di API data yang ada pada backend
     * untuk ditampilkan di frontend
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'photo' => $this->photo,
            'officeSpaces_count' => $this->officeSpaces_count, //menghitung jumlah office yang ada di setiap kota cth: Medan terdapat 10 Office
            'officeSpaces' => OfficeSpaceResource::collection($this->whenLoaded('officeSpaces')),
        ];
    }
}
