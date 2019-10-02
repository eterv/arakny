<?php defined('_ARAKNY_') OR exit;
/**
 * Declares variables
 *
 * @var string $locale
 */

// 스타일 로드
addStyle('fontawesome', _asset('assets/font/FontAwesome/css/all.min.css'));
addStyle('semantic', _asset('assets/css/semantic.min.css'));
addStyle('ai', _asset('assets/ai/ai.css'));
//addStyle('site-base', _asset('assets/css/base.css'));

// 스크립트 로드
addScriptInHead('jquery', baseThemeUrl('assets/js/jquery-3.4.1.min.js'));
addScriptInHead('lodash', baseThemeUrl('assets/js/lodash.min.js'), ['jquery']);
addScriptInHead('external', baseThemeUrl('assets/js/external.js'));
addScriptInHead('semantic', baseThemeUrl('assets/js/semantic.min.js'), ['jquery']);

addScriptInHead('language', baseThemeUrl('assets/js/language/' . $locale . '.js'), ['jquery', 'semantic']);
addScriptInHead('site-base', baseThemeUrl('assets/js/base.js'), ['jquery']);
addScriptInHead('ai', baseThemeUrl('assets/ai/ai.js'), ['jquery']);

?><!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<?= _metaCSRF() ?>

	<title><?= _pageHeadTitle() ?></title>

	<!-- 사용자 정의 메타 태그 넣기 -->

	<!-- 상수 스크립트 -->
	<?= _constants() ?>

	<!-- 스타일 / 스크립트 -->
	<?= _styles() ?>
	<?= _scriptsHead() ?>

	<script>

	</script>

	<style>
		body { margin: 0; }
		header {  }
		header .logo { margin: 0 auto; width: 120px; height: 120px; background: url('<?= _asset('assets/logo-big-white.png') ?>') center/contain no-repeat; }

		#menu {  }
		#menu .lv1 { display: flex;  }
		#menu .lv1-item { flex: 1; text-align: center; overflow: hidden; }
		#menu .lv1-item-a { display: block; padding: 20px 5px; background: #eee; }
		#menu .lv1-item-a:hover { background: #0078aa; color: white; }
		#menu .lv2 {  }

		footer { display: flex; align-items: center; justify-content: center; flex-direction: column; margin-top: 20px; }

		/* Semantic UI - Arakny Fix */

		.ui.prompt.label { line-height: 1.4; }
	</style>
</head>
<body>

<header id="header">
	<h1>헤더 부분 입니다. 베이스 입니다. <?= isHome() ? '홈!' : '서브!' ?></h1>

	<nav id="menu">
		<ul class="lv1">
			<?php foreach (_menu() as $menu): ?>
			<li class="lv1-item">
				<a class="lv1-item-a" href="<?= $menu['href'] ?>" <?= $menu['target_attr'] ?>><?= $menu['label'] ?></a>
				<ul class="lv2">
					<?php foreach ($menu['items'] as $menu2): ?>
					<li class="lv2-item">
						<a class="lv2-item-a" href="<?= $menu2['href'] ?>" <?= $menu2['target_attr'] ?>><?= $menu2['label'] ?></a>
						<ul class="lv3">

						</ul>
					</li>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php endforeach; ?>
		</ul>
	</nav>
</header>

<div id="content" class="">