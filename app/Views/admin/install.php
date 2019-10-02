<?php defined('_ARAKNY_') OR exit;

/**
 * Declares variables
 *
 * @var string $locale
 * @var string $bootstrap_css
 * @var string $jquery_js
 * @var string $zxcvbn_js
 * @var int $step
 * @var int $next_step
 */

?><!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
	<meta charset="utf-8">
	<title><?= _g('Arakny'); ?> - <?= _g('installation') ?></title>

	<link href="<?= $bootstrap_css ?>" rel="stylesheet">
	<script src="<?= $jquery_js ?>"></script>

	<?php if ($step == 3): ?>
	<script src="<?= $zxcvbn_js ?>" async></script>
	<?php endif; ?>

	<script>
		function ajax_post(url, data, done, fail, dataType) {
			var xhr = $.ajax({
				url: url,
				method: 'POST',
				data: data,
				dataType: dataType,
				success: done
			});
			if (typeof fail === 'undefined') {
				xhr.fail(function(jqXHR, textStatus, errorThrown) {
					var response, msg;
					try {
						//alert(jqXHR.responseText);
						response = JSON.parse(jqXHR.responseText);
						msg = response.message + ' (Error : ' + response.error + ')';
						alert(msg); return;
					} catch (e) {
						if (e instanceof SyntaxError) {
						} else {
							alert(e); return;
						}
					}
					msg = 'Status Code : ' + jqXHR.status + '\n' + 'Error : ' + jqXHR.error + '';
					alert(msg);
					console.log(jqXHR.responseText);
				});
			} else {
				xhr.fail(fail);
			}
		}
	</script>

	<style>
		body { background: #222; color: white; font-size: 16px; }
		header { background: #0078aa; color: white; }
		header .logo { margin: 0 auto; width: 120px; height: 120px; background: url('<?= adminThemeUrl('assets/img/logo-big-white.png') ?>') center/contain no-repeat; }

		.body { background: white; color: #333; overflow-x: hidden; overflow-y: auto; }
		.body .head { margin-bottom: 20px; }

		.body .locale { overflow-y: auto; }

		.body textarea { font-size: 14px; background: #f5f5f5 !important; }

		footer { display: flex; align-items: center; justify-content: center; flex-direction: column; height: 90px; }
	</style>
</head>
<body>

<header class="container-fluid py-3 text-center">
	<div class="logo"></div>
</header>

<div class="body">
	<div class="container py-5">

<?php
if ($step == 1):		// Step 1 :: Select a Default Language
?>
	<h5 class="head">Select a default language ::</h5>

	<form id="form" action="<?= BASEURL ?>admin/install/step<?= $next_step ?>" method="post">
        <?= csrf_field() ?>

		<div class="form-group">
			<select name="locale" id="locale" class="form-control locale" size="5" required>
				<!-- <option value="zh-Hans-CN">Chinese Simplified (中文普通话)</option> -->
				<option value="en-US">English (United States)</option>
				<!-- <option value="ja-JP">Japanese (日本語)</option> -->
				<option value="ko-KR">Korean (한국어)</option>
			</select>
		</div>

		<div class="text-center">
			<button type="submit" class="btn btn-primary">Continue</button>
		</div>
	</form>

	<script>
		(function() {
			var select = document.getElementById('locale');
			var value = "<?= $locale ?>";

			for (var i = 0; i < select.options.length; i++) {
				if (select.options[i].value === value) {
					select.options[i].selected = true;
					break;
				}
			}
		})();
	</script>

<?php
elseif ($step == 2):	// Step 2 :: Agree License & Input Database Information
?>
	<h5 class="head"><?= _g('m_install_step2_1') ?></h5>

	<form id="form" action="<?=BASEURL?>admin/install/step3" method="post">
        <?= csrf_field() ?>
		<input type="hidden" name="locale" value="<?=$locale?>">

		<div class="form-group">
			<textarea class="form-control" rows="15" readonly><?php include_once( adminThemePath('license.php') ); ?></textarea>
		</div>

		<div class="form-group text-right">
			<label><input type="checkbox" id="agree" class="" required> <?= _g('agree') ?></label>
		</div>

		<h5 class="head"><?= _g('m_install_step2_2') ?></h5>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="dbhost"><?= _g('dbhost') ?></label>
			<div class="col-sm-5">
				<input type="text" name="dbhost" id="dbhost" class="form-control" placeholder="<?= _ga('dbhost') ?>" required value="localhost">
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('dbhost_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="dbuser"><?= _g('dbuser') ?></label>
			<div class="col-sm-5">
				<input type="text" name="dbuser" id="dbuser" class="form-control" placeholder="<?= _ga('dbuser') ?>" required autofocus>
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('dbuser_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="dbpass"><?= _g('dbpass') ?></label>
			<div class="col-sm-5">
				<input type="password" name="dbpass" id="dbpass" class="form-control" placeholder="<?= _ga('dbpass') ?>" required>
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('dbpass_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="dbname"><?= _g('dbname') ?></label>
			<div class="col-sm-5">
				<input type="text" name="dbname" id="dbname" class="form-control" placeholder="<?= _ga('dbname') ?>" required>
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('dbname_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="dbprefix"><?= _g('dbprefix') ?></label>
			<div class="col-sm-5">
				<input type="text" name="dbprefix" id="dbprefix" class="form-control" placeholder="<?= _ga('dbprefix') ?>" required value="a_">
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('dbprefix_desc') ?></span>
		</div>

		<div class="form-group text-center">
			<button type="submit" class="btn btn-primary"><?= _g('next') ?></button>
		</div>

	</form>

	<script>
		var is_success = false;

		$(document).ready(function() {
			var $form = $('#form');
			$form.on('submit', function() {
				if (is_success) return true;

				ajax_post('<?= BASEURL ?>admin/install/db_process', $form.serialize(), function(data, textStatus, jqXHR) {
					var response;
					try {
						response = JSON.parse(jqXHR.responseText);
						if (response.result === 'success') {
							is_success = true;
							$form.submit();
						}
					} catch (e) {
						if (e instanceof SyntaxError) {
						} else {
							alert(e);
						}
					}
				});
				return false;
			});
		});
	</script>
<?php
elseif ($step == 3):	// Step 3 :: Set-up Application (Solution)
?>
	<h5 class="head"><?= _g('m_install_step3') ?></h5>

	<form id="form" action="<?= BASEURL ?>admin/install/step4" method="post">
        <?= csrf_field() ?>
		<input type="hidden" name="locale" value="<?= $locale ?>">

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="sitename"><?= _g('sitename') ?></label>
			<div class="col-sm-5">
				<input type="text" name="sitename" id="sitename" class="form-control" placeholder="<?= _ga('sitename') ?>" required autofocus>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="adminid"><?= _g('adminid') ?></label>
			<div class="col-sm-5">
				<input type="text" name="adminid" id="adminid" class="form-control" placeholder="<?= _ga('adminid') ?>"
					   required maxlength="50" pattern="[a-zA-Z0-9@._-]{2,50}" title="<?= _ga('adminid_desc') ?>">
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('adminid_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="adminpass"><?= _g('adminpass') ?></label>
			<div class="col-sm-5">
				<div class="input-group password-group">
					<input type="password" name="adminpass" id="adminpass" class="form-control" placeholder="<?= _ga('adminpass') ?>"
						   required pattern="[A-Za-z0-9`~!@#$%^&*()=+\\|{}[\];:,<.>/?'&quot;_-]{4,100}" style="ime-mode: disabled;">
					<div class="input-group-append">
						<button type="button" class="btn btn-outline-secondary btn-sm btn-togglepw" data-pwinput="#adminpass"
								data-text-show="<?= _ga('show'); ?>" data-text-hide="<?= _ga('hide'); ?>"><?= _g('show') ?></button>
					</div>

					<div class="pw-strength">
						<div class="pb"></div>
					</div>
				</div>
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('adminpass_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="adminname"><?= _g('adminname') ?></label>
			<div class="col-sm-5">
				<input type="text" name="adminname" id="adminname" class="form-control" placeholder="<?= _ga('adminname'); ?>" required maxlength="40" value="<?= _ga('l_ur_superadmin') ?>">
			</div>
			<span class="col-sm-5 form-text text-muted"><?= _g('adminname_desc') ?></span>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="adminemail"><?= _g('adminemail') ?></label>
			<div class="col-sm-5">
				<input type="email" name="adminemail" id="adminemail" class="form-control" placeholder="<?= _ga('adminemail') ?>"
					   required value="admin@domain.com">
			</div>
		</div>

		<div class="form-group text-center">
			<button type="submit" class="btn btn-primary"><?= _g('next') ?></button>
		</div>

	</form>

	<!-- < Password Strength Result - BEGIN > -->
	<style>
		.password-group { position: relative; padding-bottom: 8px; }
		.pw-strength { position: absolute; bottom: 0; width: 100%; height: 5px; }
		.pw-strength .pb { width: 100%; height: 100%; transition-duration: 300ms; }
		.pw-strength.score1 .pb { width: 20%; height: 100%; background: rgb(192, 0, 0); }
		.pw-strength.score2 .pb { width: 40%; height: 100%; background: rgb(255, 192, 0); }
		.pw-strength.score3 .pb { width: 60%; height: 100%; background: rgb(255, 255, 0); }
		.pw-strength.score4 .pb { width: 80%; height: 100%; background: rgb(0, 176, 80); }
		.pw-strength.score5 .pb { width: 100%; height: 100%; background: rgb(0, 112, 192); }
	</style>
	<script>
		var is_success = false;

		$(document).ready(function() {
			$('#adminpass').on('input', function() {
				var score = zxcvbn(this.value).score + 1;
				var $pws = $(this).parent().find('.pw-strength');
				$pws.removeClass('score1 score2 score3 score4 score5');
				$pws.addClass('score' + score);
			});

			$('.btn-togglepw').on('click', function() {
				var $this = $(this);

				var $input = $($this.data('pwinput'));
				if ($input.attr('type') === 'password') {
					$input.attr('type', 'text');
					$this.html( $this.data('text-hide') );
				} else {
					$input.attr('type', 'password');
					$this.html( $this.data('text-show') );
				}
				$input.select();
			});

			// Form Submit
			var $form = $('#form');
			$form.on('submit', function() {
				if (is_success) return true;

				ajax_post('<?= BASEURL ?>admin/install/finish', $form.serialize(),
					function(data, textStatus, jqXHR) {				// On Success
						var response;
						try {
							response = JSON.parse(jqXHR.responseText);
							if (response.result === 'success') {
								is_success = true;
								$form.submit();
							}
						} catch (e) {
							if (e instanceof SyntaxError) { } else { alert(e); }
						}
					},
					function(jqXHR, textStatus, errorThrown) {		// On Failure
						var response, msg, data, data2;
						try {
							response = JSON.parse(jqXHR.responseText);
							msg = response.message + ' (Error : ' + response.error + ')';
							data = response.data;
							if (typeof data === 'object') {
								if (data.library === 'validation') {
									msg = data.message;
									$('#' + data.field).select();
								}
							}
							alert(msg); return;
						} catch (e) {
							if (e instanceof SyntaxError) { }
							else {
								alert(e); return;
							}
						}
						msg = 'Status Code : ' + jqXHR.status + '\n' + 'Error : ' + jqXHR.error + '';
						alert(msg);
						//console.log(jqXHR.responseText);
					}
				);

				return false;
			});
		});
	</script>
	<!-- < / Password Strength Result - END > -->
<?php
elseif ($step == 4):	// Step 4 :: Set-up Application (Solution)
?>
	스텝 4 단계 완료!
<?php
endif;
?>
	</div>
</div>

<footer class="text-center">
	<div>Page rendered in <strong>{elapsed_time}</strong> seconds.</div>
	<div class="copyright">Copyright ⓒ 2019~ Arakny.com. All Rights Reserved. (Arakny v<?= A_VERSION ?>)</div>
</footer>

<?php load_admin_scripts(); ?>

<?php if (1 == 1): // 임시작업... ?>
<script>
	(function() {
		$('#agree').attr('checked', true);
		$('#dbuser').val( 'root' );
		$('#dbpass').val( 'root' );
		$('#dbname').val( 'arakny' );

		$('#sitename').val( 'Arakny' );
		$('#adminid').val( 'admin' );
		$('#adminpass').val( 'admin' );
	})();
</script>
<?php endif; ?>

</body>
</html>