<?php

namespace CloudCastle\SmsServices\Providers;

use CloudCastle\SmsServices\Response;

/**
 *
 */
abstract class AbstractProvider
{

    /**
     * Токен подключения смс сервиса
     * @var string|false
     */
    protected string|false $token = false;

    /**
     * Логин личного кабинета смс сервиса
     * @var string|false
     */
    protected string|false $login = false;

    /**
     * Пароль личного кабинета смс сервиса
     * @var string|false
     */
    protected string|false $password = false;

    /**
     * Режим тестирования сервиса
     * @var bool
     */
    protected bool $test = false;

    /**
     * Имя отправителя в ЛК поставщика услуг
     * @var string|false
     */
    protected string|false $from;

    /**
     * @param false|string $login Логин личного кабинета смс сервиса
     * @param false|string $password Пароль личного кабинета смс сервиса
     * @param false|string $token Токен подключения смс сервиса
     * @param bool $test Режим тестирования сервиса
     * @param false|string $from Имя отправителя в ЛК поставщика услуг
     */
    public function __construct(
        false|string $login,
        false|string $password,
        false|string $token,
        bool         $test,
        false|string $from,

    )
    {
        $this->login = $login;
        $this->password = $password;
        $this->token = $token;
        $this->test = $test;
        $this->from = $from;
    }

    /**
     * @param mixed $response
     * @param string $type
     * @return Response
     */
    final protected function getResultObj(mixed $response, string $type): Response
    {
        $result = new Response();
        $result->provider_class = static::class;
        $result->provider_name = static::APP_URL;
        $result->client_ip = $response->client_ip;
        $result->type = $type;
        return $response;
    }
}