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
				<div class="control select">
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

			<div class="field w-1/2:s">
				<label>셀렉트 에어리어 2 3</label>
				<div class="control dropdown" data-type="country">
					<input type="hidden" placeholder="선택하세요">
					<nav class="menu"></nav>
				</div>
			</div>

			<div class="field w-1/2:s">
				<label>검색형 드롭다운</label>
				<div class="control dropdown search">
					<input type="hidden" placeholder="선택하세요">
					<nav class="menu">
						<div class="item" data-value="m">남자</div>
						<div class="item" data-value="f">여자</div>
						<div class="item" data-value="1">기린</div>
						<div class="item" data-value="2">코끼리</div>
						<div class="item" data-value="3">외계인</div>
						<div class="item" data-value="4">아담</div>
						<div class="item" data-value="5">하와</div>
						<div class="item" data-value="6">원숭이</div>
						<div class="item" data-value="7">코알라</div>
						<div class="item" data-value="8">타조</div>
						<div class="item" data-value="9">강아지</div>
					</nav>
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
				<div class="control dropdown" id="test-dd">
					<input type="hidden" name="gender" placeholder="성별" value="9">
					<nav class="menu">
						<div class="item" data-value="m">남자</div>
						<div class="item" data-value="f">여자</div>
						<div class="item" data-value="1">기린</div>
						<div class="item" data-value="2">코끼리</div>
						<div class="item" data-value="3">외계인</div>
						<div class="item" data-value="4">아담</div>
						<div class="item" data-value="5">하와</div>
						<div class="item" data-value="6">원숭이</div>
						<div class="item" data-value="7">코알라</div>
						<div class="item" data-value="8">타조</div>
						<div class="item" data-value="9">강아지</div>
						<div class="item" data-value="10">고양이</div>
						<div class="item" data-value="11">사자</div>
						<div class="item" data-value="12">호랑이</div>
						<div class="item" data-value="13">얼룩말</div>
						<div class="item" data-value="14">사슴</div>
						<div class="item" data-value="15">토끼</div>
					</nav>
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
						<button type="button" class="ai-b w-1" id="btn-test1">테스트1</button>
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
					<input type="hidden" name="remember" value="1">
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

		<style>
			.ftest { border: 1px solid #ddd; cursor: pointer; }
			.ftest:focus { outline: none; border: 1px solid red; }
			.ftest .child { position: absolute; }
		</style>
		<div class="ftest" tabindex="1">
			포커스 테스트
			<nav class="child">
				<div>자식1</div>
				<div>자식2</div>
			</nav>
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

		let dd = ai.Dropdown.init('#test-dd');

		$('#btn-test1').on('click', function () {
			console.log(dd.selectedText, dd.selectedValue);
			byId('test-dd').addClass('asd asad a2-2');
		});

		//new Dropdown();

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

<script>
	$(function () {

	});
</script>