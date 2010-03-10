<?php
/**
 * View for Control Panel Settings Form
 * This file is responsible for displaying the user-configurable settings for the NSM Multi Language extension in the ExpressionEngine control panel.
 *
 * @package Nsm_multi_language
 * @version 2.0.0
 * @author Leevi Graham & Anthony Short <http://newism.com.au>
 * @copyright Copyright (c) 2007-2009 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 **/

?>

<div class="mor">
	<?= form_open(
			'C=addons_extensions&M=extension_settings&file=nsm_live_look',
			array('id' => 'nsm_live_look_prefs'),
			array('file' => 'nsm_live_look')
		)
	?>

	<!-- 
	===============================
	Alert Messages
	===============================
	-->

	<?php if(validation_errors()) : ?>
		<div class="alert error"><?php echo validation_errors() ?></div>
	<?php endif; ?>

	<?php if($message) : ?>
		<div class="alert success">
			<p><?php print($message); ?></p>
		</div>
	<?php endif; ?>

	<!-- 
	===============================
	Channel Settings
	===============================
	-->

	<div class="tg">
		<h2><?= lang('channel_preferences_title') ?></h2>
		<div class="alert info"><?php echo lang('channel_preferences_info') ?></div>
		<table>
			<thead>
				<tr>
					<th><?php echo lang('channel') ?></th>
					<th><?php echo lang('preview_url') ?></th>
				</tr>
			</thead>
			<tbody>
		
				<?php if (empty($channels)) : ?>
				<tr>
					<td colspan="3"><?php echo lang("msg.warning.no_channels") ?></td>
				</tr>
				<?php else : ?>
	
				<?php foreach($channels as $count => $channel) : ?>
				<tr class="<?php echo ($count%2) ? 'odd' : 'even'; ?>">
			
					<th scope="row">
						<?php echo $channel->channel_title; ?>
					</th>
	
					<td>
						<?php
							$field = array
							(
								'name'        => 'Nsm_live_look_ext[channels]['.$channel->channel_id.'][preview_url]',
								'id'          => 'channel_' . $channel->channel_id . '_preview_url',
								'value'       => $settings['channels'][$channel->channel_id]['preview_url'],
								'style'		  => 'width:97%'
							);
							echo form_input($field);
						?>
					</td>
				</tr>
				<?php endforeach; ?>

				<?php endif; ?>
			</tbody>
		</table>
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