<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nsm_live_look_tab
 *
 * Adds the tabs for this modulate
 *
 * @package default
 * @author Anthony Short & Leevi Graham
 */
class Nsm_live_look_tab
{
	/**
	 * Add tabs to the publish page
	 *
	 * @param $channel_id int The channel id
	 * @param $entry_id mixed The entry id if the entry already exists, FALSE if a new entry
	 * @return array The fields inside the tab
	 */
	public function publish_tabs($channel_id, $entry_id = FALSE)
	{
		if(!class_exists('Nsm_live_look_ext'))
		{
			require_once dirname(__FILE__) . '/ext.nsm_live_look.php';
		}

		$EE =& get_instance();
		$EE->lang->loadfile('nsm_live_look');
		
		$field_settings[] = array(
					'field_id'             => Nsm_live_look_ext::id(),
					'field_label'          => Nsm_live_look_ext::name(),
					'field_required'       => 'n',
					'field_data'           => '',
					'field_list_items'     => '',
					'field_fmt'            => '',
					'field_instructions'   => '',
					'field_show_fmt'       => 'n',
					'field_pre_populate'   => 'n',
					'field_text_direction' => 'ltr',
					'field_type'           => Nsm_live_look_ext::id()
				);

		return $field_settings;
	}

	/**
	 * Validates the submitted data
	 *
	 * @return boolean TRUE if the data is valid, FALSE if invalid
	 */
	public function validate_publish()
	{
		return TRUE;
	}
}