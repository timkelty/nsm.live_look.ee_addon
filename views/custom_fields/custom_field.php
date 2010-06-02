<?php
/**
 * Custom field
 *
 * @package			NsmLiveLook
 * @version			0.2.0
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.live_look.ee-addon
 * @copyright 		Copyright (c) 2007-2010 Newism
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
?>
<!-- Start LG Live Look Tab -->
<div class="mor cf">
	<?php if($entry_id == FALSE) : ?>
		<div class="alert error"><p><?php print $this->lang->line('alert.info.entry_unsaved'); ?></p></div>
	<?php elseif(!$urls): ?>
		<div class="alert error"><p><?php print $this->lang->line('alert.info.no_preview_urls'); ?></p></div>
	<?php else: ?>
		<ul class="menu tabs">
			<?php foreach($urls as $count => $url) : ?>
				<li><a href="#url-<?=$count?>" class="active"><?= $url["title"]; ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php foreach($urls as $count => $url) : ?>
			<div id="url-<?=$count?>" class='iframe-wrap tg'>
				<div class="alert info" style="margin:0">
					<a href='#' class='icon delete shrink-iframe'>
						<?php print $this->lang->line('shrink_preview') ?>
					</a>
					<a href='#' class='icon add enlarge-iframe'>
						<?php print $this->lang->line('enlarge_preview') ?>
					</a>
					Previewing: <a href="<?= $url["url"]; ?>" target="_blank"><?= $url["url"]; ?></a>
				</div>
				<iframe src='<?= $url["url"]; ?>'></iframe>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<!-- End LG Live Look Tab -->