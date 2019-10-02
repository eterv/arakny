/**
 * Korean - 한국어
 *
 * Made by Lucas Choi.
 */

/**
 *
 * 'en': {
		'Cancel': 'Cancel',
		'OK': 'OK',
		'No': 'No',
		'Yes': 'Yes',

		'Add': 'Add',
		'Edit': 'Edit',
		'Delete': 'Delete',

		'DeleteQuestion': 'Once you continue to delete {0}, then you can not undo. Do you want to continue?'
	},
 *
 */

var LCode = 'ko';
var LFullCode = 'ko-KR';

var L = {
	// 공용 명령
	_: Object.freeze({
		Cancel: '취소',
		OK: '확인',
		No: '아니오',
		Yes: '예',

		Add: '추가',
		Edit: '수정',
		Delete: '삭제',
	}),

	// 공용 메세지
	M: Object.freeze({
		CannotAdd: '새로 추가할 수 없습니다.',
		CannotEdit: '수정할 수 없습니다.',
		CannotDelete: '삭제할 수 없습니다.',
		Saved: '저장하였습니다.',
		Uploading: '업로드 중입니다...',
	}),

	// 공용 질문
	Q: Object.freeze({
		Delete: '{0} 삭제를 진행하면 되돌릴 수 없습니다. 계속 진행 하시겠습니까?',
	}),

	Docs: Object.freeze({
		Q: Object.freeze({
			Delete: '{0} 문서 삭제를 진행하면 되돌릴 수 없습니다. 계속 진행 하시겠습니까?',
			DeleteDocs: '여러개의 문서 삭제를 진행하면 되돌릴 수 없습니다. 계속 진행 하시겠습니까?',
		}),

		OnSaveSuccess: '저장되었습니다.',
	}),

	ImageUploader: Object.freeze({
		NotImage: '{0} 파일은 이미지 형식이 아니라서 업로드할 수 없습니다.'
	}),

	Userroles: Object.freeze({
		OnSaveSuccess: '저장되었습니다.',
	}),

	Validator: {
		required:			"{0} 필드를 입력해 주세요.",
		type: {
			number:			"전화번호 형식이 올바르지 않습니다.",
			integer:		"정수를 입력해 주세요.",
			digits:			"숫자를 입력해 주세요.",
			alphanum:		"알파벳과 숫자만 입력하실 수 있습니다."
		},
		notblank:       	"공백은 허용되지 않습니다.",
		min:            	"입력하신 내용이 %s보다 크거나 같아야 합니다.",
		max:            	"입력하신 내용이 %s보다 작거나 같아야 합니다.",
		range:				"입력하신 내용이 %s보다 크고 %s 보다 작아야 합니다.",


		exact_length:		"{0}의 길이는 {1} 글자이어야 합니다.",
		max_length:			"{0}의 값은 최대 {1} 글자 이내이어야 합니다.",
		min_length:  		"{0}의 값은 최소 {1} 글자 이상이어야 합니다.",
		length:				"{0}의 값은 {1} ~ {2} 글자 이내이어야 합니다.",

		matches:			"{0}의 값은 {1}의 값과 서로 일치해야 합니다.",
		differs:			"{0}의 값은 {1}의 값과 서로 달라야 합니다.",

		regex_match:        "{0}의 형식이 올바르지 않습니다.",

		valid_email:		"{0}의 값이 정상적인 이메일 주소가 아닙니다.",
		valid_ip:			"{0}의 값이 정상적인 IP 주소가 아닙니다.",
		valid_url:			"{0}의 값이 정상적인 URL 주소가 아닙니다.",

		unique: 			"이미 존재하는 {0} 입니다.",




		mincheck:			"최소한 %s개를 선택하여 주세요.",
		maxcheck:			"%s개 또는 그보다 적게 선택하여 주세요.",
		check:				"%s~%s개 이내로 선택하셔야 합니다.",


		equaltoPassword:	"비밀번호가 서로 일치하지 않습니다.",

		patternLogin:		"아이디는 알파벳, 숫자, 밑줄, 하이픈, 마침표, @ 심볼만 가능합니다.",

		uniqueLogin:		"아이디가 이미 존재합니다."
	},

	Parsley: {
		defaultMessage:		"입력하신 내용이 올바르지 않습니다.",
		type: {
			email:			"이메일 형식이 올바르지 않습니다.",
			url:			"URL 형식이 올바르지 않습니다.",
			number:			"전화번호 형식이 올바르지 않습니다.",
			integer:		"정수를 입력해 주세요.",
			digits:			"숫자를 입력해 주세요.",
			alphanum:		"알파벳과 숫자만 입력하실 수 있습니다."
		},
		notblank:       	"공백은 허용되지 않습니다.",
		required:      		"반드시 값이 필요합니다.",
		pattern:        	"입력하신 내용이 올바르지 않습니다.",
		min:            	"입력하신 내용이 %s보다 크거나 같아야 합니다.",
		max:            	"입력하신 내용이 %s보다 작거나 같아야 합니다.",
		range:				"입력하신 내용이 %s보다 크고 %s 보다 작아야 합니다.",
		minlength:  		"최소한 %s자 이상으로 입력하셔야 합니다.",
		maxlength:			"%s자 이내로 입력하셔야 합니다.",
		length:				"%s~%s자 이내로 입력하셔야 합니다.",
		mincheck:			"최소한 %s개를 선택하여 주세요.",
		maxcheck:			"%s개 또는 그보다 적게 선택하여 주세요.",
		check:				"%s~%s개 이내로 선택하셔야 합니다.",
		equalto:			"값이 서로 일치하지 않습니다.",

		equaltoPassword:	"비밀번호가 서로 일치하지 않습니다.",

		patternLogin:		"아이디는 알파벳, 숫자, 밑줄, 하이픈, 마침표, @ 심볼만 가능합니다.",

		unique: 			"이미 존재합니다.",
		uniqueLogin:		"아이디가 이미 존재합니다."
	}
};

