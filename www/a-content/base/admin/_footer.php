<?php defined('_ARAKNY_') OR exit;

?>
<!--
	<footer id="footer">
		<div>Page rendered in <strong>{elapsed_time}</strong> seconds.</div>

		<div class="mt10"><?= routerDirectory() . ' || ' . routerController() ?></div>
	</footer>
-->
</div><!-- < / #body > -->

<div id="mo-a-file-explorer" class="ui tiny modal">
	<i class="close icon"></i>
	<div class="header">File Explorer</div>
	<div class="scrolling content">
		<div class="ui pointing secondary menu">
			<a class="item active" data-tab="upload">업로드</a>
			<a class="item" data-tab="list">목록</a>
		</div>
		<div class="ui active tab" data-tab="upload">
			<form id="dz-upload" class="dropzone" action="<?= _url('file/upload') ?>">
				<!-- <input type="file" class="file"> -->
			</form>
		</div>
		<div class="ui tab" data-tab="list">
			목록
		</div>

		<form class="ui form">
			<div class="field">
				<label for="">메뉴 ID</label>
				<input type="text" name="" id="mo-info-menuid" placeholder="메뉴 ID">
			</div>
			<div class="field">
				<label for="">메뉴 이름</label>
				<input type="text" name="" id="mo-info-menuname" placeholder="메뉴 이름">
			</div>
		</form>
	</div>
	<div class="actions">
		<div class="ui cancel button">취소</div>
		<div class="ui ok positive button fw-light">확인</div>
	</div>
</div>

<!-- 페이지 로드 후반에 할 일을 여기에 추가할 수 있다. -->
<!-- (ex: script, popup 등등) -->

<script>
	//Dropzone.autoDiscover = false;

	function AFileExplorer2() {
		$('#mo-a-file-explorer').modal('show');
	}

	AFileExplorer2.prototype.show = function () {
		alert('123');
	};

	$(function () {
		var $mo_fe = $('#mo-a-file-explorer');

		$mo_fe.find('.menu .item').tab({ });

		$mo_fe.modal({  });
		$mo_fe.parent().css({ 'z-index': 70000 });

		// DropZone 설정
		/*
		var dz1 = new Dropzone('#dz-upload', {
			url: '<?= _url('file/upload') ?>',
			acceptedFiless: 'image/*' /*'.gif,.jpg,.jpeg,.png'* /,
			maxFiles: 10,
			maxFilesize: <?= 8 /*ini_get('post_max_size')*/ ?>
		});
		dz1.on('sending', function(file, xhr, formData) {
			formData.append('type', 'image');
			formData.append("<?= csrf_token() ?>", "<?= csrf_hash() ?>");
		});*/

		//new AFileExplorer();
	});
</script>

</body>
</html>