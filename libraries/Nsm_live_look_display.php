<?php
/**
* NSM Live Look Display
* 
* A couple of simple display methods including adding CSS/JS
*/
class Nsm_live_look_display{

	private $addon_id;

	/**
	 * Constructs the class and sets the addon id
	 */
	public function __construct(){
		$this->addon_id = strtolower(substr(__CLASS__, 0 ,-8));
	}

	/**
	 * Adds CSS to the CP
	 * 
	 * @access public
	 * @return void
	 * @var $css string The CSS filepath or content
	 * @var $options array The options for this include
	 */
	public function addCSS($css, $options = array())
	{
		$options = array_merge(array(
			"where" => "head",
			"type" => "css",
		), $options);
		$this->addThemeAsset($css, $options);
	}

	/**
	 * Adds JS to the CP
	 * 
	 * @access public
	 * @return void
	 * @var $js string The JS filepath or content
	 * @var $options array The options for this include
	 */
	public function addJS($js, $options = array())
	{
		$options = array_merge(array(
			"where" => "foot",
			"type" => "js",
		), $options);
		$this->addThemeAsset($js, $options);
	}

	/**
	 * Adds either CSS or JS to the CP
	 * 
	 * @access public
	 * @return void
	 * @var $content string The CSS/JS content or filepath
	 * @var $options array The options for this include
	 */
	public function addThemeAsset($content, $options)
	{
		$EE =& get_instance();

		$options = array_merge(array(
			"where" => "head",
			"type" => "css",
			"file" => TRUE,
			"theme_url" => $this->_getThemeURL()
		), $options);

		switch ($options["type"]) {
			case 'css':
				if($options["file"])
					$content = '<link rel="stylesheet" type="text/css" href="'.$options["theme_url"] . "/styles/" . $content.'" />';
				else
					$content = '<style type="text/css" media="screen">'.$content.'</style>';
				break;
			
			case 'js':
				if($options["file"])
					$content = '<script type="text/javascript" charset="utf-8" src="'.$options["theme_url"] . "/scripts/" . $content.'"></script>';
				else
					$content = '<script type="text/javascript" charset="utf-8">'.$content.'</script>';
				break;
		}

		$method = "add_to_".$options["where"];
		$EE->cp->$method($content);
	}

	/**
	 * Get the current themes URL from the theme folder + / + the addon id
	 * 
	 * @access private
	 * @return string The theme URL
	 */
	private function _getThemeUrl()
	{
		$EE =& get_instance();
		if(!isset($EE->session->cache[$this->addon_id]['theme_url']))
		{
			$theme_url = $EE->config->item('theme_folder_url');
			if (substr($theme_url, -1) != '/') $theme_url .= '/';
			$theme_url .= "third_party/" . $this->addon_id;
			$EE->session->cache[$this->addon_id]['theme_url'] = $theme_url;
		}
		return $EE->session->cache[$this->addon_id]['theme_url'];
	}
}