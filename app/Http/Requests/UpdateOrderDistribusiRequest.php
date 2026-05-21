<?php

namespace App\Http\Requests;

class UpdateOrderDistribusiRequest extends StoreOrderDistribusiRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah order.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStock() === true;
    }
}
