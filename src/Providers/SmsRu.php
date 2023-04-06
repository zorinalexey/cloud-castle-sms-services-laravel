<?php

namespace CloudCastle\SmsServices\Providers;

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
        $response = Http::get($url, $data)->object();
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
        if($is_msg){
            $data['to'] = $this->getPhones($phones);
            $data['json'] = 1;
            if ($this->from) {
                $data['from'] = $this->from;
            }
            $data['msg'] = $message;
        }else{
            $data['phone'] = $this->getPhones($phones);
        }
        if(env('SMS_TEST', false)){
            $data['test'] = 1;
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
       return '7' . preg_replace('~^(\+7|8)?(\D)$~u', '', $phone);
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
        $response = Http::get($url, $data)->object();
        $response->client_ip = $data['ip'];
        $response->phone = $data['phone'];
        return $this->result($response, false);
    }

    /**
     * @param mixed $response
     * @param bool $is_msg
     * @return array
     */
    private function result(mixed $response, bool $is_msg = true): array
    {
        $data = [];
        if($is_msg) {
            if ($response && mb_strtoupper($response->status) === 'OK') {
                foreach ($response->sms as $phone => $msg_data) {
                    $result = $this->getResultObj($response, 'message');
                    $result->balance = $response->balance;
                    $result->phone = $phone;
                    if (mb_strtoupper($msg_data->status) === 'OK') {
                        $result->status = 'send';
                        $result->message_id = $msg_data->sms_id;
                    } else {
                        $result->status = 'failed';
                        $result->message = $msg_data->status_text;
                    }
                    $data[] = $result;
                }
            }
        }else{
            $result = $this->getResultObj($response, 'call');
            $result->phone = $response->phone;
                if($response && mb_strtoupper($response->status) === 'OK'){
                    $result->status = 'send';
                    $result->balance = $response->balance;
                    $result->message_id = $response->call_id;
                    $result->message = $response->code;
                }else{
                    $result->status = 'failed';
                    $result->message = $response->status_text;
                }
                $data[] = $result;
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
