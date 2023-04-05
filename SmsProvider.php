<?php

namespace CloudCastle\SmsServices;

use CloudCastle\SmsServices\Providers\ProviderInterface;
use CloudCastle\SmsServices\Providers\SmscRu;
use CloudCastle\SmsServices\Providers\SmsRu;

final class SmsProvider
{
    /**
     * Список доступных провайдеров смс сервисов
     * @var array
     */
    private static array $providers = [
        'smsc.ru' => SmscRu::class,
        'sms.ru' => SmsRu::class,
    ];

    /**
     * Имя сервиса провайдера смс рассылки
     * @var string|false
     */
    private string|false $provider;

    /**
     * Токен подключения смс сервиса
     * @var string|false
     */
    private string|false $token;

    /**
     * Логин личного кабинета смс сервиса
     * @var string|false
     */
    private string|false $login;

    /**
     * Пароль личного кабинета смс сервиса
     * @var string|false
     */
    private string|false $password;

    /**
     * Режим тестирования сервиса
     * @var bool
     */
    private bool $test;

    /**
     * Имя отправителя в ЛК поставщика услуг
     * @var string|false
     */
    private string|false $from;

    public function __construct()
    {
        $this->token = env('SMS_TOKEN', false);
        $this->login = env('SMS_LOGIN', false);
        $this->password = env('SMS_PASSWORD', false);
        $this->provider = env('SMS_PROVIDER', false);
        $this->test = env('SMS_TEST', false);
        $this->from = env('SMS_FROM', false);
    }

    /**
     * Получить провайдера СМС сервиса
     * @return ProviderInterface|false
     */
    public function getProvider(): ProviderInterface|false
    {
        if (isset(self::$providers[$this->provider])) {
            $provider = self::$providers[$this->provider];
            return new $provider(
                $this->login,
                $this->password,
                $this->token,
                $this->test,
                $this->from
            );
        }
        return false;
    }

}
