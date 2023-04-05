<?php

namespace CloudCastle\SmsServices\Providers;

use CloudCastle\SmsServices\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 *
 */
final class SmscRu extends AbstractProvider implements ProviderInterface
{

    /**
     * Адрес сервера для отправки сообщения
     */
    private const APP_URL = 'https://smsc.ru';

    /**
     * Приведение номера телефона к требуемому формату
     * @param string $phone Номер телефона
     * @return string
     */
    public static function phoneFormat(string $phone): string
    {
        return '7'.preg_replace('~^(\+7|8)(\D)$~u', '', $phone);
    }

    /**
     * Отправить сообщение на номер(а) $phones с текстом $message
     * @param array|string $phones Номер телефона или массив номеров
     * @param string $message Текст сообщения
     * @return array
     */
    public function sendSms(array|string $phones, string $message): array
    {
        $data = $this->setRequestParams($phones, $message, true);
        $url = self::APP_URL.'/sys/send.php';
        $response = Http::get($url, $data);
        return $this->result($response->json(), $data);
    }

    /**
     * Дозвон с кодом абоненту $phone
     * @param string $phone Номер телефона абонента
     * @return array
     */
    public function call(string $phone): array
    {
        $data = $this->setRequestParams($phone, null, false);
        $url = self::APP_URL.'/sys/send.php';
        $response = Http::get($url, $data);
        return $this->result($response->json(), $data);
    }

    /**
     * @param array|string $phones
     * @param string|null $message
     * @param bool $is_msg
     * @return array
     */
    private function setRequestParams(array|string $phones, string|null $message = null, bool $is_msg = true):array
    {
        $data['login'] = $this->login;
        $data['psw'] = $this->password;
        $data['phones'] = $this->getPhones($phones);
        $data['cost'] = 3;
        $data['fmt'] = 3;
        $data['userip'] =  request()->ip();
        $data['err'] = 1;
        $data['op'] = 1;
        $data['charset'] = 'utf-8';
        if($is_msg){
            $data['op'] = 1;
            $data['mes'] = $message;
            $data['call'] = 0;
        }else{
            $data['mes'] = 'code';
            $data['call'] = 3;
        }
        if($this->from){
            $data['sender'] = $this->from;
        }
        if($data['login'] && $data['psw']){
            return $data;
        }
        return [];
    }

    /**
     * @param array|string $phones
     * @return string
     */
    private function getPhones(array|string $phones):string
    {
        $str = '';
        if(is_string($phones)){
            $str = self::phoneFormat($phones);
        }
        if(is_array($phones)){
            foreach ($phones as $phone){
                $str.= self::phoneFormat($phone).';';
            }
        }
        return trim($str, ';');
    }

    /**
     * @param mixed $response
     * @param array $data
     * @return array
     */
    private function result(mixed $response, array $data):array
    {
        $results = [];
        $result = new Response();
        $result->provider_class = self::class;
        $result->provider_name = self::APP_URL;
        return $results;
    }


    public function __destruct()
    {
        $log = env('SMS_LOG', false);
        if($log){
            Log::info('SMS provider info : '.__CLASS__, (array)$this);
        }
    }

}