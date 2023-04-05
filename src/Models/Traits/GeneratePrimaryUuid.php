<?php

namespace CloudCastle\SmsServices\Models\Traits;

use Ramsey\Uuid\Uuid;

trait GeneratePrimaryUuid
{
    public function generatePrimaryUuid(): static
    {
        if (!$this->getKey())
            $this->setAttribute($this->getKeyName(), (string)Uuid::uuid6());
        return $this;
    }
}
