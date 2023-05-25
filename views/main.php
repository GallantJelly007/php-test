<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo DOMAIN.'/views/css/faststyle.css'?>">
	<link rel="stylesheet" href="<?php echo DOMAIN.'/views/css/style.css'?>">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo DOMAIN.'/views/images/favicon.ico'?>">
	<script src="<?php echo DOMAIN.'/views/js/script.js'?>"></script>
	<script src="<?php echo DOMAIN.'/views/js/jax.js'?>"></script>
	<script src="<?php echo DOMAIN.'/views/js/requests.js'?>"></script>
	<title>Тестовое задание</title>
</head>
<body class="d-flex fd-col">
	<div class="popup-back p-10 maxh-100">
		<div id="auth" class="popup-message maxh-100 ov w-30 w-m-100 ov-m-scroll-y mh-m-10 d-none">
			<?php echo $authPanel?>
		</div>
	</div>
	<header class="d-flex ai-center jc-bspace pv-10 pos-fix w-100 z-20">
		<nav class="container fd-row jc-bspace main-padding w-100">
			<div class="fsz-20 fw-bold c-main d-flex ajc-center">LOGO</div>
			<?php echo $navAdminButtons?>
			<?php echo $authButton?>
		</nav>
	</header>
	<main class="container pv-20 pt-m-10 pb-m-50 main-padding flex-1 w-100 mt-50">
		<article id="content-cont" class="w-100 minh-100 d-flex fd-col">
            <?php echo $content?>
		</article>
		<aside id="aside-desc" class="aside-desc maxw-20 maxw-m-100 fsz-08">
		</aside>
	</main>
	<footer class="d-flex pb-10 pr-10 jc-end w-100 pos-fix">
		<span class="author">@Created by Alex</span>
	</footer>
</body>
</html>