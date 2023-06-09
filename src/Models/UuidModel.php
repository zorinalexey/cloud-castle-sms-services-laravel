<?php

namespace CloudCastle\SmsServices\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

abstract class UuidModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    private function generatePrimaryUuid(): static
    {
        if (!$this->getKey())
            $this->setAttribute($this->getKeyName(), (string)Uuid::uuid6());
        return $this;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $model) {
            $model->generatePrimaryUuid();
        });
    }
}
