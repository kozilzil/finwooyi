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
								<h4 class="card-title">{{ $data['data']['title'] }}</h4>
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
</script>
