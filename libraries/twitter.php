<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Simple Twitter addon
 *
 * Works best with APC installed
 *
 * @package		MojoMotor
 * @subpackage	Addons
 * @author		eoinmcg
 * @link		http://www.starfishwebconsulting.co.uk/mojomotor
 */
class Twitter
{

	var $addon;
	var $addon_verion = '0.1';
	
	/**
	 * __construct
	 *
	 * @access public
	 * @return void
	 */ 
	public function __construct()
	{
		$this->addon =& get_instance();
		
	}


	function show_tweets($template_data = array())
	{
	
		$user = $template_data['parameters']['user'];
		$count = $template_data['parameters']['count'];
	
		$user = urlencode($user);
		$url = "http://twitter.com/statuses/user_timeline/$user.xml?count=$count";

		if ( $cached = $this->cache_check($url) )
		{
			return $cached;
		}

		if ( !$xml = simplexml_load_file($url))
		{
			return FALSE;
		}
		
		$tweets = '<ul class="tweets">';
		
	
		foreach ( $xml->status as $status )
		{
	
			// convert this to a unixtimestamp
			$date = strtotime( (string) $status->created_at);
	
			$tweets .= '<li>';
			$tweets .= (string) $status->text;
			$tweets .= '<span class="time">'.date('d M H:i', $date).'</span>';
			$tweets .= '</li>';
			
		}
		
		$tweets .= '</ul>';
		
		$this->cache_save($url, $tweets);
		
		return $tweets;
		
	}
	
	
	/**
	 * check_cache
	 *
	 * @access public
	 * @param string
	 * @return mixed
	 */ 
	public function cache_check($url)
	{
	
		if ( !function_exists('apc_fetch') )
		{
			return FALSE;
		}
	
		$key = md5($url);

		$tweets = apc_fetch($url);
		
		if( $tweets )
		{
			return $tweets;
		}
		else
		{
			return FALSE;
		}
		
	}



	/**
	 * cache_save
	 *
	 * @access public
	 * @param string
	 * @return void
	 */ 
	public function cache_save($key, $data)
	{
	
		if ( !function_exists('apc_fetch') )
		{
			return FALSE;
		}
	
		apc_store($key, $data, 600);
	}

}

/* End of file twitter.php */
/* Location: system/mojomotor/third_party/twitter/libraries/robots.php */
