<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperBankAccount
 */
class BankAccount extends Model implements AuditableInterface
{
    use HasFactory, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'name',
        'currency',
        'number_account',
        'bank_code',
        'iban',
        'swift',
        'default',
        'show_on_documents',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('company_bank_accounts_only', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('company_id', auth()->user()->current_team_id);
            }
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usersDefault(): BelongsTo
    {
        return $this->belongsTo(DefaultItem::class, 'id', 'default_id')
                    ->where('default_items.user_id', auth()->user()->id)
                    ->where('default_items.company_id', auth()->user()->current_team_id);
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
                     ->where('company_id', $companyId ?: auth()->user()->current_team_id)
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
                         return $query->map(static function (BankAccount $bankAccount) {
                             return [
                                 'value' => $bankAccount->id,
                                 'label' => $bankAccount->name,
                             ];
                         });
                     })
                     ->values()
                     ->toArray();
    }
}
