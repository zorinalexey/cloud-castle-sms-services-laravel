<?php

namespace CloudCastle\SmsServices;

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
    public float|null $type = null;

    public function save():static
    {
        return $this;
    }

    public function log(bool $log = true):static
    {
        if($log){
            Log::info('SMS provider info : '.__CLASS__, (array)$this);
        }
        return $this;
    }

    public function __destruct()
    {
        $log = env('SMS_LOG', false);
        if($log){
            Log::info('SMS provider info : '.__CLASS__, (array)$this);
        }
    }
}