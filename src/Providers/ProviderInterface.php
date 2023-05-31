<?php

namespace CloudCastle\SmsServices\Providers;

use CloudCastle\SmsServices\Response;

interface ProviderInterface
{

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

    );

    public function __destruct();

    /**
     * Приведение номера телефона к требуемому формату
     * @param string $phone Номер телефона
     * @return string
     */
    public static function phoneFormat(string $phone): string;

    /**
     * Отправить сообщение на номер(а) $phones с текстом $message
     * @param array|string $phones Номер телефона или массив номеров
     * @param string $message Текст сообщения
     * @return array
     */
    public function sendSms(array|string $phones, string $message): array;

    /**
     * Дозвон с кодом абоненту $phone
     * @param string $phone Номер телефона абонента
     * @return array
     */
    public function call(string $phone): array;
}