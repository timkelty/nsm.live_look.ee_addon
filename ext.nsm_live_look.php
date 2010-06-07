<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension file, hooks and addon settings
 *
 * @package			NsmLiveLook
 * @version			0.3.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
class Nsm_live_look_ext
{
	/**
	 * Version number of this extension. Should be in the format "x.x.x", with only integers used.	EE use.
	 * @var 		string
	 */
	public $version			= '0.3.0';

	/**
	 * Display name for this extension.
	 * @var			string
	 **/
	public $name			= 'NSM Live Look';

	/**
	 * Description for this extension
	 * @var 		string
	 */
	public $description		= 'Settings for NSM Live Look';

	/**
	 * Link to documentation for this extension. EE use.
	 * @var			string
	 **/
	public $docs_url		= '';

	/**
	 * Defines whether the extension has user-configurable settings.  EE use.
	 * @var			boolean
	 **/
	public $settings_exist	= TRUE;

	/**
	 * Settings
	 * @var			array
	 **/
	public $settings		= array();

	/**
	 * Default site settings
	 * @var			array
	 **/
	public $default_site_settings = array(
		'enabled' => TRUE,
		'channels' => array(),
		'member_groups' => array()
	);

	/**
	 * Default channel settings
	 * @var			array
	 **/
	public $default_channel_settings = array("urls" => array());

	/**
	 * Hooks for the extension
	 * @var			array
	 **/
	public $hooks = array('dummy_hook');

	// ====================================
	// = Delegate & Constructor Functions =
	// ====================================

	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 **/
	function __construct()
	{
		// set the addon id
		$this->addon_id = strtolower(substr(__CLASS__, 0, -4));
	
		// define a constant for the current site_id rather than calling $PREFS->ini() all the time
		if (defined('SITE_ID') == FALSE)
			define('SITE_ID', get_instance()->config->item('site_id'));

		// Load settings
		$EE =& get_instance();
		$EE->load->model('addons_model');
		if($EE->addons_model->extension_installed($this->addon_id))
		{
			// get the settings from our custom settings DB
			$this->settings = $this->_getSettings();
		}
	}

	// ===============================
	// = Hook Functions =
	// ===============================

	public function dummy_hook(){}

	// ===============================
	// = Setting Functions =
	// ===============================

