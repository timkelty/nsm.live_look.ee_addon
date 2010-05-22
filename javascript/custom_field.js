/**
 * Changes height of iFrame and stores the value in a cookie
 *
 * @package			NsmLiveLook
 * @version			0.2.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
jQuery(document).ready(function($) {

	h = $.cookie("nsm_live_look_h") || 200;
	$iframe = $(".iframe-wrap iframe").attr({"height": h+"px"});

	$('.enlarge-iframe').click(function() {
		h = h + 100;
		$iframe.height(h);
		$.cookie("lg_live_look_h", h);
		return false;
	});

	$('.shrink-iframe').click(function() {
		if(h > 200)
		{
			h = h - 100;
			$iframe.height(h);
			$.cookie("lg_live_look_h", h);
		}
		return false;
	});

});