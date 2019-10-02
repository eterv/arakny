<?php defined('_ARAKNY_') OR exit;

use Arakny\Models\UsersModel as M;

/**
 * Declares variables
 *
 * @var string $locale
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
];

?>
<style>
	/*.ai.icon.form input[type=text]::placeholder { color: #ccc; }
	.ai.icon.form input[type=text]:focus::placeholder { color: #999; }*/

	.agreement { font-size: 0.9em; }
	.agreement a { color: #333; text-decoration: underline; cursor: pointer; }
	.agreement a.on { color: #00a0e0; font-weight: 500; }
	.agreement .box { display: none; height: 250px; margin-top: 20px; overflow: auto; }
	.agreement .box.on { display: block; }

	form { font-size: 16px !important; }

	.box1 { max-width: 100%; height: 50px; background: red; }
	.box2 { max-width: 100%; height: 50px; background: blue; }
	.box3 { max-width: 100%; height: 50px; background: #8a4ecb; }

	.b1 { margin-bottom: 1rem; background: #0c85d0; }

	.m-box1 { height: 50px; line-height: 50px; background: #5ecb52; text-align: center; }

	.ai-c { max-width: 1300px; }

</style>

<div class="ai-c max-w-1200 page-signup">
	<form id="form" method="post">

		<div class="b1 w:a">w:s</div>
		<div class="b1 w-1/2:s">w-1/2:s</div>

		<div class="grid gx-2/4 gy-2/4 bottom-gap">
			<div class="cell w"><div class="box3"></div></div>
			<div class="cell w-1/4"><div class="box3">내용</div></div>
			<div class="cell w-1">
				<div class="grid g-6/4:s">
					<div class="cell w-2/3"><div class="box3">내용2</div></div>
					<div class="cell w"><div class="box3"></div></div>
				</div>
			</div>
		</div>

		<div class="grid bottom-gap hidden::small">
			<div class="cell w-1/3:s"><div class="box1"></div></div>
			<div class="cell w:s"><div class="box1">내용</div></div>
			<div class="cell w-1/2:s"><div class="box1"></div></div>
			<div class="cell w-3/5:s"><div class="box1"></div></div>
			<div class="cell w-2/5:s"><div class="box1"></div></div>
			<div class="cell w-2/3:s"><div class="box1"></div></div>
		</div>

		<div class="grid g-2/4 pb-1">
			<div class="cell w w-1/3:s"><div class="box2"></div></div>
			<div class="cell w w-1/3:s"><div class="box2">100% 플렉서블</div></div>
			<div class="cell w-1 w-1/3:s"><div class="box2"></div></div>

			<div class="cell w-1"><div class="box2"></div></div>

			<div class="cell w"><div class="box2"></div></div>
			<div class="cell w"><div class="box2"></div></div>
		</div>

		<div class="grid g-2/4">
			<div class="cells">
				<div class="cell w w:s"><div class="box2"></div></div>
				<div class="cell w w:s"><div class="box2">cells 로 줄구분</div></div>
				<div class="cell w-1 w:s"><div class="box2"></div></div>
			</div>
			<div class="cells">
				<div class="cell w"><div class="box2"></div></div>
			</div>
			<div class="cells">
				<div class="cell w"><div class="box2"></div></div>
				<div class="cell w"><div class="box2"></div></div>
			</div>
		</div>

		<div class="grid gx-10/4 py-1 py-2:m multi1">
			<div class="cell w:m br:m b-gray-l2 b-solid">
				<div class="grid g-2/4">
					<div class="cell w-1"><div class="m-box1">Title 1</div></div>
					<div class="cell w"><div class="m-box1">1 Col</div></div>
					<div class="cell w"><div class="m-box1">2 Col</div></div>
					<div class="cell">
						<div class="grid gx-1 gy-2/4">
							<div class="cell w-1/4"><div class="m-box1">w-1/4</div></div>
							<div class="cell w-auto"><div class="m-box1">w-auto</div></div>
							<div class="cell w"><div class="m-box1">w</div></div>
							<div class="cell w-1"><div class="m-box1">w</div></div>
						</div>
					</div>
				</div>
			</div>

			<div class="cell w:m">
				<div class="grid g-2/4">
					<div class="cell w-1"><div class="m-box1">Title 2</div></div>
					<div class="cell w-1/2:s"><div class="m-box1">1 Col</div></div>
					<div class="cell w-1/2 w-1/4:s"><div class="m-box1">1 Col</div></div>
					<div class="cell w-1/2 w-1/4:s"><div class="m-box1">2 Col</div></div>
				</div>
			</div>
		</div>

		<div class="grid fields">
			<?= _captchaHtml(false, 38) ?>

			<div class="field">
				<label>텍스트 에어리어</label>
				<div class="control">
					<textarea><?= "테스트 이렇게\n하는 것이다!" ?></textarea>
				</div>
			</div>
			<div class="field">
				<label>텍스트 에어리어 2</label>
				<div class="control">
					<textarea rows="2"><?= "테스트 이렇게\n하는 것이다!" ?></textarea>
				</div>
			</div>

			<div class="field">
				<label>선택 옵션</label>
				<div class="controls box">
					<label class="control checkbox">
						<input type="checkbox">
						<span>If you can use ENGLISH!</span>
					</label>
					<label class="control checkbox">
						<input type="checkbox">
						<span>선택하자!</span>
					</label>
					<label class="control checkbox">
						<input type="checkbox">
						<span>한국어로 선택 Save 123</span>
					</label>
				</div>
			</div>

			<div class="field">
				<label>라디오 옵션</label>
				<div class="controls inline:m box">
					<label class="control checkbox radio">
						<input type="radio" name="radio1">
						<span>If you can use ENGLISH!</span>
					</label>
					<label class="control checkbox radio">
						<input type="radio" name="radio1">
						<span>선택하자!</span>
					</label>
					<label class="control checkbox radio">
						<input type="radio" name="radio1">
						<span>한국어로 선택 Save 123</span>
					</label>
				</div>
			</div>
		</div>

		<div class="grid fields horizontal:m">
			<div class="field">
				<label>HORIZONTAL</label>
				<div class="control input">
					<input type="text">
				</div>
			</div>

			<div class="field">
				<label>선택 옵션</label>
				<div class="control">
					<div class="controls box">
						<label class="control checkbox">
							<input type="checkbox">
							<span>If you can use ENGLISH!</span>
						</label>
						<label class="control checkbox">
							<input type="checkbox">
							<span>선택하자!</span>
						</label>
						<label class="control checkbox">
							<input type="checkbox">
							<span>한국어로 선택 Save 123</span>
						</label>
					</div>
					<div class="helptext">빨리 선택하는 것이 신상에 좋죠!</div>
				</div>
			</div>

			<div class="field">
				<label>Name 3</label>
				<div class="control dropdown">
					<select>
						<option value="">선택하세요</option>
						<option value="m">남자</option>
						<option value="f">여자</option>
					</select>
					<div class="helptext">빨리 선택하는 것이 신상에 좋죠!</div>
				</div>
			</div>

			<div class="field">
				<label>텍스트 에어리어 2</label>
				<div class="control">
					<textarea rows="4"><?= "테스트 이렇게\n하는 것이다!" ?></textarea>
				</div>
			</div>

			<div class="field">
				<label>HORIZON 3</label>
				<div class="control input">
					<input type="text">
				</div>
			</div>
		</div>

		<div class="grid fields">
			<div class="field">
				<label>텍스트 에어리어 1</label>
				<div class="control input">
					<input type="text">
				</div>
			</div>

			<div class="field">
				<label>셀렉트 에어리어 2 3</label>
				<div class="control dropdown">
					<select>
						<option value="">선택하세요</option>
						<option value="m">남자</option>
						<option value="f">여자</option>
					</select>
				</div>
			</div>

			<div class="field w:s">
				<label>텍스트 에어리어 2</label>
				<div class="control input">
					<input type="text">
				</div>
			</div>

			<div class="field w-1/3:s w-1/4:m">
				<label>셀렉트 에어리어 2</label>
				<div class="control dropdown">
					<select>
						<option value="">선택하세요</option>
						<option value="m">남자</option>
						<option value="f">여자</option>
					</select>
				</div>
			</div>

			<div class="field w-1/2:s">
				<label>텍스트 에어리어 3</label>
				<div class="control input">
					<input type="text" placeholder="Test Placeholder">
				</div>
			</div>

			<div class="field inline center mt-1">
				<button type="submit" class="ai-b primary w round"><?= _g('agree_and_signup') ?></button>
			</div>

			<div class="field">
				<div class="agreement">본인은 <a data-type="terms">사이트 이용약관</a> 및 <a data-type="privacy">개인정보 처리방침 안내</a>에 동의합니다.
					<div class="terms box"><?php include_once(baseThemePath('pages/terms.php')); ?></div>
					<div class="privacy box"><?php include_once(baseThemePath('pages/privacy.php')); ?></div>
				</div>
			</div>

			<div class="field">
				<div class="grid fields g-2/4 justify-center">
					<div class="field w-1/2 w-auto:m">
						<button type="button" class="ai-b w-1">테스트1</button>
					</div>
					<div class="field w-1/2 w-auto:m">
						<button type="button" class="ai-b red-l w-1 round">테스트2 버튼</button>
					</div>
					<div class="field w-1/3 w-auto:m">
						<button type="button" class="ai-b violet w-1 round">
							<i class="fas fa-pen"></i>
						</button>
					</div>
					<div class="field w-1/3 w-auto:m">
						<button type="button" class="ai-b teal w-1 round">
							?
						</button>
					</div>
				</div>
			</div>

		</div>

		<div class="grid fields">
			<div class="field w-1/3:s">
				<label class="control checkbox">
					<input type="checkbox" name="remember3" value="1">
					<span>If you can use ENGLISH!</span>
				</label>
			</div>

			<div class="field w-1/3:s">
				<label class="control checkbox toggle">
					<input type="checkbox" name="remember" id="remember2" value="1">
					<span><?= _g('rememberme') ?></span>
				</label>
			</div>

			<div class="field w-1/3:s">
				<label class="control checkbox bool toggle">
					<input type="hidden" name="remember">
					<span>LOGIN Save</span>
				</label>
			</div>

			<div class="field w-1/2:s">
				빈 필드
			</div>

			<div class="cell">
				<div class="hidden::big">작은 화면에서 보이는 요소입니다. (class - hidden::big)</div>
				<div class="hidden::small">큰 화면에서 보이는 요소입니다. (class - hidden::small)</div>

				<div class="hidden:m">작은 화면에서 보이는 요소입니다. (class - hidden:m)</div>
				<div class="hidden block:m">큰 화면에서 보이는 요소입니다. (class - hidden block:m)</div>
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
			var url = '';
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