/**
 * 	Validation errors messages for Parsley
 * 	Load this after Parsley
 */
//Parsley.addMessages(LCode, L.Parsley);
//Parsley.setLocale(LCode);


/**
 * 	Fomantic UI - Form Validation Default Prompt
 */

$.fn.form.settings.prompt = {
	empty                : '{name} 필드를 입력해 주세요',
	checked              : '{name} 필드는 반드시 체크되어야 합니다',
	email                : '{name} 필드는 정상적인 이메일이어야 합니다',
	url                  : '{name} 필드는 정상적인 URL이어야 합니다',
	regExp               : '{name} 필드의 형식이 올바르지 않습니다',
	integer              : '{name} 필드는 정수이어야 합니다',
	decimal              : '{name} 필드는 10진수이어야 합니다',
	number               : '{name} 필드는 숫자이어야 합니다',
	is                   : '{name}의 값은 \'{ruleValue}\' 이어야 합니다',
	isExactly            : '{name}의 값은 대소문자 정확히 \'{ruleValue}\' 이어야 합니다',
	not                  : '{name}의 값은 \'{ruleValue}\' 이어서는 안됩니다',
	notExactly           : '{name}의 값은 대소문자 정확히 \'{ruleValue}\' 이어서는 안됩니다',
	contains             : '{name}의 값에 \'{ruleValue}\' 값을 포함해야 합니다',
	containsExactly      : '{name}의 값에 대소문자 정확히 \'{ruleValue}\' 값을 포함해야 합니다',
	doesntContain        : '{name}의 값에 \'{ruleValue}\' 값을 포함할 수 없습니다',
	doesntContainExactly : '{name}의 값에 대소문자 정확히 \'{ruleValue}\' 값을 포함할 수 없습니다',
	minLength            : '{name} 필드의 길이는 최소 {ruleValue} 자 이상이어야 합니다',
	length               : '{name} 필드의 길이는 {ruleValue} 자이어야 합니다',
	exactLength          : '{name} 필드의 길이는 {ruleValue} 자이어야 합니다',
	maxLength            : '{name} 필드의 길이는 {ruleValue} 자를 초과할 수 없습니다',
	match                : '{name} 필드는 반드시 {ruleValue} 필드의 값과 일치해야 합니다',
	different            : '{name} 필드는 반드시 {ruleValue} 필드의 값과 달라야 합니다',
	creditCard           : '{name} 필드는 정상적인 신용카드번호이어야 합니다',
	minCount             : '{name} 필드는 최소 {ruleValue} 개를 선택해야 합니다',
	exactCount           : '{name} 필드는 반드시 {ruleValue} 개를 선택해야 합니다',
	maxCount             : '{name} 필드는 최대 {ruleValue} 개를 선택해야 합니다',

	matchPassword		 : '비밀번호 필드의 값과 일치하지 않습니다',
	isUnique			 : '이미 존재하는 {name} 입니다',
	checkLoginId		 : '이미 존재하는 아이디 입니다',
};
