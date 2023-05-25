<?php
    $navId = $root?'id="nav-admin-btns"':'';
    $rootButton = $root?'<button id="create-root" class="create-root admin-button"></button>':'';
	$disabled = $root?'disabled':'';
?>

<div <?php echo $navId ?> class="admin-btns d-flex ai-center">
	<?php echo $rootButton ?>
	<button class="create admin-button" <?php echo $disabled?>></button>
	<button class="edit admin-button" <?php echo $disabled?>></button>
	<button class="delete admin-button" <?php echo $disabled?>></button>
</div>