<?php defined('_ARAKNY_') OR exit;
/**
 * Declares variables
 *
 * @var string $redirect
 */

echo _ipAddress();
echo service_useragent()->getBrowser();
echo '<br>';
echo base64_encode( _ipAddress() . '|' . service_useragent()->getBrowser() );

?>
<div class="ai w600 c page-login">
	<form id="form" class="ui form" action="<?= _url('users/login-process') ?>" method="post">
		<input type="hidden" name="redirect" value="<?= $redirect ?>">

		<div class="field">
			<div class="ui left icon large input">
				<input type="text" name="uid" id="uid" class="" placeholder="<?= _ga('l_u_login') ?>"
					   required pattern="[a-zA-Z0-9@._-]{2,50}" autofocus>
				<i class="fal fa-user blue icon"></i>
			</div>
		</div>

		<div class="field">
			<div class="ui left icon large input">
				<input type="password" name="upw" id="upw" class="form-control form-control-lg" placeholder="<?= _ga('l_u_pass') ?>"
					   required pattern="[A-Za-z0-9`~!@#$%^&*()=+\\|{}[\];:,<.>/?'&quot;_-]{4,100}">
				<i class="fal fa-key blue icon"></i>
			</div>
		</div>

		<div class="ui error message">
			<i class="fal fa-times close icon"></i>
			<div class="content"></div>
		</div>

		<div class="two fields ptb10">
			<div class="ten wide field">
				<button type="submit" class="ui primary big fluid submit button"><?= _g('login') ?></button>
			</div>
			<div class="six wide field">
				<a class="ui secondary big fluid button" href="<?= _url('users/signup') ?>"><?= _g('signup') ?></a>
			</div>
		</div>

		<div class="field">
			<div class="ui toggle checkbox">
				<input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
				<label for="remember"><?= _g('rememberme') ?></label>
			</div>
			<div class="col">

			</div>
		</div>

	</form>
</div>

<script>
	$(function() {
		var is_success = false;

		var form = byId('form');
		var $form = $(form);
		$form.on('submit', function() {
			if (is_success) return true;

			ajaxPost('<?= _url('users/login-process') ?>', form, null,
				function (data) {
					$('#alert').fadeOut();

					location.replace(data.redirect);
				},
				function (content) {
					$('.error.message .content').html( data.message );
					$('.error.message').transition('scale');

					$('#uid').val('').focus();
					$('#upw').val('');
				});

			return false;
		});

		$('.error.message .close').on('click', function() {
			$(this).closest('.error.message').transition('scale');
		});

		// 임시
		$('#uid').val('admin'); $('#upw').val('admin');
	});
</script>

<style>

</style>