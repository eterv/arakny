<?php defined('_ARAKNY_') OR exit;
/**
 * Declares variables
 *
 * @var string $locale
 */

?>
<div class="ai center text">
	<div class="ui center text container"></div>

	<div>
		로그인 정보 -
		<?php if (isLoggedIn()): ?>
		ID :: <?= _e('') ?>
		<a href="<?= _url('users/logout') ?>">로그아웃</a>
		<?php else: ?>
		<a href="<?= _url('users/login') ?>">로그인</a>
		<?php endif; ?>
	</div>

	_base - 메인 페이지<br><br>

	다음 테스트는 ... 스크립트 스타일 경로 테스트 (_base, khome-v1, thema001 ) 3군데에 파일을 넣고 어떻게 작동하는지 본다.<br>
	스타일시트는 부모->자식 순서로 누적 기능을 도입해야<br>
</div>

<script>
	(function() {

	})();
</script>