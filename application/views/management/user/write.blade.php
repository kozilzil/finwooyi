@include('/_parts/topMenu')
<div class="container-fluid page-body-wrapper">
	@include('/_parts/sliderMenu')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="row">
				<div class="col-12 grid-margin stretch-card">
					<div class="card card-rounded">
						<div class="card">
							<div class="card-body">
								<h4 class="card-title">
									{{ $data['data']['title'] }}
									@if( array_key_exists('info', $data['data']))
										<button id="auth-btn" class="btn btn-success fullwidth">권한설정</button>
									@endif
								</h4>
								<div class="forms-sample">
									<div class="form-group">
										<label for="name">이름</label>
										<input type="text" class="form-control" id="name" placeholder="이름" value="{{ array_key_exists('info', $data['data']) ? $data['data']['info']['NAME'] : '' }}">
									</div>
									<div class="form-group">
										<label for="office">직책</label>
										<input type="text" class="form-control" id="office" placeholder="직책" value="{{ array_key_exists('info', $data['data']) ? $data['data']['info']['OFFICE'] : '' }}">
									</div>
									<div class="form-group">
										<label for="id">아이디</label>
										<input type="text" class="form-control" id="id" placeholder="아이디" value="{{ array_key_exists('info', $data['data']) ? $data['data']['info']['ID'] : '' }}">
									</div>
									<div class="form-group">
										<label for="password">비밀번호</label>
										<input type="password" class="form-control" id="password" placeholder="비밀번호" value="">
									</div>
									<div class="form-group">
										<label for="password-check">비밀번호 확인</label>
										<input type="password" class="form-control" id="password-check" placeholder="비밀번호 체크" value="">
									</div>
									<div class="form-group">
										<label for="explanation">설명</label>
										<input type="text" class="form-control" id="explanation" placeholder="설명" value="{{ array_key_exists('info', $data['data']) ? $data['data']['info']['EXPLANATION'] : '' }}">
									</div>
									@if( array_key_exists('info', $data['data']))
										<button type="button" id="update" class="btn btn-primary me-2" data-value="{{ $data['data']['info']['NO'] }}">수정</button>
										<button type="button" id="delete" class="btn btn-danger me-2" data-value="{{ $data['data']['info']['NO'] }}">삭제</button>
									@else
										<button type="button" id="register" class="btn btn-primary me-2">등록</button>
									@endif
									<a href="/management/user" class="btn btn-light">취소</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@if( array_key_exists('info', $data['data']))
	@include('/management/user/modal/auth_form')
@endif

