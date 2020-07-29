<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Medleycache
{
	private $token;
	private $chat_id;

	function __construct()
	{
		$CI =& get_instance();
		$this->CI = $CI;
		$this->CI->load->config('medleycache');
		$this->CI->load->driver('cache', array('adapter' => $this->CI->config->item('metacache'), 'backup' => 'dummy'));
		$this->CI->load->library(['tgapi/Tgapi']);
		$this->token = $this->CI->config->item('telegram')['bot']['token'];
		$this->chat_id = $this->CI->config->item('telegram')['channel'];
		$this->telegram = new tgapi($this->token);
	}

	public function save($cachekey, $data, $ttl = 31536000)
	{
		$contents = array(
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		);
		if ($msg = $this->_saveToTelegram(serialize($contents))){
			if($msg->ok){
				$this->CI->cache->save($cachekey, $msg->result->document->file_id, $ttl);
				return TRUE;
			}
			else{
				die(var_dump($msg));
			}
		}
		return FALSE;
	}

	public function get($cachekey)
	{
		/*get Telegram's file id */
		if( ! $fileKey = $this->CI->cache->get($cachekey))
		{
			return FALSE;
		}
		$cachedData = $this->_getFromTelegram($fileKey);
		if ($cachedData['ttl'] > 0 && time() > $cachedData['time'] + $cachedData['ttl'])
		{
			$this->CI->cache->delete($cachekey);
			return FALSE;
		}
		return $cachedData['data'];
	}
	/*
	 * Saves file to Telegram
	 * @input filepath
	 * @return tg id
	 */
	private function _saveToTelegram($contents)
	{
		$tmpFile = FCPATH . 'application/cache/'. substr(md5(time() . rand(1000,10000)), rand(2,10), 8);
		file_put_contents($tmpFile, $contents);
		$toTG = $this->telegram->sendDocument([ 'chat_id' => $this->chat_id, 'document' => $tmpFile ]);
		unlink($tmpFile);
		return $toTG;
	}

	private function _getFromTelegram($fileKey)
	{
		if($fileKey) {
			return $this->telegram->getFile(['file_id' => $fileKey]);
		}
		return false;
	}

}
