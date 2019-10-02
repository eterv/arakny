<?php defined('_ARAKNY_') OR exit;

?>
</div><!-- < / #content > -->

<footer id="footer" class="text-center">
	<div>_base 의 푸터입니다.</div>
	<div>Page rendered in <strong>{elapsed_time}</strong> seconds.</div>
	<div class="copyright">Copyright ⓒ 2019~ Arakny.com. All Rights Reserved. (Arakny v<?= A_VERSION ?>)</div>
	<div><?= routerController() . ' // ' . routerMethod() ?></div>
</footer>

<!-- 페이지 로드 후반에 할 일을 여기에 추가할 수 있다. -->
<!-- (ex: script, popup 등등) -->


</body>
</html>