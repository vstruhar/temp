<?php

namespace App\Models;

use App\Services\DocumentNumberService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperDocumentNumber
 */
class DocumentNumber extends Model implements AuditableInterface
{
    use HasFactory, Auditable, SoftDeletes;

    public const TYPE_INVOICE          = 'invoice';
    public const TYPE_PROFORMA_INVOICE = 'proforma-invoice';
    public const TYPE_CREDIT_NOTE      = 'credit-note';
    public const TYPE_QUOTATION        = 'quotation';
    public const ALL_TYPES             = [self::TYPE_INVOICE, self::TYPE_PROFORMA_INVOICE, self::TYPE_CREDIT_NOTE, self::TYPE_QUOTATION];

    public const PERIOD_YEARLY  = 'yearly';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_DAILY   = 'daily';
    public const PERIOD_NONE    = 'none';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'period',
        'format',
        'is_default',
        'current_counter',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('company_invoice_numbers_only', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('document_numbers.company_id', auth()->user()->current_team_id);
            }
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'company_id');
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
     * @param $type
     *
     * @return DocumentNumber
     */
    public static function firstDefault($type): DocumentNumber
    {
        $query = self::where('company_id', auth()->user()->current_team_id)
                     ->where('type', $type);

        $first = $query->where('is_default', true)->first();

        return $first ?? $query->first();
    }

    /**
     * @param null                            $type
     * @param array                           $with
     * @param int|null                        $companyId
     * @param \Illuminate\Support\Carbon|null $date
     *
     * @return array
     */
    public static function dropdownSelect($type = null, array $with = [], ?int $companyId = null, ?Carbon $date = null): array
    {
        $items = self::where('company_id', $companyId ?: auth()->user()->current_team_id)
                     ->when($type !== null, function ($query) use ($type) {
                         $query->where('type', $type);
                     })
                     ->with(['usersDefault'])
                     ->withoutGlobalScope('company_invoice_numbers_only')
                     ->orderBy('name')
                     ->get()
                     ->map(static function ($item) use ($with, $date) {
                         $data = [
                             'value' => $item->id,
                             'label' => $item->name,
                         ];

                         if (in_array('default', $with, true)) {
                             $data['is_default'] = $item->usersDefault
                                 ? $item->usersDefault->default_id === $item->id
                                 : $item->is_default;
                         }
                         if (in_array('next', $with, true)) {
                             $data['next_number'] = DocumentNumberService::getNext($item, $date ?? now());
                         }
                         if (in_array('period', $with, true)) {
                             $data['period'] = $item->period;
                         }
                         if (in_array('type', $with, true)) {
                             $data['type'] = $item->type;
                         }
                         return $data;
                     })
                     ->prepend(['value' => 0, 'label' => __('Custom')]);

        // if there is no default, use owners default
        if (in_array('default', $with, true) && $items->every('is_default', false)) {
            $defaultId = self::where('company_id', $companyId ?: auth()->user()->current_team_id)
                             ->where('is_default', true)
                             ->withoutGlobalScope('company_invoice_numbers_only')
                             ->value('id');

            $items = $items->map(static function ($item) use ($defaultId) {
                return ['is_default' => $item['value'] === $defaultId] + $item;
            });
        }

        return $items->toArray();
    }

    /**
     * @param bool $isDefault
     */
    public function setDefault(bool $isDefault): void
    {
        if ($isDefault) {
            self::where('company_id', auth()->user()->current_team_id)
                ->where('type', $this->type)
                ->update(['is_default' => 0]);

            $this->update(['is_default' => 1]);
        }
    }
}
