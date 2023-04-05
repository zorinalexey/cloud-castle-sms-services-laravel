<?php

namespace CloudCastle\SmsServices\Models;

use CloudCastle\SmsServices\Models\Traits\GeneratePrimaryUuid;
use Illuminate\Database\Eloquent\Model;

abstract class UuidModel extends Model
{
    use GeneratePrimaryUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $model) {
            $model->generatePrimaryUuid();
        });
    }
}
