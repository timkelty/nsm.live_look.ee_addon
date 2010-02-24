<?php

/**
 * Live Look Extension
 *
 * @package LiveLook
 * @version 2.0.0
 * @since 1.0.0
 * @author Anthony Short <http://newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 **/

class Nsm_live_look_ext 
{
	/**
	 * Unique identifier to be used for loading settings and the like.
	 * @version		2.0.0
	 * @since		Version 2.0.0
	 * @access		private
	 * @var			string
	 **/
	public static $addon_id = 'nsm_live_look';

	/**
	 * Display name for this extension.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		private
	 * @var			string
	 **/
	private $addon_name = 'NSM Live Look';

	/**
	 * Name for this extension. EE use.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @var			string
	 **/
	public $name = 'NSM Live Look';

	/**
	 * Description of this extension
	 *
	 * @var string
	 **/
	public $description = 'Allows you to preview an entry within a publish tab';

	/**
	 * Version number of this extension. Should be in the format "x.x.x", with only integers used.	EE use.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @var			string
	 **/
	public $version = '2.0.0';

	/**
	 * Link to documentation for this extension. EE use.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @var			string
	 **/
	public $docs_url = 'http://newism.com.au/live-look/';

	/**
	 * The XML auto-update URL for LG Auto Updater.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @var			string
	 **/
	public $versions_xml = 'http://newism.com.au/live-look/versions.xml';

	/**
	 * Defines the ExpressionEngine hooks that this extension will intercept.
	 * @version		1.0.0
	 * @since		Version 1.0.0
	 * @access		private
	 * @var			mixed	an array of strings that name defined hooks
	 * @see			http://codeigniter.com/user_guide/general/hooks.html
	 **/
 	private $hooks = array('publish_form_entry_data');

	/**
	 * Defines whether the extension has user-configurable settings.  EE use.
	 * @version		1.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @var			string
	 **/
	public $settings_exist = 'y';
	
	
	// ====================================
	// = Delegate & Constructor Functions =
	// ====================================

	/**
	 * PHP5 constructor function.
	 * @version		2.0.0
	 * @since		Version 1.4.0
	 * @access		public
	 * @param		array	$input_settings	an array of settings used to construct a new instance of this class.
	 * @return		void
	 **/
	public function __construct()
	{
		$this->EE =& get_instance();

		include_once(PATH_THIRD.'nsm_live_look/lib/lib.nsm_helpers'.EXT);

		// define a constant for the current site_id rather than calling $PREFS->ini() all the time
		if(defined('SITE_ID') == FALSE)
		{
			define('SITE_ID', $this->EE->config->item('site_id'));
		}

		if($this->EE->addons_model->extension_installed(self::$addon_id))
		{
			// get the settings from our custom settings DB
			$this->settings = $this->_getSettings();
		}
	}

	/**
	 * Called by ExpressionEngine when the user activates the extension.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @return		void
	 **/
	public function activate_extension()
	{
		$this->_createSettingsTable();
		$this->_registerHooks();
	}

	/**
	 * Called by ExpressionEngine when the user disables the extension.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		public
	 * @return		void
	 **/
	public function disable_extension()
	{
		$this->_unregisterHooks();
	}

	/**
	 * Prepares and loads the settings form for display in the ExpressionEngine control panel.
	 * @version		2.0.0
	 * @since		Version 1.5.0
	 * @access		public
	 * @return		void
	 **/
	public function settings_form()
	{
		$this->EE->lang->loadfile('nsm_live_look');
		$this->EE->load->library('form_validation');

		$vars['message'] = FALSE;
		$vars['addon_name'] = $this->addon_name;
		$vars['member_groups'] = $this->EE->member_model->get_member_groups()->result();
		
		// create new settings for new channels
		foreach($vars['channels'] = $this->EE->channel_model->get_channels()->result() as $channel)
		{
			if(!isset($this->settings['channels'][$channel->channel_id]))
			{
				$this->settings['channels'][$channel->channel_id] = $this->_buildDefaultChannelSettings($channel->channel_id);
			}
		}

		$vars['settings'] = $this->settings;

		if($new_settings = $this->EE->input->post(__CLASS__))
		{
			$this->_saveSettings($new_settings);
			$vars['message'] = $this->EE->lang->line('extension_settings_saved_success');
			$vars['settings'] = $new_settings;
		}

//		$this->EE->jquery->ui(BASE.AMP.'C=javascript'.AMP.'M=load'.AMP.'ui=accordion', TRUE);
//		$this->EE->cp->add_to_head('<script type="text/javascript" charset="utf-8" src="/' . SYSDIR . '/expressionengine/third_party/nsm_better_meta/views/assets/js/core.nsm_better_meta.js"></script>');
//		$this->EE->javascript->compile();

		return $this->EE->load->view('form_settings', $vars, TRUE);
	}

