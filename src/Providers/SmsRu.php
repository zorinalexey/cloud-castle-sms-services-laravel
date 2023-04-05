<?php

namespace CloudCastle\SmsServices\Providers;

use CloudCastle\SmsServices\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 *
 */
final class SmsRu extends AbstractProvider implements ProviderInterface
{
    /**
     * Адрес сервера для отправки сообщения
     */
    private const APP_URL = 'https://sms.ru';

    /**
     * Отправить сообщение на номер(а) $phones с текстом $message
     * @param array|string $phones Номер телефона или массив номеров
     * @param string $message Текст сообщения
     * @return array
     */
    public function sendSms(array|string $phones, string $message): array
    {
        $data = $this->setRequestParams($phones, $message);
        $url = self::APP_URL.'/sms/send';
        $response = Http::get($url, $data)->json();
        $response->message = $message;
        $response->client_ip = $data['ip'];
        return $this->result($response);
    }

    /**
     * @param array|string $phones
     * @param string|null $message
     * @param bool $is_msg
     * @return array
     */
    private function setRequestParams(array|string $phones, string|null $message = null, bool $is_msg = true): array
    {
        if ($this->token) {
            $data['api_id'] = $this->token;
        } elseif ($this->login && $this->password) {
            $data['login'] = $this->login;
            $data['password'] = $this->password;
        }
        if ($message) {
            $data['msg'] = $message;
        }
        if($is_msg){
            $data['to'] = $this->getPhones($phones);
            $data['json'] = 1;
            if ($this->from) {
                $data['from'] = $this->from;
            }
        }else{
            $data['phone'] = $this->getPhones($phones);
        }
        $data['ip'] = request()->ip();
        return $data;
    }

    /**
     * @param array|string $phones
     * @return string
     */
    private function getPhones(array|string $phones): string
    {
        $str = '';
        if (is_string($phones)) {
            $str = self::phoneFormat($phones);
        }
        if (is_array($phones)) {
            foreach ($phones as $phone) {
                $str .= self::phoneFormat($phone) . ',';
            }
            return trim($str, ',');
        }
        return $str;
    }

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
     * Дозвон с кодом абоненту $phone
     * @param string $phone Номер телефона абонента
     * @return array
     */
    public function call(string $phone): array
    {
        $data = $this->setRequestParams($phone, null, false);
        $url = self::APP_URL.'/code/call';
        $response = Http::get($url, $data)->json();
        $response->client_ip = $data['ip'];
        $response->phone = $data['phones'];
        return $this->result($response);
    }

    /**
     * @param mixed $response
     * @param bool $is_msg
     * @return array
     */
    private function result(mixed $response, bool $is_msg = true): array
    {
        $data = [];
        if($response && mb_strtoupper($response->status) === 'OK'){
            if($is_msg){
                foreach ($response->sms as $phone=>$msg_data){
                    $result = new Response();
                    $result->type = 'message';
                    $result->provider_class = self::class;
                    $result->provider_name = self::APP_URL;
                    $result->client_ip = $response->client_ip;
                    $result->balance = $response->balance;
                    $result->phone = $phone;
                    if(mb_strtoupper($msg_data->status) === 'OK'){
                        $result->status = 'send';
                        $result->message_id = $msg_data->sms_id;
                    }else{
                        $result->status = 'failed';
                        $result->message = $msg_data->status_text;
                    }
                    $data[] = $result;
                }
            }else{
                $result = new Response();
                $result->provider_class = self::class;
                $result->provider_name = self::APP_URL;
                $result->client_ip = $response->client_ip;
                $result->balance = $response->balance;
                $result->type = 'call';
                $result->phone = $response->phone;
                if(mb_strtoupper($response->status) === 'OK'){
                    $result->status = 'send';
                    $result->message_id = $response->call_id;
                    $result->message = $response->code;
                }else{
                    $result->status = 'failed';
                    $result->message = $response->status_text;
                }
                $data[] = $result;
            }
        }
        return $data;
    }


    public function __destruct()
    {
        $log = env('SMS_LOG', false);
        if($log){
            Log::info('SMS provider info : '.__CLASS__, (array)$this);
        }
    }

}