<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Install / Uninstall and updates the modules
 *
 * @package LiveLook
 * @author Anthony Short
 */
class Nsm_live_look_upd
{
	/**
	 * The module version
	 *
	 * @var string
	 */
	public static $version = '2.0.0';
	
	/**
	 * The name of the module
	 *
	 * @var string
	 */
	public static $module_name = 'Nsm_live_look';
	
	/**
	 * Does this module have a control panel?
	 *
	 * @var boolean
	 */
	public static $has_cp_backend = false;

	/**
	 * Does this module have publish fields?
	 *
	 * @var boolean
	 */
	public static $has_publish_fields = TRUE;
	
	/**
	 * Does this module have tabs?
	 *
	 * @var boolean
	 */
	public static $has_tabs = TRUE;
	
	/**
	 * Tab information
	 *
	 * @var array
	 */
	private $tabs = array
	(
		"Live Look" => array
		(
			"meta" => array(
				'visible'		=> 'true',
				'collapse'		=> 'false',
				'htmlbuttons'	=> 'false',
				'width'			=> '100%'
			)
		)
	);
	
	/**
	 * Get the EE singleton instance
	 */
	public function __construct() 
	{ 
		$this->EE =& get_instance();
	}  

	/**
	 * Installs the module
	 * 
	 * Installs the module, adding a record to the exp_modules table, creates and populates and necessary database tables, adds any necessary records to the exp_actions table, and if custom tabs are to be used, adds those fields to any saved publish layouts
	 *
	 * @return boolean
	 * @author Leevi Graham
	 */
	public function install()
	{
		$data = array
		(
			'module_name' 			=> self::$module_name,
			'module_version' 		=> self::$version,
			'has_cp_backend' 		=> (self::$has_cp_backend) ? "y" : "n",
			'has_publish_fields' 	=> (self::$has_publish_fields) ? "y" : "n"
		);
		
		# Add the module to the DB
		$this->EE->db->insert('modules', $data);

		return TRUE;
	}

	/**
	 * Updates the module
	 * 
	 * This function is checked on any visit to the module's control panel, and compares the current version number in the file to the recorded version in the database. This allows you to easily make database or other changes as new versions of the module come out.
	 *
	 * @return Boolean FALSE if no update is necessary, TRUE if it is.
	 * @author Leevi Graham
	 */
	public function update($current = FALSE)
	{
		return FALSE;
	}

	/**
	 * Uninstalls the module
	 *
	 * @return Boolean FALSE if uninstall failed, TRUE if it was successful
	 * @author Leevi Graham
	 **/
	public function uninstall()
	{
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => self::$module_name));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', self::$module_name);
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', self::$module_name);
		$this->EE->db->delete('actions');

		$this->EE->db->where('class', self::$module_name . "_mcp");
		$this->EE->db->delete('actions');	

		return TRUE;
	}
}