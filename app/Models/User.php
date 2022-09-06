<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements AuditableInterface
{
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use Auditable;

    //use HasApiTokens;
    //use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password', 'active', 'language', 'last_login_at', 'registered_from',
    ];

    protected $dates = [
        'last_login_at',
    ];

    protected $auditExclude = [
        'created_at', 'updated_at', 'last_login_at', 'registered_from',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active'            => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @param  iterable|string  $permission
     *
     * @return bool
     */
    public function hasCurrentTeamPermission($permission)
    {
        return $this->hasTeamPermission($this->currentTeam, $permission);
    }

    /**
     * @return bool
     */
    public function ownsCurrentCompany(): bool
    {
        return $this->currentTeam && $this->ownsTeam($this->currentTeam);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultBankAccount(): HasOne
    {
        return $this->hasOne(DefaultItem::class)
                    ->where('user_id', $this->id)
                    ->where('company_id', $this->current_team_id)
                    ->where('type', DefaultItem::TYPE_BANK_ACCOUNT);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultIssuedBy(): HasOne
    {
        return $this->hasOne(DefaultItem::class)
                    ->where('user_id', $this->id)
                    ->where('company_id', $this->current_team_id)
                    ->where('type', DefaultItem::TYPE_ISSUED_BY);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function printedDocuments(): HasMany
    {
        return $this->hasMany(PrintedDocument::class)
                    ->where('company_id', $this->current_team_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function defaultDocumentNumbers(): HasManyThrough
    {
        return $this->hasManyThrough(DocumentNumber::class, DefaultItem::class, 'user_id', 'id', 'id', 'default_id')
                    ->where('user_id', $this->id)
                    ->where('default_items.company_id', $this->current_team_id)
                    ->where('default_items.type', DefaultItem::TYPE_DOCUMENT_NUMBER);
    }

    /**
     * @return String
     */
    public function getCompanyName(): string
    {
        return auth()->user()->currentTeam->companyDetails->name ?? auth()->user()->currentTeam->name;
    }

    /**
     * @param String|null $name
     *
     * @return String
     */
    public function getCompanyNameInSnakeCase(?string $name = null): string
    {
        return Str::of($name ?: $this->getCompanyName())->lower()->replace(['.', ','], '')->snake();
    }
}
