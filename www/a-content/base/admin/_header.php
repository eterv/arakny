<?php defined('_ARAKNY_') OR exit;
/**
 * Declares variables
 *
 * @var string $locale
 * @var array $menu_main
 * @var array $menu_sub
 */

// 스타일 로드
addStyle('fontawesome', baseThemeUrl('assets/font/fontawesome/css/all.min.css'));
addStyle('semantic', baseThemeUrl('assets/css/semantic.min.css'));
//addStyle('site-base', baseThemeUrl('assets/css/base.css'));
addStyle('ai', baseThemeUrl('assets/ai/ai.css'));

// 스크립트 로드
addScriptInHead('jquery', baseThemeUrl('assets/js/jquery-3.4.1.min.js'));
addScriptInHead('lodash', baseThemeUrl('assets/js/lodash.min.js'), ['jquery']);
addScriptInHead('external', baseThemeUrl('assets/js/external.js'));
addScriptInHead('semantic', baseThemeUrl('assets/js/semantic.min.js'), ['jquery']);

addScriptInHead('language', baseThemeUrl('assets/js/language/' . $locale . '.js'), ['jquery', 'semantic']);
addScriptInHead('site-base', baseThemeUrl('assets/js/base.js'), ['jquery']);
addScriptInHead('ai', baseThemeUrl('assets/ai/ai.js'), ['jquery']);
addScriptInHead('admin-base', adminThemeUrl('assets/js/admin.js'), ['jquery', 'semantic', 'site-base']);

addScriptInHead('afilelist', baseThemeUrl('assets/ai/afilelist.js'), ['jquery', 'site-base']);
addScriptInHead('afileexplorerdialog', baseThemeUrl('assets/ai/afileexplorerdialog.js'), ['jquery', 'site-base']);
addScriptInHead('aimageuploader', baseThemeUrl('assets/ai/aimageuploader.js'), ['jquery', 'site-base']);

