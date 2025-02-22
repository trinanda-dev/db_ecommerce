<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePenggunaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nomor_hp' => $this->nomor_hp,
            'nama_toko' => $this->nama_toko,
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : asset('storage/default.png'),
        ];
    }
}
