<?php if ($status_messages): ?>
	<div class="status_messages">
	<?php foreach ($status_messages AS $message): ?>
		<p class="<?php echo $message->get_type(); ?>"><?php echo $message->get_message(); ?></p>
	<?php endforeach; ?>
	</div>
<?php endif; ?>