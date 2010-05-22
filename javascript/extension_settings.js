/**
 * jQuery code for extension settings
 *
 * @package			NsmLiveLook
 * @version			0.2.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
jQuery(document).ready(function($) {
	$("#preview-urls").NSM_Cloneable({
		cloneTemplate: NSM_Live_Look.templates.$preview_url
	})
	.NSM_UpdateInputsOnChange();
});