	/**
	 * Render the custom settings form and processes post vars
	 *
	 * @access public
	 * @return The settings form HTML
	 */
	public	function settings_form()
	{
		$EE =& get_instance();
		$EE->lang->loadfile($this->addon_id);

		// Create the variable array
		$vars = array(
			'message' => FALSE,
			'error' => FALSE,
			'addon_id' => $this->addon_id,
			'input_prefix' => __CLASS__,
			'channels' => $EE->channel_model->get_channels()->result()
		);

		// Are there settings posted from the form?
		// PARSE POST TO SETTINGS FORMAT FOR SAVE
		if($data = $EE->input->post(__CLASS__))
		{
			if(! $vars['error'] = validation_errors())
			{
				$new_settings["enabled"] = $data["enabled"];
				if(isset($data["urls"]))
				{
					foreach ($data["urls"] as $url_data)
					{
						$new_settings["channels"][$url_data["channel_id"]]["urls"][] = $url_data;
					}
				}
				$this->settings = $this->_saveSettings($new_settings);
				$EE->session->set_flashdata('message_success', $this->name . ": ". $EE->lang->line('alert.success.extension_settings_saved'));
				$EE->functions->redirect(BASE.AMP.'C=addons_extensions');
				// $this->_eeNotice('alert.success.extension_settings_saved');
				// $vars['message'] = $EE->lang->line('alert.success.extension_settings_saved');
			}
		}
		// PARSE SETTINGS FOR FORM FORMAT
		else
		{
			$data["enabled"] = $this->settings["enabled"];
			if(isset($this->settings["channels"]))
			{
				foreach ($this->settings["channels"] as $channel_id => $channel)
				{
					foreach ($channel["urls"] as $url)
					{
						$data["urls"][] = $url;
					}
				}
			}
		}

		$vars["data"] = $data;

		// Javascript away
		$js = 'NSM_Live_Look = {
				templates : {
					$preview_url: $('. json_encode($EE->load->view('extension_settings/_preview_url_row', array(
						"input_prefix" => __CLASS__,
						"count" => FALSE,
						"row_class" => FALSE,
						"channels" => $vars['channels'],
						"channel_id" => FALSE,
						"row" => array(
							"title" => FALSE,
							"url" => FALSE,
							"channel_id" => FALSE,
							"page_url" => FALSE
						)
					), TRUE)) . ')
				}
			};';

		// add the releases php / js object
		$EE->cp->add_to_foot('<script type="text/javascript" charset="utf-8">'.$js.'</script>');
		$EE->cp->load_package_js('extension_settings');

		// Return the view.
		return $EE->load->view('extension_settings/extension_settings', $vars, TRUE);
	}

	/**
	 * Builds default settings for the site
	 *
	 * @access public
	 * @param int $site_id The site id
	 * @param array The default site settings
	 */
	public function _buildDefaultSiteSettings($site_id = FALSE)
	{
		$EE =& get_instance();
		$default_settings = $this->default_site_settings;

		// No site id, use the current one.
		if(!$site_id)
			$site_id = SITE_ID;

		// Channel preferences
		$channels = $EE->channel_model->get_channels($site_id);
		if ($channels->num_rows() > 0)
		{
			foreach($channels->result() as $channel)
			{
				$default_settings['channels'][$channel->channel_id] = $this->_buildDefaultChannelSettings($channel->channel_id);
			}
		}

		// return settings
		return $default_settings;
	}

	/**
	 * Build the default channel settings
	 *
	 * @access public
	 * @param array $channel_id The target channel
	 * @return array The new channel settings
	 */
	public function _buildDefaultChannelSettings($channel_id)
	{
		return $this->default_channel_settings;
	}


	/**
	 * Get the preview URLS for the channel
	 * 
	 * @access private
	 * @param int $channel_id
	 * @return array The channel preview urls
	 */
	public static function _getChannelPreviewUrls($channel_id, $urls = array())
	{
		$channel_urls = array();
		foreach ($urls as $url)
		{
			if($url["channel_id"] == $channel_id)
				$channel_urls[] = $url;
		}
		return $channel_urls;
	}

	// ===============================
	// = Class and Private Functions =
	// ===============================

	/**
	 * Called by ExpressionEngine when the user activates the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function activate_extension()
	{
		$this->_createSettingsTable();
		$this->settings = $this->_getSettings();
		$this->_registerHooks();
	}

	/**
	 * Called by ExpressionEngine when the user disables the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function disable_extension()
	{
		$this->_unregisterHooks();
	}

	/**
	 * Called by ExpressionEngine updates the extension
	 *
	 * @access public
	 * @return void
	 **/
	public function update_extension($current=FALSE){}

	/**
	 * Sets up and subscribes to the hooks specified by the $hooks array.
	 *
	 * @access private
	 * @param array $hooks A flat array containing the names of any hooks that this extension subscribes to. By default, this parameter is set to FALSE.
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _registerHooks($hooks = FALSE)
	{
		$EE =& get_instance();

		if($hooks == FALSE && isset($this->hooks) == FALSE)
			return;

		if (!$hooks)
			$hooks = $this->hooks;

		$hook_template = array(
			'class'    => __CLASS__,
			'settings' => serialize(array()),
			'version'  => $this->version,
		);

		foreach ($hooks as $key => $hook)
		{
			if (is_array($hook))
			{
				$data['hook'] = $key;
				$data['method'] = (isset($hook['method']) === TRUE) ? $hook['method'] : $key;
				$data = array_merge($data, $hook);
			}
			else
			{
				$data['hook'] = $data['method'] = $hook;
			}

			$hook = array_merge($hook_template, $data);
			$EE->db->insert('exp_extensions', $hook);
		}
	}

	/**
	 * Removes all subscribed hooks for the current extension.
	 * 
	 * @access private
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _unregisterHooks()
	{
		$EE =& get_instance();
		$EE->db->where('class', __CLASS__);
		$EE->db->delete('exp_extensions'); 
	}


	// ======================
	// = Settings Functions =
	// ======================

	/**
	 * The settings table
	 *
	 * @access		private
	 **/
	private static $settings_table = 'nsm_addon_settings';

	/**
	 * The settings table fields
	 *
	 * @access		private
	 **/
	private static $settings_table_fields = array(
		'id'						=> array(	'type'			 => 'int',
												'constraint'	 => '10',
												'unsigned'		 => TRUE,
												'auto_increment' => TRUE,
												'null'			 => FALSE),
		'site_id'					=> array(	'type'			 => 'int',
												'constraint'	 => '5',
												'unsigned'		 => TRUE,
												'default'		 => '1',
												'null'			 => FALSE),
		'addon_id'					=> array(	'type'			 => 'varchar',
												'constraint'	 => '255',
												'null'			 => FALSE),
		'settings'					=> array(	'type'			 => 'mediumtext',
												'null'			 => FALSE)
	);
	
	/**
	 * Creates the settings table table if it doesn't already exist.
	 *
	 * @access		protected
	 * @return		void
	 **/
	protected function _createSettingsTable()
	{
		$EE =& get_instance();
		$EE->load->dbforge();
		$EE->dbforge->add_field(self::$settings_table_fields);
		$EE->dbforge->add_key('id', TRUE);

		if (!$EE->dbforge->create_table(self::$settings_table, TRUE))
		{
			show_error("Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$settings_table);
			log_message('error', "Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$settings_table);
		}
	}

	/**
	 * Get the addon settings
	 *
	 * 1. Load settings from the session
	 * 2. Load settings from the DB
	 * 3. Create new settings and save them to the DB
	 * 
	 * @access private
	 * @param boolean $refresh Load the settings from the DB not the session
	 * @return mixed The addon settings 
	 */
	private function _getSettings($refresh = FALSE)
	{
		$EE =& get_instance();
		$settings = FALSE;

		if (
			// if there are settings in the settings cache
			isset($EE->session->cache[$this->addon_id][SITE_ID]['settings']) === TRUE 
			// and we are not forcing a refresh
			AND $refresh != TRUE
		)
		{
			// get the settings from the session cache
			$return_settings = $EE->session->cache[$this->addon_id][SITE_ID]['settings'];
		}
		else
		{
			$settings_query = $EE->db->get_where(
									self::$settings_table,
									array(
										'addon_id' => $this->addon_id,
										'site_id' => SITE_ID
									)
								);
			// there are settings in the DB
			if ($settings_query->num_rows())
			{
				$settings = json_decode($settings_query->row()->settings, TRUE);
				$this->_saveSettingsToSession($settings);
			}
			// no settings for the site
			else
			{
				$settings = $this->_buildDefaultSiteSettings(SITE_ID);
				$this->_saveSettings($settings);
			}
			
		}
		return $settings;
	}

	/**
	 * Save settings to DB and to the session
	 *
	 * @access private
	 * @param array $settings
	 */
	private function _saveSettings($settings)
	{
		$this->_saveSettingsToDatabase($settings);
		$this->_saveSettingsToSession($settings);
	}

	/**
	 * Save settings to DB
	 *
	 * @access private
	 * @param array $settings
	 * @return array The settings
	 */
	private function _saveSettingsToDatabase($settings)
	{
		$EE =& get_instance();
		$data = array(
			'settings'	=> json_encode($settings),
			'addon_id'	=> $this->addon_id,
			'site_id'	=> SITE_ID
		);
		$settings_query = $EE->db->get_where(
							'nsm_addon_settings',
							array(
								'addon_id' =>  $this->addon_id,
								'site_id' => SITE_ID
							), 1);

		if ($settings_query->num_rows() == 0)
		{
			$query = $EE->db->insert('exp_nsm_addon_settings', $data);
			log_message('info', __METHOD__ . ' Inserting settings: $query => ' . $query);
		}
		else
		{
			$query = $EE->db->update(
							'exp_nsm_addon_settings',
							$data,
							array(
								'addon_id' => $this->addon_id,
								'site_id' => SITE_ID
							));
			log_message('info', __METHOD__ . ' Updating settings: $query => ' . $query);
		}
		return $settings;
		
	}

	/**
	 * Save the settings to the session
	 *
	 * @access private
	 * @param array $settings The settings to push to the session
	 * @return array the settings unmodified
	 */
	private function _saveSettingsToSession($settings){
		$EE =& get_instance();
		$EE->session->cache[$this->addon_id][SITE_ID]['settings'] = $settings;
		return $settings;
	}

	/**
	 * Get the channel settings if the exist or load defaults
	 *
	 * @access private
	 * @param int $channel_id The channel id
	 * @return array the channel settings
	 */
	public function _channelSettings($channel_id){
		return (isset($this->settings["channels"][$channel_id]))
					? $this->settings["channels"][$channel_id]
					: $this->_buildDefaultChannelSettings($channel_id);
	}
}