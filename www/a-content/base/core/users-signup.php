<?php defined('_ARAKNY_') OR exit;

use Arakny\Models\UsersModel as M;

/**
 * Declares variables
 *
 * @var string $locale
 * @var array $fields
 * @var array $choosableItems
 * @var array $rules
 * @var string $redirect
 * @var string $urlSubmit
 */

// zxcvbn 패스워드 강도 측정 스크립트
addScriptInHead('zxcvbn', baseThemeUrl('assets/js/zxcvbn.js'));

// Cleave Phone Number Mask i18n 스크립트
addScriptInHead('cleave-phone-i18n', baseThemeUrl('assets/js/cleave-phone.i18n.js'));

$localeCountry = substr($locale, strpos($locale, '-') + 1);

$masks = [
	M::u_phone => 'phone,' . $localeCountry,
	M::u_birthdate => 'date-Y-m-d',
]

?>
<style>
	/*.ai.icon.form input[type=text]::placeholder { color: #ccc; }
	.ai.icon.form input[type=text]:focus::placeholder { color: #999; }*/

	.agreement { margin-top: 10px; font-size: 0.9em; }
	.agreement a { color: #333; text-decoration: underline; cursor: pointer; }
	.agreement a.on { color: #00a0e0; font-weight: 500; }
	.agreement .box { display: none; height: 250px; margin-top: 20px; padding: 20px; border: 1px solid #ccc; overflow: auto; }
	.agreement .box.on { display: block; }
</style>

<div class="ai w600 c page-signup">
	<form id="form" class="ai size-l" method="post">
		<input type="hidden" name="redirect" value="<?= $redirect ?>">

		<?php foreach ($fields as $index => $field):
			$inputType = 'text';
			switch ($field) {
				case M::u_pass:
				case M::u_pass_check:
					$inputType = 'password'; break;
			}
		?>
		<div class="field">
			<label for="field_<?= $field ?>"><?= _g('l_' . $field) ?></label>
			<?php if (array_key_exists($field, $choosableItems)): ?>
			<select class="dropdown" name="<?= $field ?>" id="field_<?= $field ?>" <?= ($index === 0 ? 'autofocus' : '') ?>
					data-rules="<?= _ea($rules[$field]['rules']) ?>">
				<option value=""></option>
				<?php foreach ($choosableItems[$field] as $item): ?>
				<option value="<?= $item['value'] ?>"><?= $item['text'] ?></option>
				<?php endforeach; ?>
			</select>
			<?php else: ?>
			<div class="ai input">
				<input type="<?= $inputType ?>" name="<?= $field ?>" id="field_<?= $field ?>" <?= ($index === 0 ? 'autofocus' : '') ?>
					   data-rules="<?= _ea($rules[$field]['rules']) ?>"
				       <?= array_key_exists($field, $masks) ? 'data-mask="'. $masks[$field] .'"' : '' ?>>
			</div>
			<?php endif; ?>
			<?php if ($field === M::u_pass): ?>
			<div class="pw-strength score1"><div class="pb"></div></div>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>

		<?= _captchaHtml(false, 50) ?>

		<div class="field row">
			<label>텍스트 에어리어</label>
			<textarea>테스트<br>이렇게 하는 것이다!</textarea>
		</div>

		<div class="field">
			<label>선택 옵션</label>
			<div class="ai checkboxes">
				<label><input type="checkbox"> 체크 상자1</label>
				<label><input type="checkbox"> 옵션 2</label>
				<label><input type="checkbox"> 선택하세요~~</label>
			</div>
		</div>

		<div class="fields">
			<div class="field">
				<label>텍스트 에어리어 1</label>
				<input type="text">
			</div>

			<div class="field">
				<label>텍스트 에어리어 2</label>
				<input type="text">
			</div>
		</div>

		<div class="field ptb10">
			<button type="submit" class="ai primary large round full button"><?= _g('agree_and_signup') ?></button>

			<div class="agreement">본인은 <a data-type="terms">사이트 이용약관</a> 및 <a data-type="privacy">개인정보 처리방침 안내</a>에 동의합니다.
				<div class="terms box"><?php include_once(baseThemePath('pages/terms.php')); ?></div>
				<div class="privacy box"><?php include_once(baseThemePath('pages/privacy.php')); ?></div>
			</div>
		</div>


		<div class="field">
			<div class="ai checkbox">
				<input type="checkbox" name="remember3" id="remember4" value="1">
				<label for="remember4"><?= _g('rememberme') ?></label>
			</div>
		</div>

		<div class="field">
			<div class="ai toggle checkbox">
				<input type="checkbox" name="remember" id="remember2" value="1">
				<label for="remember2"><?= _g('rememberme') ?></label>
			</div>
		</div>

		<div class="field">
			<div class="ai bool toggle checkbox">
				<input type="hidden" name="remember" id="remember3">
				<label for="remember3">LOGIN Save</label>
			</div>
		</div>

	</form>
</div>

<script>
	$(function() {

		var elForm = byId('form');
		var $form = $(elForm);

		var aiValidator = new AIValidator({
			form: elForm,
		})
		.on('form.submit', function () {
			var url = '<?= $urlSubmit ?>';
			ajaxPost(url, elForm, {},
				function (result) {
					toastSuccess('성공!');
				});

			return false;
		});

		/*
		$form.parsley({
			debounce: 300,
			validationThreshold: 0,
			errorsWrapper: '<ul class="parsley-errors-list ui pointing error label"></ul>',
			errorsContainer: function(pEle) {
				return pEle.$element.closest('.field');
			}
		})
		.on('form:error', function () {
			$.each(this.fields, function (key, field) {
				if (field.validationResult !== true) {
					field.$element.closest('.field').find('.parsley-errors-list').show();
				}
			});
		})
		.on('field:validated', function () {
			var $field = this.$element.closest('.field');
			if (this.validationResult === true) {
				$field.addClass('success');
				$field.removeClass('error');
				$field.find('.parsley-errors-list').hide();
			} else {
				$field.removeClass('success');
				$field.addClass('error');
				$field.find('.parsley-errors-list').show();
			}
		})
		.on('form:submit', function () {
			var url = '';
			ajaxPost(url, elForm, {},
				function (result) {
					toastSuccess('성공!');
				});

			return false;
		});*/

		$('.agreement a').click(function () {
			var $this = $(this);

			if ($this.hasClass('on')) {
				$('.agreement a').removeClass('on');
				$('.agreement .box').removeClass('on');
			} else {
				$('.agreement a').removeClass('on');
				$('.agreement .box').removeClass('on');

				$(this).addClass('on');
				$('.agreement .box.' + this.dataset.type).addClass('on');
			}
		});
	});
</script>

<!-- < Password Strength Result - BEGIN > -->
<style>
	.password.field { position: relative; }
	.pw-strength { margin: 3px 5px 0; height: 6px; border-radius: 2px; overflow: hidden; }
	.pw-strength .pb { width: 100%; height: 100%; transition-duration: 300ms; }
	.pw-strength.score1 .pb { width: 20%; height: 100%; background: rgb(192, 0, 0); }
	.pw-strength.score2 .pb { width: 40%; height: 100%; background: rgb(255, 192, 0); }
	.pw-strength.score3 .pb { width: 60%; height: 100%; background: rgb(255, 255, 0); }
	.pw-strength.score4 .pb { width: 80%; height: 100%; background: rgb(0, 176, 80); }
	.pw-strength.score5 .pb { width: 100%; height: 100%; background: rgb(0, 112, 192); }
</style>
<script>
	$(function() {
		window.passwordCheck = function () {
			var score = zxcvbn(this.value).score + 1;
			var $pws = $(this).closest('.field').find('.pw-strength');
			$pws.removeClass('score1 score2 score3 score4 score5');
			$pws.addClass('score' + score);
		};
		document.getElementById('field_u_pass').addEventListener('input', passwordCheck);
	});
</script>
<!-- < / Password Strength Result - END > -->


<script>
	$(function () {
		// 임시
		$('#field_u_login').val('eterv');
		$('#field_u_pass').val('gwangwonc1');
		$('#field_u_pass_check').val('gwangwonc1');
		$('#field_u_name').val('최광원');
		$('#field_u_nickname').val('이터브');
		$('#field_u_email').val('eterv@naver.com');

		$('#field_u_phone').data('cleave').setRawValue('01075850004');

		passwordCheck.call(byId('field_u_pass'));
	});
</script>