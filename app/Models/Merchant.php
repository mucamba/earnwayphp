<?php

namespace App\Models;

use App\Enums\MerchantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Merchant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'site_url',
        'currency_id',
        'business_logo',
        'business_description',
        'business_email',
        'fee',
        'status',
        'merchant_key',
        'api_key',
        'api_secret',
    ];

    //    protected $hidden = ['api_key', 'api_secret'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'fee'    => 'double',
        'status' => MerchantStatus::class,
    ];

    public function scopeActive()
    {
        return $this->where('status', MerchantStatus::APPROVED);
    }

    // App\Models\Merchant.php

    public function scopeFilter($query, Request $request)
    {
        $query->when($request->filled('type'), function ($q) use ($request) {
            $q->where('status', $request->type);
        });

        $query->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('daterange'), function ($q) use ($request) {
            $dateRange = explode(',', $request->daterange);

            if (count($dateRange) === 2) {
                [$startDate, $endDate] = $dateRange;

                $q->whereBetween('created_at', [
                    \Carbon\Carbon::parse(trim($startDate))->startOfDay(),
                    \Carbon\Carbon::parse(trim($endDate))->endOfDay(),
                ]);
            }
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhere('merchant_key', 'like', "%{$search}%")
                    ->orWhere('business_email', 'like', "%{$search}%")
                    ->orWhere('site_url', 'like', "%{$search}%");
            });
        });

        return $query;
    }

    public function getBusinessLogoAttribute(?string $value): string
    {
        return $value ?? '/general/static/default/shop.png';
    }

    /**
     * Get the API Key only when the merchant is APPROVED.
     */
    public function getApiKeyAttribute(?string $value): string
    {
        return $this->status === MerchantStatus::APPROVED
            ? ($value ?? __('No API Key Generated'))
            : __('Your API Key will be available after approval.');
    }

    /**
     * Get the API Secret only when the merchant is APPROVED.
     */
    public function getApiSecretAttribute(?string $value): string
    {
        return $this->status === MerchantStatus::APPROVED
            ? ($value ?? __('No API Secret Generated'))
            : __('Your API Secret will be available after approval.');
    }

    /**
     * Get the user that owns the merchant.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the currency associated with the merchant.
     *
     * @return BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
