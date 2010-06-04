<?php
/**
 * Preview URL partial - Loaded in the extension settings table
 *
 * @package			NsmLiveLook
 * @version			0.3.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
?>
<tr>
	<td>
		<select name="<?= $input_prefix ?>[urls][][channel_id]">
			<?php 
				foreach ($channels as $channel) :
				$selected = ($row["channel_id"] == $channel->channel_id) ? " selected='selected'" : "";
			?>
				<option value="<?= $channel->channel_id ?>"<?= $selected ?>>
					<?= $channel->channel_title ?>
				</option>
			<?php endforeach; ?>
		</select>
	</td>
	<th scope="row">
		<input 
			type="text"
			name="<?= $input_prefix ?>[urls][][title]"
			value="<?= $row["title"] ?>"
		 />
	</th>
	<td>
		<input 
			type="text"
			name="<?= $input_prefix ?>[urls][][url]"
			value="<?= $row["url"] ?>"
			 />
		</td>
	<td>
		<input 
			type="hidden"
			name="<?= $input_prefix ?>[urls][][page_url]"
			value=""
		/>
		<input 
			type="checkbox"
			name="<?= $input_prefix ?>[urls][][page_url]"
			<?php if($row["page_url"]) print("checked='checked'"); ?>
			value="1"
		 />
	</td>
	<td>
		<span class="icon delete">Delete</span>
	</td>
</tr>