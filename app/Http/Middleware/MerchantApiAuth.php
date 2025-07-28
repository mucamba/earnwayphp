<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;

class MerchantApiAuth
{
    /**
     * Handle an incoming request.
     *
     * Expected headers:
     * - X-Merchant-Key
     * - X-API-Key
     * - X-Signature (HMAC signature of the JSON payload using the merchant's API secret)
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey      = $request->header('X-API-KEY');
        $merchantKey = $request->header('X-MERCHANT-KEY');

        $merchant = Merchant::where('api_key', $apiKey)
            ->where('merchant_key', $merchantKey)
            ->first();

        if (! $merchant) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['merchant' => $merchant]);

        return $next($request);
    }
}
