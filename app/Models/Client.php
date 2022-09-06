<?php

namespace App\Models;

use App\Services\CountriesService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperClient
 */
class Client extends Model implements AuditableInterface
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'name',
        'address',
        'organization_id',
        'postal_code',
        'email',
        'tax',
        'phone',
        'city',
        'value_added_tax',
        'fax',
        'country',
        'number_account',
        'bank_code',
        'iban',
        'swift',
        'contacts',
        'shipping_address',
    ];

    protected $casts = [
        'contacts'         => 'array',
        'shipping_address' => 'array',
    ];

    protected $appends = ['country_label'];

    /**
     * @return string
     */
    public function getCountryLabelAttribute(): string
    {
        return CountriesService::getName($this->country, false);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('company_clients_only', function (Builder $builder) {
            if (auth()->check()) {
                $builder->whereIn('company_id', auth()->user()->allTeams()->pluck('id'));
            }
        });
    }

    /**
     * Get the company that the info belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Team::class, 'company_id');
    }

    /**
     * @param null     $includeId
     * @param string[] $columns
     * @param int|null $companyId
     *
     * @return array
     */
    public static function dropdownOptions($includeId = null, array $columns = ['id', 'name'], ?int $companyId = null): array
    {
        $items = self::select($columns)
                     ->where('company_id', $companyId ?: auth()->user()->currentTeam->id)
                     ->get();

        if ($includeId && !$items->contains('id', $includeId)) {
            $items->push(
                self::withTrashed()
                    ->select($columns)
                    ->where('id', $includeId)
                    ->first()
            );
        }

        return $items->sortBy('name')
                     ->when(count($columns) === 2, function ($query) {
                         return $query->map(static function (Client $client) {
                             return [
                                 'value' => $client->id,
                                 'label' => $client->name,
                             ];
                         });
                     })
                     ->values()
                     ->toArray();
    }
}