	public function publish_form_entry_data($result)
	{
		return $result;
	}
	

	// ===============================
	// = Class and Private Functions =
	// ===============================

	/**
	 * Gets the extension settings
	 * 
	 * Returns the settings from the session.
	 * If the settings are not currently in the session, they are loaded from the database.
	 * If no settings exist in the DB for the site we create them based on the default settings
	 * 
	 * @version		1.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @param		boolean $refresh	if this is set to TRUE, the settings stored in the session will be cleared, and reloaded from the database. Defaults to TRUE.
	 * @return		array		current settings for this extension
	 **/
	protected function _getSettings($refresh = FALSE)
	{		
		$return_settings = FALSE;

		if (
			// if there are settings in the settings cache
			isset($this->EE->session->cache[self::$addon_id][SITE_ID]['settings']) === TRUE 
			// and we are not forcing a refresh
			AND $refresh != TRUE
		)
		{
			// get the settings from the session cache
			$return_settings = $this->EE->session->cache[self::$addon_id][SITE_ID]['settings'];
		}
		else
		{
			// First get the global extension settings
			$settings_query = $this->EE->db->query("SELECT `settings`
													FROM `exp_nsm_addon_settings`
													WHERE `addon_id` = '" . self::$addon_id . "'
													AND `site_id` = '" . SITE_ID . "'
													LIMIT 1");
			// there are settings in the DB
			if ($settings_query->num_rows())
			{
				// set the settings to return
				$return_settings = unserialize($settings_query->row()->settings);
			}
			// there are no settings in the DB for this site
			else
			{
				// build the default settings
				$return_settings = $this->_buildDefaultSettings();
				// save the settings back to the DB
				$this->_saveSettingsToDatabase($return_settings);
			}
			
			// save the settings to the session incase we need them later
			$this->_saveSettingsToSession($return_settings);
		}
		
		// return the settings
		return $return_settings;
	}

	/**
	 * Saves the settings array to the database and the session.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @param		array	$input_settings	an array of settings to save.
	 * @return		void
	 **/
	protected function _saveSettings($input_settings)
	{
		if (isset($input_settings) AND is_array($input_settings))
		{
			$this->_saveSettingsToDatabase($input_settings);
			$this->_saveSettingsToSession($input_settings);
		}
	}

	/**
	 * Saves the specified settings array to the database.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @param		array	$input_settings an array of settings to save to the database.
	 * @return		void
	 **/
	protected function _saveSettingsToDatabase($input_settings)
	{
		// Check if the database has a row for the current site/addon,
		//	if not INSERT, otherwise UPDATE.
		$settings_query = $this->EE->db->get_where('nsm_addon_settings', array('addon_id' =>  self::$addon_id, 'site_id' => SITE_ID), 1);

		if ($settings_query->num_rows() > 0)
		{
			$query = $this->EE->db->update_string(
				'exp_nsm_addon_settings',
				array(
					'settings'	=> serialize($input_settings),
					'addon_id'	=> self::$addon_id,
					'site_id'	=> SITE_ID
				),
				array(
					'addon_id'	=> self::$addon_id,
					'site_id'	=> SITE_ID
				)
			);
		}
		else
		{
			$query = $this->EE->db->insert_string(
				'exp_nsm_addon_settings',
				array(
					'settings'	=> serialize($input_settings),
					'addon_id'	=> self::$addon_id,
					'site_id'	=> SITE_ID
				)
			);
		}

		log_message('error', __METHOD__ . ': $query => ' . $query);

		$this->EE->db->query($query);
	}

	/**
	 * Saves the specified settings array to the session.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @param		array	$input_settings	an array of settings to save to the session.
	 * @return		array	the provided settings array
	 **/
	protected function _saveSettingsToSession($input_settings)
	{
		$this->EE->session->cache[self::$addon_id][SITE_ID]['settings'] = $input_settings;
		return $input_settings;
	}

	/**
	 * Sets up and subscribes to the hooks specified by the $hooks array.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @param		array	$hooks	a flat array containing the names of any hooks that this extension subscribes to. By default, this parameter is set to FALSE.
	 * @return		void
	 * @see			http://codeigniter.com/user_guide/general/hooks.html
	 **/
	protected function _registerHooks($hooks = FALSE)
	{
		if (isset($hooks) === FALSE OR empty($hooks) === TRUE)
		{
			$hooks = $this->hooks;
		}

		$hook_template = array(
			'class'	   => __CLASS__,
			'settings' => serialize(array()), // we are not using EE's settings table anymore
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
			$this->EE->db->query($this->EE->db->insert_string('exp_extensions', $hook));
		}
	}

	/**
	 * Removes all subscribed hooks for the current extension.
	 * @version		2.0.0
	 * @since		Version 1.0.0
	 * @access		protected
	 * @return		void
	 * @see			http://codeigniter.com/user_guide/general/hooks.html
	 **/
	protected function _unregisterHooks()
	{
		$this->EE->db->query("DELETE FROM `exp_extensions` WHERE `class` = '".__CLASS__."'");
	}

	/**
	 * Creates the settings table ('exp_nsm_addon_settings') table if it doesn't already exist.
	 * @version		2.0.0
	 * @since		Version 2.0.0
	 * @access		protected
	 * @return		void
	 **/
	protected function _createSettingsTable()
	{
		$table_name = 'nsm_addon_settings';
		$this->EE->load->dbforge();

		$fields = array(
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

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);

		if (!$this->EE->dbforge->create_table($table_name, TRUE))
		{
			show_error("Unable to create settings table for {$addon_name}: " . $this->EE->config->item('db_prefix') . $table_name);
			log_message('error', "Unable to create settings table for {$addon_name}: " . $this->EE->config->item('db_prefix') . $table_name);
		}
	}

	/**
	 * Prepares default member group settings.
	 * @version		2.0.0
	 * @since		Version 2.0.0
	 * @access		protected
	 * @return		void
	 **/
	public function _buildDefaultChannelSettings($channel_id = FALSE)
	{
		$default_channel_settings = array(
			'enabled' => FALSE,
			'preview_url' => ''
		);
		
		if($channel_id)
		{
			$this->settings['channels'][$channel_id] = $default_channel_settings;
			$this->_saveSettings($this->settings);
		}
		
		return $default_channel_settings;
	}

	/**
	 * Returns the default settings for this extension
	 * This is used when the extension is activated or when a new site is installed
	 * @version		2.0.0
	 * @since		1.x
	 * @access		protected
	 * @return	the default settings for the CURRENT site only
	 */
	protected function _buildDefaultSettings()
	{
		$default_settings_prototype = array(
			'enabled'			=> TRUE,
			'member_groups'	 	=> array(),
			'channels'			=> array(),
			'divider'			=> 0,
		);

		// Get all the channels for this site
		$channels = $this->EE->channel_model->get_channels(SITE_ID);

		// if there are channels
		if ($channels->num_rows() > 0)
		{
			$default_channel_settings = $this->_buildDefaultChannelSettings();
			// loop over the channels
			foreach($channels->result() as $channel)
			{
				$default_settings_prototype['channels'][$channel->channel_id] = $default_channel_settings;
			}
		}
		// return the default settings for this site
		return $default_settings_prototype;
	}
}