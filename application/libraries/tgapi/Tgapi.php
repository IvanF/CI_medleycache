<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TGApi {

    private $token;
	private $api_url = 'https://api.telegram.org/file/bot';

    function __construct($token = null)
    {
        $this->token = $token;
    }

	public function sendDocument($data)
	{
		return json_decode($this->_curl('sendDocument', $data));
	}

	public function sendMessage($data)
	{
		return $this->_curl('sendMessage', $data);
	}

	public function getMessage($data)
	{
		return $this->_curl('getMessage', $data);
	}

	public function getFile($data)
	{
		$file_path = json_decode($this->_curl('getFile', $data))->result->file_path;
	    return unserialize($this->_downloadFile($file_path));

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
		return $result;
	}

    private function _downloadFile($file_path)
    {
        $url = 'https://api.telegram.org/file/bot' . $this->token . '/' . $file_path;
        return file_get_contents($url);
    }

}
