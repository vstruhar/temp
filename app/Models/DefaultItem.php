<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDefaultItem
 */
class DefaultItem extends Model
{
    public const TYPE_BANK_ACCOUNT = 'bank_account';
    public const TYPE_DOCUMENT_NUMBER = 'document_number';
    public const TYPE_ISSUED_BY = 'issued_by';

    public $timestamps = false;

    protected $fillable = ['user_id', 'company_id', 'type', 'default_id', 'value'];

    /**
     * @return array|null
     */
    public static function getIssuedBy(): ?array
    {
        $value = self::query()
                   ->where('user_id', auth()->id())
                   ->where('company_id', auth()->user()->current_team_id)
                   ->where('type', self::TYPE_ISSUED_BY)
                   ->value('value');

        return $value
            ? json_decode($value, true, 512, JSON_THROW_ON_ERROR)
            : null;
    }
}
