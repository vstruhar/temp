<?php

namespace App\Models;

use App\Casts\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperDocumentPayment
 */
class DocumentPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = [
        'payment_date',
    ];

    protected $casts = [
        'amount' => Currency::class,
    ];

    protected $fillable = [
        'document_id',
        'created_by_user_id',
        'payment_method',
        'amount',
        'comment',
        'payment_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
