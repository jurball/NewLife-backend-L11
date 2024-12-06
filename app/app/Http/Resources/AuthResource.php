<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // выводим только то что мы хотим в джесон
    public function toArray(Request $request): array
    {
        return [
            'success'=>true,
            'message'=>'Success',
//            'token'=>
        ];
    }
}
