<?php
/**
 * View for Control Panel Settings Form
 * This file is responsible for displaying the user-configurable settings for the NSM Multi Language extension in the ExpressionEngine control panel.
 *
 * @package			NsmLiveLook
 * @version			0.3.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 **/
?>

<?php
	$hidden["{$input_prefix}[enabled]"] = TRUE;
?>

<div class="mor">
	<?= form_open(
			'C=addons_extensions&M=extension_settings&file=' . $addon_id,
			array('id' => $addon_id . '_prefs'),
			$hidden
		)
	?>

	<!-- 
	===============================
	Alert Messages
	===============================
	-->

	<?php if($error) : ?>
		<div class="alert error"><?php print($error); ?></div>
	<?php endif; ?>

	<?php if($message) : ?>
		<div class="alert success"><?php print($message); ?></div>
	<?php endif; ?>

	<!-- 
	===============================
	Channel Preferences
	===============================
	-->
	<div class="tg">
		<h2><?= lang('channel_preferences_title') ?></h2>
		<div class="alert info"><?php echo lang('channel_preferences_info') ?></div>
		<table class="cloneable" id="preview-urls">
			<thead>
				<tr>
					<th><?php echo lang('channel') ?></th>
					<th><?php echo lang('title') ?></th>
					<th><?php echo lang('preview_url') ?></th>
					<th><?php echo lang('Use page URL?') ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if(isset($data["urls"]))
					{
						$count = 0; 
						foreach ($data["urls"] as $row)
						{
							$this->load->view('extension_settings/_preview_url_row', array(
								"input_prefix" => $input_prefix,
								"count" => $count,
								"row_class" => ($count % 2) ? "odd" : "even",
								"channels" => $channels,
								"row" => $row
							));
							$count++;
						}
					}
				?>
			</tbody>
		</table>
		<div class="actions">
			<span class="icon add">Add another preview URL</span>
		</div>
	</div>

	<!-- 
	===============================
	Submit Button
	===============================
	-->

	<div class="actions">
		<input type="submit" class="submit" value="<?php print lang('save_extension_settings') ?>" />
	</div>

	<?= form_close(); ?>
</div>