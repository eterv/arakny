/**
 * Arakny Content Editor
 *
 * 머나먼 미래에 작업 필요. 라이센스 문제나 tinymce 가 너무 불필요하게 거대하고 트윅하기 쉽지 않기에.
 * 필요한 기능만 넣는 자체 에디터 필요. 단, 기술과 시간이 다소 필요하므로 나중에 작업하자!
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/*

HTML 코드 블록

<style>
	.a-editor-content { border: 1px solid #ddd; }
	.a-editor-content:focus { outline: none; }
</style>

<div class="a-editor-container" style="display: none;">
	<div class="ui icon buttons">
		<a class="ui button btn-align-left"><i class="align left icon"></i></a>
		<a class="ui button btn-align-center"><i class="align center icon"></i></a>
		<a class="ui button btn-align-right"><i class="align right icon"></i></a>
	</div>

	<div class="ui icon buttons">
		<a class="ui button btn-bold"><i class="bold icon"></i></a>
	</div>
	<div class="a-editor-content" contentEditable="true">
		<p></p>
	</div>
</div>
<textarea class="ai editor" style="display: none;"></textarea>

 */

(function () {
	function AEditor(selector) {
		var textarea = document.querySelector(selector);

		var container = document.createElement('div');
		container.classList.add('add');
		textarea.parentNode.insertBefore(container, textarea);

		// 임시
		var obj = document.querySelector('.a-editor-container');

		obj.querySelector('.btn-align-center');

		var editor = obj.querySelector('.a-editor-content');
		editor.style.height = '300px';

		document.execCommand('defaultParagraphSeparator', false, 'p');

		editor.addEventListener('keydown', function () {
			if (this.innerText == '') {
				this.innerHTML = '<p><br></p>';
			}
		});
		editor.addEventListener('change', function () {

		});
		editor.addEventListener('keydown', _.debounce(function () {
			//textarea.value = editor.innerHTML;
		}, 400));

		var observer = new MutationObserver(function (mutationList, observer) {
			forEach(mutationList, function (item, i) {

			});
			textarea.value = editor.innerHTML;
		});
		observer.observe(editor, {
			attributes: true,
			childList: true,
			characterData: true,
			subtree: true
		});

		// 버튼

		function replaceSelectedText(replacementText) {
			var sel, range;
			if (window.getSelection) {
				sel = window.getSelection();
				if (sel.rangeCount) {
					range = sel.getRangeAt(0);
					range.deleteContents();
					range.insertNode(document.createTextNode(replacementText));
				}
			} else if (document.selection && document.selection.createRange) {
				range = document.selection.createRange();
				range.text = replacementText;
			}
		}

		obj.querySelector('.btn-bold').addEventListener('click', function (evt) {
			//document.execCommand('bold');
			replaceSelectedText('최광원 ABC');
		});

		obj.querySelector('.btn-align-center').addEventListener('click', function (evt) {
			document.execCommand('bold');
		});
	}
})();