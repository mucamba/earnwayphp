<?php

namespace App\Observers;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use Illuminate\Support\Str;

class MerchantObserver
{
    public function creating(Merchant $merchant): void
    {
        // Always set real keys for new merchants
        $merchant->api_key      = Str::random(28);
        $merchant->api_secret   = Str::random(38);
        $merchant->merchant_key = Str::random(12);
        $merchant->status       = MerchantStatus::PENDING;
    }
}
