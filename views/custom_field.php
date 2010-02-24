<!-- Start LG Live Look Tab -->
<?php if($entry_id == FALSE) : ?>
	<div class="mor-alert error"><p><?php print $this->lang->line('msg.info.entry_unsaved'); ?></p></div>
<?php else: ?>

<p>Previewing entry <a href="<?= $preview_url ?>">#<?= $entry_id ?> <small>(latest revision)</small></a></p>

<p class='top'>
	<a href='#' class='btn toggle enlarge-iframe'>
		<?php print $this->lang->line('enlarge_iframe') ?>
	</a>
		&nbsp;&nbsp;&nbsp;
	<a href='#' class='btn toggle collapse shrink-iframe'>
		<?php print $this->lang->line('shrink_iframe') ?>
	</a>
</p>

<p>
	<div class='iframe-wrap'>
		<iframe id='llp_frame' src='<?= $preview_url; ?>'></iframe>
	</div>
</p>

<p>
	<a href='#' class='btn toggle enlarge-iframe'>
		<?php print $this->lang->line('enlarge_iframe') ?>
	</a>
		&nbsp;&nbsp;&nbsp;
	<a href='#' class='btn toggle collapse shrink-iframe'>
		<?php print $this->lang->line('shrink_iframe') ?>
	</a>
</p>

<?php endif; ?>
<!-- End LG Live Look Tab -->