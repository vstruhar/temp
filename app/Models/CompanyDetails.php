<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use App\Services\CountriesService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperCompanyDetails
 */
class CompanyDetails extends Model implements AuditableInterface
{
    use HasFactory, Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'country',
        'address',
        'postal_code',
        'city',
        'phone',
        'fax',
        'web',
        'register',
        'organization_id',
        'tax',
        'value_added_tax',
        'tax_profile',
        'default_tax_percentage',
        'logo',
        'signature',
        'invoice_color',
    ];

    protected $casts = [
        'tax_profile' => 'integer',
    ];

    protected $appends = [
        'country_label',
        'logo_url',
    ];

    /**
     * @return string
     */
    public function getCountryLabelAttribute(): string
    {
        return $this->country ? CountriesService::getName($this->country, false) : '';
    }

    /**
     * @return string
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo !== null && Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isTaxable(): bool
    {
        return $this->tax_profile > 0;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('company_details_only', function (Builder $builder) {
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
    public function team()
    {
        return $this->belongsTo(Team::class, 'company_id');
    }
}
