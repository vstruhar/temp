<?php

namespace App\Models;

use AjCastro\Searchable\Searchable;
use App\Casts\Currency;
use App\Casts\Decimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * @mixin IdeHelperDocumentItem
 */
class DocumentItem extends Model implements AuditableInterface
{
    use HasFactory, Auditable, Searchable;

    protected $casts = [
        'price'                    => Currency::class,
        'price_with_tax'           => Currency::class,
        'discount_amount'          => Currency::class,
        'discount_percent'         => Currency::class,
        'quantity'                 => Decimal::class,
        'calculated_from_subtotal' => 'boolean',
    ];

    protected $fillable = [
        'document_id',
        'name',
        'description',
        'price',
        'price_with_tax',
        'tax',
        'quantity',
        'unit',
        'image',
        'discount_amount',
        'discount_percent',
        'calculated_from_subtotal',
    ];

    public const COLUMNS_AFFECTING_PDF = [
        'name', 'description', 'calculated_from_subtotal', 'price_with_tax', 'price', 'tax', 'quantity', 'unit',
        'discount_amount', 'discount_percent',
    ];

    protected $searchable = [
        'columns' => [
            'document_items.name',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : null;
    }
}
