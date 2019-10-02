/**
 * Arakny User Interface
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/**
 * 	Arakny UI - Form
 */

(function () {
	function PromiseTest1() {
		var func = [
			[ 'A', 'aa2' ],
			[ 'B', 'bb2' ],
			[ 'C', 'cc2' ],
			[ 'D', 'dd2' ],
		];

		function test2(name, name2) {
			console.log('name', name, name2);
		}

		// Promise Chaining 모델
		function test1() {
			var promises = $.when();

			func.forEach(function (items, i) {
				promises = promises.then(function (data) {
					var def = $.Deferred();

					// resolve 되어야 다음 작업을 진행한다.
					setTimeout(function () {
						test2.apply(null, items);

						if (i == 1) {

							$.when().then(function () {
								var def2 = $.Deferred();

								setTimeout(function () {
									console.log('중간1');

									def2.resolve();
								}, Math.floor(Math.random() * 500) + 100 );

								//return $.Deferred().reject('aaa');
								//throw 'T.T';

								return def2.promise();

							}).then(function () {
								var def2 = $.Deferred();

								setTimeout(function () {
									console.log('중간2');

									def2.reject();
									//def2.resolve();
								}, Math.floor(Math.random() * 500) + 100 );

								return def2.promise();
							}).then(function () {
								var def2 = $.Deferred();

								setTimeout(function () {
									console.log('중간3');

									def2.resolve();
								}, Math.floor(Math.random() * 500) + 100 );

								return def2.promise();
							}).then(function () {
								def.resolve();
							}).catch(function () {
								console.log('중간에서 에러가!');
								def.reject();
							});

						} else {
							def.resolve('d1 하하');
						}
					}, Math.floor(Math.random() * 200) + 100 );

					return def.promise();
				});
			});

			promises.then(
				function () {
					var def = $.Deferred();
					console.log('성공!!!');

					def.resolve();

					return def.promise();
				}).catch(function () {
				var def = $.Deferred();
				console.log('실패!!!');

				def.reject();

				return def.promise();
			});

			// pending 상태에서 벗어날때까지 함수 종료를 무기한 기다린다.
			//while (promises.state() === 'pending') { }
			setTimeout(function () {
				console.log(promises.state());
			}, 500);
			setTimeout(function () {
				console.log(promises.state());
			}, 3000);

			//console.log(promises.state());

			return (promises.state() === 'resolved');
		}

		console.log('test1 Function :: ' + test1());
	}
	window.PromiseTest1 = PromiseTest1;

})();