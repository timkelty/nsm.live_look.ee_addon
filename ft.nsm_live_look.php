<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package			NSM
 * @subpackage		LiveLook
 * @version			0.1.0
 * @author			Leevi Graham & Anthony Short <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 * @copyright 		Copyright (c) 2007-2009 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
*
*/
class Nsm_live_look_ft extends EE_Fieldtype
{
	/**
	 * Fieldtype information
	 *
	 * @var array
	 */
	public $info = array
	(
		'name'		=> 'NSM Live Look',
		'version' 	=> '0.1.1'
	);

	/**
	 * The field settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	private $field_type;

	/**
	 * Constructor
	 * 
	 * Calls the parent constructor
	 *
	 * @access public
	 */
	public function __construct()
	{
		if(!class_exists('Nsm_live_look_ext'))
		{
			require_once dirname(__FILE__) . '/ext.nsm_live_look.php';
		}
		
		# Get everything ready for EE using the main addon class
		$this->field_type = Nsm_live_look_ext::id();
		
		parent::EE_Fieldtype();
	}

	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @return String The custom field HTML
	 */
	public function display_field($data)
	{
		$channel_id 	= $this->EE->input->get('channel_id');
		$entry_id 		= $this->EE->input->get('entry_id');
		$preview_url	= '';
		
		if(!class_exists('Nsm_live_look_ext'))
			include(PATH_THIRD.'nsm_live_look/ext.nsm_live_look.php');
			
		$ext = new Nsm_live_look_ext();

		if(!isset($ext->settings['channels'][$channel_id]))
		{
			$ext->settings['channels'][$channel_id] = $ext->_buildDefaultChannelSettings($channel_id);
		}

		# Add the custom field stylesheet to the header 
		$this->EE->cp->load_package_css('custom_field');
		
		# Load the JS for the iframe
		$this->EE->cp->load_package_js('jquery.cookie');
		$this->EE->cp->load_package_js('admin_publish');  

		$this->EE->lang->loadfile('nsm_live_look');

		if($entry_id)
		{
			$preview_url = $ext->settings['channels'][$channel_id]['preview_url'];
			$preview_url = $this->parse_url($preview_url, $entry_id);
		}

		$field_data = array
		(
			'entry_id'			=> $entry_id,
			'field_name' 		=> $this->field_name,
			'channel_id' 		=> $channel_id,
			'preview_url' 		=> $preview_url
		);
		
		return $this->EE->load->view('custom_field', $field_data, TRUE);
	}
	
	/**
	 * Parses a preview url string and replaces each of the tags with the data
	 * from the entry id given as an argument.
	 *
	 * @author Anthony Short
	 * @param	$url		string		The URL string with {tags} to insert entry data into
	 * @param 	$entry_id	int   		The id of the channel entry to base the url on
	 * @return 	string
	 */
	private function parse_url($url,$entry_id)
	{
		if(isset($this->EE->config->config["site_pages"]["uris"][$entry_id]))
		{
			$url = $this->EE->config->config['site_pages']['uris'][$entry_id];
		}
		else
		{
			$query = $this->EE->db
				->from('exp_channel_titles')
				->join('exp_channel_data', 'exp_channel_titles.entry_id = exp_channel_data.entry_id', 'LEFT')
				->where('exp_channel_titles.entry_id', $entry_id)
				->limit(1)
				->get()
				->result_array();
 
			if(count($query) > 0)
			{
				$data = $query[0];
	
				$data['entry_date_day'] 	= date('d', $data['entry_date']);
				$data['entry_date_month'] 	= date('m', $data['entry_date']);
				$data['entry_date_year'] 	= date('Y', $data['entry_date']);
 
				foreach ($data as $key => $value)
				{
					if(strpos($url, LD.$key.RD) !== FALSE)
					{
						$url = str_replace(LD.$key.RD, $value, $url);
					}
				}
			}
		}

		return $this->EE->functions->create_url($url);
	}
}