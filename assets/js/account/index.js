"use strict"

$(document).on('click', '#login-btn', () => {
	const id = $('#id').val()
	const password = $('#password').val()

	if (id.trim().length == 0) {
		Swal.fire({
			title 				: '아이디를 입력하세요.',
			icon 				:'error',
			confirmButtonText	: '확인'
		}).then((result) => {
			if (result.isConfirmed) {
				$('#id').val('')
				$('#id').focus()
			}
		})
		return false
	}

	if (password.trim().length == 0) {
		Swal.fire({
			title 				: '비밀번호를 입력하세요.',
			icon 				:'error',
			confirmButtonText	: '확인'
		}).then((result) => {
			if (result.isConfirmed) {
				$('#password').val('')
				$('#password').focus()
			}
		})
		return false
	}

	$('#login-form').action()
})
