<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TGApi {

    private $token;

    function __construct($token = null)
    {
        $this->token = $token;
    }

    public function getFile($data)
    {
		return $this->_curl('getFile', $data);
    }

    public function sendDocument($data)
    {
		//return $this->_curl('sendDocument', $data);

		$url = 'https://api.telegram.org/bot' . $this->token . '/sendDocument';
		$ch = curl_init($url);
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.17) Gecko/2009122116 Firefox/3.0.17");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// Указываем CURL, что будем отправлять POST запрос
		curl_setopt($ch, CURLOPT_POST, true);

		// Передаем массив с полями формы, где field1, field2 - имена тегов, а value1, value2 - значения тегов
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id'   => $data['chat_id'], 'document' => new CURLFile($data['document'])));

		$result = curl_exec($ch); // выполняем запрос curl

		curl_close($ch);

		return json_decode($result);
    }

	public function sendMessage($data)
	{
		return $this->_curl('sendMessage', $data);
	}

	public function getMessage($data)
	{
		return $this->_curl('getMessage', $data);
	}

	private function _curl($method, $postfields)
	{
		foreach ($postfields as $key => $val) {
			if($key == 'document') {
				$val = new CURLFile($val);
				$postfields[$key] = $val;
			}
		}
		$url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;
		$ch = curl_init($url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.17) Gecko/2009122116 Firefox/3.0.17");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$result = curl_exec($ch);
		curl_close($ch);
		die(var_dump($result));
		return json_decode($result);
	}
}