?><!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
	<?= _metaCSRF() ?>

	<title><?= _pageHeadTitle(); ?></title>

	<!-- 사용자 정의 메타 태그 넣기 -->

	<?= _styles() ?>
	<?= _scriptsHead() ?>

	<script>
		$(function () {
			var $body = $('body');
			var $menu = $('#menu');
			var $menuitems = $menu.find('.menuitem');
			var $submenu = $menu.find('.sub-menu');
			var $btn_menu = $('.header-top-bar .btn-menu');

			$btn_menu.on('click', function () {
				$body.toggleClass('menu-on');
				if ($body.hasClass('menu-on')) {
					//$btn_menu.find('i').removeClass('fas fa-grip-horizontal').addClass('fal fa-times');
					//$btn_menu.find('.icon-open').show();
					//$btn_menu.find('.icon-open').show();
				} else {
					//$btn_menu.find('i').removeClass('fal fa-times').addClass('fas fa-grip-horizontal');
				}
			});

			$menuitems.on('click', function () {
				var d_id = $(this).data('id');

				$menuitems.removeClass('on');
				$(this).addClass('on');

				$submenu.find('.group').removeClass('on');
				$submenu.find('.group.group-' + d_id).addClass('on');
			});

			if ($submenu.find('.on').length === 0) {
				$submenu.find('.group-general').addClass('on');
			}

			byId('menu-overlay').addEventListener('click', function () {
				$body.removeClass('menu-on');
			});
		});
	</script>

	<style>
		.no-drag { -ms-user-select: none; -moz-user-select: -moz-none; -webkit-user-select: none; -khtml-user-select: none; user-select:none; }

		html, body { font-size: 15px; font-weight: 400; }

		header { /*background: #0078aa; color: white;*/ }
		a { color: #0078aa; }

		header { height: 100px; }
		.header-top-bar { position: fixed; left: 0; right: 0; height: 50px; z-index: 100; display: flex; background: #222; color: white; }

		.header-top-bar .btn-menu { width: 40px; height: 50px; line-height: 50px; font-size: 24px; text-align: center; cursor: pointer; }
		.header-top-bar .btn-menu .icon-close { display: none; }
		body.menu-on .header-top-bar .btn-menu .icon-open { display: none; }
		body.menu-on .header-top-bar .btn-menu .icon-close { display: inline-block; }

		.header-top-bar .logo-part { display: flex; align-items: center; margin-left: 10px; }
		.header-top-bar .logo { width: 30px; height: 30px; margin-right: 5px; background: url('<?= adminThemeUrl('assets/img/logo-big.png') ?>') center/contain no-repeat; }

		#menu-overlay {
			display: none;
			position: fixed; left: 0; right: 0; top: 0; bottom: 0; z-index: 99;
			background: rgba(0, 0, 0, 0.5);
		}
		#menu {
			position: fixed; left: 0; top: 50px; bottom: 0; width: 200px; z-index: 100; opacity: 0.2;
			display: flex; box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.15); overflow: hidden;
			transform: scaleX(0); transition-duration: 300ms;
			transform-origin: left center;
		}
		body.menu-on #menu { transform: scaleX(1); opacity: 1; }

		.main-menu { position: absolute; left: 0; top: 0; bottom: 0; width: 40px; background: #222; }
		.main-menu .group { padding: 5px 0; }
		.main-menu a { display: block; height: 40px; line-height: 40px; text-align: center; cursor: pointer; }
		.main-menu a:hover { background: #0078aa; color: white; }
		.main-menu a.on { background: #f5f5f5; color: #333; }

		.sub-menu { position: absolute; left: 40px; top: 0; bottom: 0; width: 160px; padding: 15px 0;
			background: #f5f5f5; border-right: 1px solid #eee; overflow: hidden; }
		.sub-menu .title { font-weight: 600; margin: 0 15px 15px; }
		.sub-menu .group { display: none; }
		.sub-menu .group.on { display: block; }
		.sub-menu a { position: relative; display: block; height: 30px; line-height: 30px; padding: 0 15px; }
		.sub-menu a span { position: relative; }
		.sub-menu a::before { content: ""; position: absolute; display: block; left: 100%; right: 0; top: 0; bottom: 0; background: #0078aa; opacity: 1; transition-duration: 300ms; }
		.sub-menu a.on::before { left: 0; background: #ddd; }
		.sub-menu a:hover { color: white; }
		.sub-menu a:hover::before { left: 0; background: #0078aa; }

		#body {
			position: fixed; left: 0; right: 0; top: 100px; bottom: 0; padding: 25px 0; overflow-x: hidden; overflow-y: auto; transition-duration: 300ms;
		}
		body.menu-on #body { left: 200px; }

		.sub-header { position: fixed; left: 0; right: 0; top: 50px; height: 50px; z-index: 9;
			display: flex; align-items: center; justify-content: space-between; padding: 0 15px;
			background: white; box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.15); transition-duration: 300ms; }
		body.menu-on .sub-header { left: 200px; padding: 0 15px; }
		.sub-header .ui.header { margin-bottom: 0; }

		footer { display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 30px 0 0; transition-duration: 300ms; }

		@media (max-width: 767px) {
			body.menu-on #menu-overlay { display: block; }

			#menu {  }
			.sub-menu {  }

			#body { margin-left: 0 !important; }
			body.menu-on #body { left: 0; }
			.sub-header { margin-left: 0 !important; padding: 0 10px; }
			body.menu-on .sub-header { left: 0; padding: 0 10px; }

			footer { margin-left: 0 !important; }
		}

		/* Semantic UI - Arakny Fix */

		.ui.fw-thin.header, .ui.fw-thin.button { font-weight: 100; }
		.ui.fw-light.header, .ui.fw-light.button { font-weight: 300; }
		.ui.fw-regular.header, .ui.fw-regular.button { font-weight: 400; }
		.ui.fw-medium.header, .ui.fw-medium.button { font-weight: 500; }
		.ui.fw-bold.header, .ui.fw-bold.button { font-weight: 700; }
		.ui.fw-black.header, .ui.fw-black.button { font-weight: 900; }

		/* icon margin fix */
		i.icon { margin: 0; }

		.ui.compact.grid .row:not(:first-child), .ui.grid .compact.row {
			padding-top: 8px;
		}
		.ui.compact.grid .row:not(:last-child), .ui.grid .compact.row {
			padding-bottom: 8px;
		}
		.ui.compact.grid .column:not(:first-child), .ui.grid .compact.row .column:not(:first-child), .ui.grid .compact.column {
			padding-left: 8px;
		}
		.ui.compact.grid .column:not(:last-child), .ui.grid .compact.row .column:not(:last-child), .ui.grid .compact.column {
			padding-right: 8px;
		}

		.ui.very.compact.grid .row:not(:first-child), .ui.grid .very.compact.row {
			padding-top: 4px;
		}
		.ui.very.compact.grid .row:not(:last-child), .ui.grid .very.compact.row {
			padding-bottom: 4px;
		}
		.ui.very.compact.grid .column:not(:first-child), .ui.grid .very.compact.row .column:not(:first-child), .ui.grid .very.compact.column {
			padding-left: 4px;
		}
		.ui.very.compact.grid .column:not(:last-child), .ui.grid .very.compact.row .column:not(:last-child), .ui.grid .very.compact.column {
			padding-right: 4px;
		}

		@media (max-width: 767px) {
			.ui.stackable.grid > .column:not(.row) { padding-left: 0 !important; padding-right: 0 !important; }
		}

		.ui.radio.checkbox label { padding-left: 22px; }

		.ui.form input[type=password],
		.ui.form input[type=text],
		.ui.form input[type=url] { padding: 7px 10px; }

		.ui.form .field .ui.segment { margin-top: 0; }
		.ui.form .ui.segment .field:first-child { margin-top: 0; }
		.ui.form .ui.segment .field:last-child { margin-bottom: 0; }
		.ui.form .field .prompt.label { font-weight: 400; }

		.ui.toast-container { z-index: 80000; }

		.ui.modal > .header { margin-bottom: 1px; }
	</style>
</head>
<body class="<?= ! isMobile() ? 'menu-on' : '' ?> no-drag">

<header id="header">
	<div class="header-top-bar">
		<div class="btn-menu">
			<i class="grip horizontal icon icon-open"></i>
			<i class="times outline l icon icon-close"></i>
		</div>
		<div class="logo-part">
			<div class="logo"></div>
			<div class="span h6n">ARAKNY</div>
		</div>
	</div>

	<div id="menu-overlay"></div>
	<div id="menu" class="">
		<nav class="main-menu">
			<div class="group">
				<?php foreach ($menu_main as $item): ?>
				<a class="menuitem <?= $item['on'] ?? '' ?>" href="<?= $item['link'] ?? 'javascript:void(0)' ?>"
				   title="<?= _ta('Admin.l_menu_main_' . $item['id']) ?>" data-id="<?= $item['id'] ?>"><i class="<?= $item['icon_class'] ?>"></i></a>
				<?php endforeach; ?>
			</div>
		</nav>

		<nav class="sub-menu">
			<?php for ($i = 0; $i < count($menu_main); $i++):
				$item = $menu_main[$i];
				if ($item['id'] === 'home') continue;
			?>
			<div class="group group-<?= $item['id'] ?> <?= $item['on'] ?? '' ?>">
				<div class="title"><?= _t('Admin.l_menu_main_' . $item['id']) ?></div>

				<?php foreach ($menu_sub as $sub):
					if ($sub['group'] !== $item['id']) continue;
					?>
				<a class="<?= $sub['on'] ?? '' ?>" href="<?= _url('admin/' . $sub['id']) ?>"><span><?= _t('Admin.l_menu_sub_' . $sub['id']) ?></span></a>
				<?php endforeach; ?>
			</div>
			<?php endfor; ?>
		</nav>
	</div>
</header>

<div id="body" class="">
	<!-- <h1>헤더 부분 입니다. 베이스 입니다. <?= isHome() ? '홈!' : '서브!' ?></h1> -->

	<?php for ($i = 0; $i <= -100; $i++): ?>
		<h5>테스트 <?= $i ?></h5>
	<?php endfor; ?>
