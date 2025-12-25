<!-- Modal -->
<div class="modal fade" id="account-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">계좌선택</h5>
				<button type="button" class="close account-modal-close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" id="account-modal-nick-name" class="form-control" placeholder="별명입력" />
					<div class="card-body" id="account-modal-data-list-div">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div style="position: absolute;left: 20px">
					<button type="button" id="account-modal-reg-btn" class="btn btn-info">신규등록</button>
					<button type="button" id="account-modal-del-btn" class="btn btn-danger">계좌삭제</button>
				</div>
				<button type="button" id="account-modal-sel-btn" class="btn btn-primary">선택</button>
				<button type="button" class="btn btn-secondary account-modal-close">닫기</button>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="account-reg-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">신규등록</h5>
				<button type="button" class="close account-reg-modal-close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="account-reg-modal-nick-name">계좌별명</label>
					<input type="text" id="account-reg-modal-nick-name" class="form-control" />
				</div>
				<div class="form-group">
					<label for="account-reg-modal-bank">은행</label>
					<select id="account-reg-modal-bank" class="form-control">
					</select>
				</div>
				<div class="form-group">
					<label for="account-reg-modal-holder">예금주</label>
					<input type="text" id="account-reg-modal-holder" class="form-control" />
				</div>
				<div class="form-group">
					<label for="account-reg-modal-number">계좌번호</label>
					<input type="text" id="account-reg-modal-number" class="form-control" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="account-reg-modal-reg-btn" class="btn btn-primary">등록</button>
				<button type="button" class="btn btn-secondary account-reg-modal-close">닫기</button>
			</div>
		</div>
	</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
	$("#bank-select").click(() => {
		bankList(1)

		$("#account-modal").modal('show')
	})
	$(document).on('keyup', "#account-modal-nick-name", () => {
		bankList(1)
	})
	async function bankList(page) {
		const nickname = $("#account-modal-nick-name").val()
		const result = await $.ajax({
			url: '/expense/account',
			type: "post",
			dataType: "html",
			data: {
				page 	 : page,
				nickname : nickname
			}
		})
		$("#account-modal-data-list-div").html(result)
	}
	$(".account-modal-close").click(() => {
		$("#account-modal").modal('hide')
	})
	$("#account-modal-sel-btn").click(() => {
		const value = $("input[name='account-modal-radios']:checked").val()
		let html = $("input[name='account-modal-radios']:checked").attr('data-nickname')
		html += ' (' + $("input[name='account-modal-radios']:checked").attr('data-name')
		html += ' / ' + $("input[name='account-modal-radios']:checked").attr('data-holder')
		html += ' / ' + $("input[name='account-modal-radios']:checked").attr('data-account') + ')'
		$("#account-text").html(html)
		$("#account-no").val(value)
		$("#account-modal").modal('hide')
	})

	$("#account-modal-del-btn").click(async() => {
		const value = $("input[name='account-modal-radios']:checked").val()
		if (value == undefined) {
			Swal.fire({
				title: '삭제할 계좌를 선택하세요.',
				icon: 'error',
				confirmButtonText: '확인'
			})
			return false
		}

		Swal.fire({
			title: '삭제하시겠습니까?',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '삭제',
			cancelButtonText: '취소'
		}).then(async (result) => {
			if (result.isConfirmed) {
				const result = await $.ajax({
					url: '/expense/del_account',
					type: "post",
					dataType: "json",
					data: {
						no 	: value
					}
				})

				if (result.status == false) {
					Swal.fire({
						title: result.message,
						icon: 'error',
						confirmButtonText: '확인'
					})
					return false
				} else {
					Swal.fire({
						title: result.message,
						icon: 'info',
						confirmButtonText: '확인'
					}).then(() => {
						bankList(1)
					})
				}
			}
		})
	})

	$("#account-modal-reg-btn").click(async() => {
		$(".account-modal-close").click()
		const result = await $.ajax({
			url: '/expense/bank_list',
			type: "post",
			dataType: "json"
		})
		let html = ''
		for(let idx = 0; idx < result.data.length; idx++) {
			html += `<option value="${result.data[idx]['CODE']}">${result.data[idx]['NAME']}(${result.data[idx]['CODE']})</option>`
		}
		$("#account-reg-modal-bank").html(html)
		$("#account-reg-modal").modal('show')
	})
	$(".account-reg-modal-close").click(() => {
		$("#account-reg-modal").modal('hide')
	})
	$("#account-reg-modal-reg-btn").click(() => {
		const nickname = $("#account-reg-modal-nick-name").val()
		const bank = $("#account-reg-modal-bank option:selected").val()
		const holder = $("#account-reg-modal-holder").val()
		const number = $("#account-reg-modal-number").val()

		if ( nickname == '' ) {
			Swal.fire({
				title: '계좌별명을 입력하세요.',
				icon: 'error',
				confirmButtonText: '확인'
			})
			return false
		}
		if ( holder == '' ) {
			Swal.fire({
				title: '예금주를 입력하세요.',
				icon: 'error',
				confirmButtonText: '확인'
			})
			return false
		}
		if ( number == '' ) {
			Swal.fire({
				title: '계좌번호를 입력하세요.',
				icon: 'error',
				confirmButtonText: '확인'
			})
			return false
		}

		Swal.fire({
			title: '등록하시겠습니까?',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '등록',
			cancelButtonText: '취소'
		}).then(async (result) => {
			if (result.isConfirmed) {
				await $.ajax({
					url: '/expense/reg_account',
					type: "post",
					dataType: "json",
					data: {
						nickname 	: nickname,
						bank 		: bank,
						holder 		: holder,
						number 		: number
					}
				})
				$("#account-reg-modal-nick-name").val('')
				$("#account-reg-modal-bank option:selected").val('')
				$("#account-reg-modal-holder").val('')
				$("#account-reg-modal-number").val('')

				$("#account-reg-modal").modal('hide')
				$("#bank-select").click()
			}
		})
	})
</script>
<style>
	#price-text {
		float: right;
	}
</style>
