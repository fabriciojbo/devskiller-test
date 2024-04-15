<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
           'data' => [
               'id' => $this->id,
               'name' => $this->name,
               'price' => $this->price,
           ]
        ];
    }
}
