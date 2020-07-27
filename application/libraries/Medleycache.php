<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Medleycache
{

	private $token;
	private $chat_id;

	function __construct()
	{
		$CI =& get_instance();
		$this->CI = $CI;
		$this->CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$this->CI->load->config('medleycache');
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
			$message_id = $msg->result->message_id; //int starting from first message on this channel
			$this->CI->cache->save($cachekey, $message_id);
			return TRUE;
		}
		return FALSE;
	}

	/*
	 * Saves file to Telegram
	 * @input filepath
	 * @return tg id
	 */
	private function _saveToTelegram($contents)
	{
		$document = '/tmp/genres.csv';
		return $this->telegram->sendDocument([ 'chat_id' => $this->chat_id, 'document' => $document ]);
	}


}