<script>
	function pw_check() {
		const password = $('#password').val()
		const passwordCheck = $('#password-check').val()

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

		if (password != passwordCheck) {
			Swal.fire({
				title 				: '비밀번호를 확인해주세요.',
				icon 				:'error',
				confirmButtonText	: '확인'
			}).then((result) => {
				if (result.isConfirmed) {
					$('#password').val('')
					$('#password-check').val('')
					$('#password').focus()
				}
			})
			return false
		}

		return true
	}

	$(document).on('click', '#register', (e) => {
		e.stopImmediatePropagation()

		const name = $('#name').val()
		if (name.trim().length == 0) {
			Swal.fire({
				title 				: '이름을 입력하세요.',
				icon 				:'error',
				confirmButtonText	: '확인'
			}).then((result) => {
				if (result.isConfirmed) {
					$('#name').val('')
					$('#name').focus()
				}
			})
			return false
		}

		const password = $('#password').val()
		let check = true
		if (password != '') {
			check = pw_check()
		}
		if (check) {
			Swal.fire({
				title: '등록하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '등록',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					const id = $('#id').val()
					const name = $('#name').val()
					const password = $('#password').val()
					const office = $('#office').val()
					const explanation = $('#explanation').val()

					const result = $.ajax({
						url: '/management/user_register',
						type: "post",
						dataType: "json",
						async: false,
						data: {
							id: id,
							password: password,
							name: name,
							office: office,
							explanation: explanation
						}
					})

					if (result.responseJSON.status == true) {
						Swal.fire({
							title: '적용되었습니다.',
							icon: 'success',
							confirmButtonText: '확인'
						}).then(() => {
							location.href = `/management/user_write/${result.responseJSON.data['NO']}`
						})
					} else {
						Swal.fire({
							title: '오류가 발생하였습니다.',
							icon: 'error',
							confirmButtonText: '확인'
						}).then(() => {
							location.reload()
						})
					}
				}
			})
		}
	})

	$(document).on('click', '#update', function(e) {
		e.stopImmediatePropagation()

		const password = $('#password').val()
		let check = true
		if (password != '') {
			check = pw_check()
		}
		if (check) {
			Swal.fire({
				title: '수정하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '수정',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					const no = $("#update").attr('data-value')
					const id = $('#id').val()
					const name = $('#name').val()
					const password = $('#password').val()
					const office = $('#office').val()
					const explanation = $('#explanation').val()

					const result = $.ajax({
						url: '/management/user_update',
						type: "post",
						dataType: "json",
						async: false,
						data: {
							no: no,
							id: id,
							password: password,
							name: name,
							office: office,
							explanation: explanation
						}
					})

					if (result.responseJSON.status == true) {
						Swal.fire({
							title: '적용되었습니다.',
							icon: 'success',
							confirmButtonText: '확인'
						}).then(() => {
							location.reload()
						})
					} else {
						Swal.fire({
							title: '오류가 발생하였습니다.',
							icon: 'error',
							confirmButtonText: '확인'
						}).then(() => {
							location.reload()
						})
					}
				}
			})
		}
	})

	$(document).on('click', '#delete', function (e) {
		e.stopImmediatePropagation()

		Swal.fire({
			title: '삭제하시겠습니까?',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '삭제',
			cancelButtonText: '취소'
		}).then((result) => {
			if (result.isConfirmed) {
				const no = $("#delete").attr('data-value')

				const result = $.ajax({
					url: '/management/user_delete',
					type: "post",
					dataType: "json",
					async: false,
					data: {
						no: no
					}
				})

				if (result.responseJSON.status == true) {
					Swal.fire({
						title: '삭제되었습니다.',
						icon: 'success',
						confirmButtonText: '확인'
					}).then(() => {
						location.href = '/management/user'
					})
				} else {
					Swal.fire({
						title: '오류가 발생하였습니다.',
						icon: 'error',
						confirmButtonText: '확인'
					}).then(() => {
						location.reload()
					})
				}
			}
		})
	})

	$(document).on('click', '#auth-btn', function() {
		const userNo = $("#update").attr('data-value')
		$.ajax({
			url: '/management/auth_data/',
			data: {'no': userNo},
			dataType: 'html',
			type: 'POST'
		}).done(function(result) {
			$("#auth-modal-contents").html(result)
			$("#auth-modal").modal('show')
			fnAuthListAfter('view')
			fnAuthListAfter('update')
			fnAuthListAfter('admin')

			// 저장 버튼 핸들러 갱신
			$(document).off('click', '#modal-save-btn').on('click', '#modal-save-btn', function() {
				const role = $('#role-select').val()
				const auths = []
				$("select[id^='auth-parent-'], select[id^='auth-child-']").each(function() {
					const val = $(this).val()
					const menuNo = $(this).find('option:selected').data('no')
					if (val !== '' && menuNo) {
						auths.push({menu_no: menuNo, auth: val})
					}
				})

				$.ajax({
					url: '/management/user_auth_save',
					type: 'POST',
					dataType: 'json',
					data: {
						no: userNo,
						role: role,
						auths: JSON.stringify(auths)
					}
				}).done(function(res) {
					if (res.status) {
						Swal.fire({
							title: '적용되었습니다.',
							icon: 'success',
							confirmButtonText: '확인'
						}).then(() => {
							$("#auth-modal").modal('hide')
							location.reload()
						})
					} else {
						Swal.fire({
							title: '저장에 실패했습니다.',
							icon: 'error',
							confirmButtonText: '확인'
						})
					}
				}).fail(function() {
					Swal.fire({
						title: '저장 중 오류가 발생했습니다.',
						icon: 'error',
						confirmButtonText: '확인'
					})
				})
			})
		})
	})
	function fnAuthListAfter(type) {
		const count = $(`.checkbox_${type}`).length
		let chk_cnt = 0

		$(`.checkbox_${type}`).each(function(idx, item) {
			if($(this).is(':checked')) {
				chk_cnt++
			}
		})

		if (count == chk_cnt) {
			$(`#all_chk_${type}`).prop("checked", true);
		} else {
			$(`#all_chk_${type}`).prop("checked", false)
		}

		// 전체 체크박스 선택처리
		$(`#all_chk_${type}`).click(function() {
			if($(this).is(":checked")) {
				$(`.checkbox_${type}`).each(function(idx, item) {
					$(this).prop("checked", true)
				})
			} else {
				$(`.checkbox_${type}`).each(function(idx, item) {
					$(this).prop("checked", false)
				})
			}

			if ($(this).attr('id') == 'all_chk_update') {
				$("#all_chk_view").click()
			} else if($(this).attr('id') == 'all_chk_admin') {
				$("#all_chk_update").click()
			}
		})

		// 개별 체크박스 선택처리
		$(`.checkbox_${type}`).click(function() {
			let count = $(`.checkbox_${type}`).length
			let chk_cnt = 0
			let parent_value = null

			if ($(this).hasClass(`child_chk_${type}`)) {
				parent_value = $(this).data('parent')
			}

			$(`.checkbox_${type}`).each(function(idx, item) {
				// 상위권한은 무조건 설정되도록
				if (parent_value != null) {
					if($(this).data('value') == parent_value) {
						$(this).prop("checked", true)
					}


					let childChkCnt = 0
					$(`.child_chk_${type}`).each(function(idx, item) {
						if ( $(this).data("parent") == parent_value && $(this).is(":checked")) {
							childChkCnt++
						}
					})

					if (childChkCnt == 0) {
						if($(this).data('value') == parent_value) {
							$(this).prop("checked", false)
						}
					}
				} else {
					const value = $(this).data("value")
					if(!$(this).is(":checked")) {
						$(`.checkbox_${type}`).each(function(idx, item) {
							if ( $(this).data("parent") == value) {
								$(this).prop("checked", false)
							}
						})
					}
				}

				if($(this).is(':checked')) {
					chk_cnt++
				}
			})


			if (count == chk_cnt) {
				$(`#all_chk_${type}`).prop("checked", true);
			} else {
				$(`#all_chk_${type}`).prop("checked", false);
			}

			if ($(this).hasClass(`parent_chk_${type}`)) {
				const parent = $(this).data('value')
				if(!$(this).is(":checked")) {
					$(`.child_chk_${type}`).each(function(idx, item) {
						if ( $(this).data("parent") == parent) {
							$(this).prop("checked", false)
						}
					})
				} else {
					$(`.child_chk_${type}`).each(function(idx, item) {
						if ( $(this).data("parent") == parent) {
							$(this).prop("checked", true)
						}
					})
				}
			}

			// parent_chk_view
			if (type == 'update') {
				if ($(this).hasClass('parent_chk_update')) {
					const no = $(this).attr('data-value')

					$(".parent_chk_view").each(function(idx, item) {
						if ($(item).attr('data-value') == no) {
							$(item).click()
						}
					})

					$(".parent_chk_admin").each(function(idx, item) {
						if ($(item).attr('data-value') == no) {
							if ($(item).is(":checked")) {
								console.log('1')
							} else {
								console.log('2')
							}
							//$(item).click()
						}
					})
				}

				if ($(this).hasClass('child_chk_update')) {
					const no = $(this).attr('data-value')
					const parent = $(this).attr('data-parent')

					$(".child_chk_view").each(function(idx, item) {
						if ($(item).attr('data-value') == no && $(item).attr('data-parent') == parent) {
							$(item).click()
						}
					})
				}
			} else if(type == 'admin') {
				if ($(this).hasClass('parent_chk_admin')) {
					const no = $(this).attr('data-value')

					$(".parent_chk_update").each(function(idx, item) {
						if ($(item).attr('data-value') == no) {
							$(item).click()
						}
					})
				}

				if ($(this).hasClass('child_chk_admin')) {
					const no = $(this).attr('data-value')
					const parent = $(this).attr('data-parent')

					$(".child_chk_update").each(function(idx, item) {
						if ($(item).attr('data-value') == no && $(item).attr('data-parent') == parent) {
							$(item).click()
						}
					})
				}
			} else {

			}
		})


		// if (type == 'update') {
		// 	const type = $(this).attr('data-type')
		//
		//
		// 	$(`.checkbox_${type}`).each(function(idx, item) {
		//
		// 	})
		// }
	}

	$(document).on('click', '#modal-save-btn', function() {

	})
</script>
