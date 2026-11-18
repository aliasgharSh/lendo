<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => $this->type,
            "amount" => $this->amount,
            "before_balance" => $this->before_balance,
            "after_balance" => $this->after_balance,
            "created_at" => $this->created_at,
        ];
    }
}
