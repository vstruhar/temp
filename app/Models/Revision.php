<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperRevision
 */
class Revision extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data', 'restored_at'];

    protected $dates = ['restored_at'];

    protected $casts = [
        'data'        => 'array',
        'restored_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
