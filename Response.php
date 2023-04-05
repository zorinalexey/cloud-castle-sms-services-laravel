<?php

namespace CloudCastle\SmsServices;

use CloudCastle\SmsServices\Models\Sms;
use Illuminate\Support\Facades\Log;

final class Response
{
    /**
     * Класс провайдера поставщика услуг
     * @var string|null
     */
    public string|null $provider_class = null;

    /**
     * Наименование провайдера поставщика услуг
     * @var string|null
     */
    public  string|null $provider_name = null;

    /**
     * id отправленного сообщения
     * @var string|null
     */
    public string|null $message_id = null;

    /**
     * Текст сообщения
     * @var string|null
     */
    public string|null $message = null;

    /**
     * Статус отправки сообщения
     * @var string|null
     */
    public string|null $status = null;

    /**
     * Номер телефона на который было отправлено сообщение
     * @var string|null
     */
    public string|null $phone = null;

    /**
     * IP адрес клиента
     * @var string|null
     */
    public string|null $client_ip = null;

    /**
     * Баланс
     * @var float|null
     */
    public float|null $balance = null;

    /**
     * Тип сообщения
     * @var float|null
     */
    public string|null $type = null;

    public function save():static
    {
        $sms = new Sms();
        foreach ($this as $field=>$value){
            $sms->$field = $value;
        }
        $sms->save();
        $this->log(env('SMS_LOG', false));
        return $this;
    }

    public function log(bool $log = true):static
    {
        if($log){
            Log::info('SMS response info : '.__CLASS__, (array)$this);
        }
        return $this;
    }

}
