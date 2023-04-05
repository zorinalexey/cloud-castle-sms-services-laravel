<?php

namespace CloudCastle\SmsServices\Providers;

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
}