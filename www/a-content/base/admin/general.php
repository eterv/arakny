<?php defined('_ARAKNY_') OR exit;
/**
 * Declare variables
 *
 * @var string $locale
 * @var array $data
 * @var array $fields
 * @var string $urlSubmit
 */

?>
<?= _adminSubHeader([ _adminIconButton('save') ]) ?>

<div class="ai-c max-w-900">
	<form id="form" class="ai-f">
		<?php foreach ($fields as $group => $fieldList): ?>
		<h3 class="ui dividing blue header" data-name="<?= $group ?>"><?= _g($group) ?></h3>

		<div class="grid fields">
		<?php
		foreach ($fieldList as $field) {
			if (is_array($field)) {
				echo '<div class="field"><div class="grid fields columns:m no-bottom-gap">';
				foreach ($field as $field2) {
					echo $field2;
				}
				echo '</div></div>';

			} else {
				echo $field;
			}
		}
		?>
		</div>
		<?php endforeach; ?>
	</form>

</div>

<script>
	(function() {
		const elForm = byId('form');
		const $form = $(elForm);

		const aiValidator = new AIValidator({
			form: elForm,
		})
		.on('form.submit', function () {
			const url = '<?= $urlSubmit ?>';
			ajaxPost(url, elForm, {},
				function (result) {
					toastSuccess('성공!');
				});

			return false;
		});

		// 폼 - 유효성 검증
		/*
		$form.parsley({
			debounce: 300,
			validationThreshold: 0,
			errorsWrapper: '<ul class="parsley-errors-list ui pointing prompt error label"></ul>',
			errorsContainer: function(pEle) {
				return pEle.$element.closest('.field');
			},
			excluded: 'input[type=button], input[type=submit], input[type=reset], [disabled]'
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
					toastSuccess('성공!^^');
				});

			return false;
		});*/

		// 상단 우측 메인 버튼

		$('.btn-save').on('click', function () {
			aiValidator.submit();
		});
	})();
</script>