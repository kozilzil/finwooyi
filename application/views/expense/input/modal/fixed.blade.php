<!-- Modal -->
<div class="modal fade" id="fixed-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">고정지출선택</h5>
				<button type="button" class="close fixed-modal-close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" class="form-control-sm date" id="fixed-modal-start-date" name="start-date" placeholder="등록일">
					<input type="text" id="fixed-modal-contents" class="form-control" placeholder="고정지출명" />
					<select id="fixed-modal-weekly" name="fixed-modal-weekly" class="form-control">
						@for($idx = 0; $idx < 5; $idx++)
							<option value="{{ $idx+1 }}">{{ $idx+1 }}주차</option>
						@endfor
					</select>

					<div id="fixed-modal-data-list-div">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="fixed-modal-sel-btn" class="btn btn-primary">등록</button>
				<button type="button" class="btn btn-secondary fixed-modal-close">닫기</button>
			</div>
		</div>
	</div>
</div>


<script>
	const date = new Date()
	const year = date.getFullYear()
	const fixedStartDate = date.getFullYear() + '-' + ('0' + (date.getMonth()+1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2)

	// 달력처리
	$("#fixed-modal-start-date").datepicker({
		format 		: "yyyy",
		autoclose	: true,
		viewMode: "years",
		minViewMode: "years"
	}).datepicker("setDate", fixedStartDate)

	$(document).on('click', '#fixed-select', async () => {
		await fixedList(1)

		$("#fixed-modal").modal('show')
	})

	$(document).on('keyup', "#fixed-modal-contents", async () => {
		await fixedList(1)
	})
	$(document).on('change', "#fixed-modal-weekly", async () => {
		await fixedList(1)
	})
	$(document).on('change', "#fixed-modal-start-date", async () => {
		await fixedList(1)
	})

	async function fixedList(page) {
		const contents = $("#fixed-modal-contents").val()
		const weekly = $("#fixed-modal-weekly option:selected").val()
		const year = $("#fixed-modal-start-date").val()
		const result = await $.ajax({
			url: '/expense/fixed_popup_list',
			type: "post",
			dataType: "html",
			data: {
				page 	 	: page,
				contents 	: contents,
				weekly		: weekly,
				year		: year
			}
		})
		$("#fixed-modal-data-list-div").html(result)
	}
	$(".fixed-modal-close").click(() => {
		$("#fixed-modal").modal('hide')
	})

	$(document).on('click', '#all-select-btn', async () => {
		const isChecked = $("#all-select-btn").is(':checked')
		if (isChecked) {
			$(".select-btn").each((idx, element) => {
				$(element).prop('checked', true)
			})
		} else {
			$(".select-btn").each((idx, element) => {
				$(element).prop('checked', false)
			})
		}
	})

	$(document).on('click', '.select-btn', async () => {
		let totalCnt = 0
		let selCnt = 0
		$(".select-btn").each((idx, element) => {
			totalCnt++
			if ($(element).is(':checked')) {
				selCnt++
			}
		})

		if ( totalCnt == selCnt ) {
			$("#all-select-btn").prop('checked', true)
		} else {
			$("#all-select-btn").prop('checked', false)
		}
	})

	$(document).on('click', '#fixed-modal-sel-btn', async () => {
		let selCnt = 0
		$(".select-btn").each((idx, element) => {
			if ($(element).is(':checked')) {
				selCnt++
			}
		})

		if ( selCnt == 0 ) {
			Swal.fire({
				title: '등록할 고정지출을 선택하세요.',
				icon: 'error',
				confirmButtonText: '확인'
			})
			return
		} else {
			Swal.fire({
				title: '등록하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '등록',
				cancelButtonText: '취소'
			}).then(async (result) => {
				if (result.isConfirmed) {
					await registerFixed()

					Swal.fire({
						title: '등록되었습니다.',
						icon: 'success',
						confirmButtonText: '확인'
					})

					$(".fixed-modal-close").click()

					await expense_list(1)
				}
			})
		}
	})

	async function registerFixed() {
		$(".select-btn").each(async (idx, element) => {
			if ($(element).is(':checked')) {
				const regDate = $("#start-date").val()
				const typeNo = $(element).attr('data-child')
				const type = $(element).attr('data-type')
				const contents = $(element).attr('data-contents')
				const recipient = $(element).attr('data-recipient')
				const price = $(element).attr('data-price').replaceAll(',', '')

				const payMethod = type
				let accountNo = ''
				if (type == 'bank') {
					accountNo = $(element).attr('data-accountno')
				}

				await $.ajax({
					url: '/expense/expense_register',
					type: "post",
					dataType: "json",
					async: false,
					data: {
						type		: typeNo,
						regDate		: regDate,
						contents	: contents,
						price		: price,
						payMethod	: payMethod,
						accountNo	: accountNo,
						recipient	: recipient
					}
				})
			}
		})
	}

	$(document).on('keyup', "#fixed-modal-contents", async () => {
		await fixedList(1)
	})
</script>
