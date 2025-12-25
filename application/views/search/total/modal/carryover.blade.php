<!-- Modal -->
<div class="modal fade" id="carryover-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">이월금 성정</h5>
				<button type="button" class="close carryover-modal-close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="carryover-modal-start-date">등록년도</label>
					<input type="text" class="form-control date" id="carryover-modal-start-date" name="carryover-modal-start-date" placeholder="등록년도">
				</div>
				<div class="form-group">
					<label for="carryover-modal-price-pre">전년도이월금</label>
					<input type="text" id="carryover-modal-price-pre" class="form-control" placeholder="전년도이월금"
						   style="text-align: right"
						   oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
						   maxlength="15"
					>
					<label class="form-check-label" style="position: absolute">
						<input type="checkbox" id="carryover-modal-price-pre-thousand-check" class="form-check-input">
						*천원
					</label>
					<div id="carryover-modal-price-pre-text"></div>
				</div>
				<div class="form-group">
					<label for="carryover-modal-price-next">차년도이월금</label>
					<input type="text" id="carryover-modal-price-next" class="form-control" placeholder="차년도이월금"
						   style="text-align: right"
						   oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
						   maxlength="15"
					>
					<label class="form-check-label" style="position: absolute">
						<input type="checkbox" id="carryover-modal-price-next-thousand-check" class="form-check-input">
						*천원
					</label>
					<div id="carryover-modal-price-next-text"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="carryover-modal-sel-btn" class="btn btn-primary">수정</button>
				<button type="button" class="btn btn-secondary carryover-modal-close">닫기</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
	const modalDate = new Date()
	const modalYear = modalDate.getFullYear()
	const modalStartDate = new Date(modalYear, 0, 1)

	async function modalSetting() {
		const year = $("#carryover-modal-start-date").val()
		$result = await $.ajax({
			url: '/search/carrayover_data',
			type: "post",
			dataType: "json",
			data: {
				year 	: year
			}
		})

		$("#carryover-modal-price-pre").val($result['data']['CARRYOVER_PRE'])
		const pre_price = $('#carryover-modal-price-pre').val()
		$('#carryover-modal-price-pre-text').html((pre_price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
		$("#carryover-modal-price-next").val($result['data']['CARRYOVER_NEXT'])
		const next_price = $("#carryover-modal-price-next").val()
		$('#carryover-modal-price-next-text').html((next_price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
	}

	// 달력처리
	$("#carryover-modal-start-date").datepicker({
		format 		: "yyyy",
		autoclose	: true,
		viewMode: "years",
		minViewMode: "years",
		onChange: async function() {
			await modalSetting()
		}
	}).datepicker("setDate", modalStartDate)

	$(document).on("change", "#carryover-modal-start-date", async function() {
		await modalSetting()
	})

	$(document).on("click", "#carryover-btn", async function() {
		await modalSetting()

		$("#carryover-modal").modal('show')
	})

	$(".carryover-modal-close").click(() => {
		$("#carryover-modal").modal('hide')
	})
	// 금액입력 엔터이벤트
	$(document).on('keyup', '#carryover-modal-price-pre', function(e) {
		e.stopImmediatePropagation()

		if (e.keyCode == 13) {
			$("#carryover-modal-price-next").focus()
		} else {
			const isChecked = $("#carryover-modal-price-pre-thousand-check")[0].checked
			let price = $('#carryover-modal-price-pre').val()
			if (isChecked) {
				price = price * 1000
			}

			$('#carryover-modal-price-pre-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
		}
	})
	$(document).on('change', '#carryover-modal-price-pre-thousand-check', function(e) {
		e.stopImmediatePropagation()

		const isChecked = $("#carryover-modal-price-pre-thousand-check")[0].checked
		let price = $('#carryover-modal-price-pre').val()
		if (isChecked) {
			price = price * 1000
		}

		$('#carryover-modal-price-pre-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
	})
	$(document).on('keyup', '#carryover-modal-price-next', function(e) {
		e.stopImmediatePropagation()

		if (e.keyCode == 13) {
			$("#carryover-modal-sel-btn").click()
		} else {
			const isChecked = $("#carryover-modal-price-next-thousand-check")[0].checked
			let price = $('#carryover-modal-price-next').val()
			if (isChecked) {
				price = price * 1000
			}

			$('#carryover-modal-price-next-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
		}
	})
	$(document).on('change', '#carryover-modal-price-next-thousand-check', function(e) {
		e.stopImmediatePropagation()

		const isChecked = $("#carryover-modal-price-next-thousand-check")[0].checked
		let price = $('#carryover-modal-price-next').val()
		if (isChecked) {
			price = price * 1000
		}

		$('#carryover-modal-price-next-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
	})
	$(document).on('click', '#carryover-modal-sel-btn', function () {
		const year = $("#carryover-modal-start-date").val()
		let pre = $('#carryover-modal-price-pre-text').html()
		if (pre !== undefined && pre !== '') {
			pre = pre.replaceAll(',', '').replaceAll('원', '')
		}
		let next = $('#carryover-modal-price-next-text').html()
		if (next !== undefined && next !== '') {
			next = next.replaceAll(',', '').replaceAll('원', '')
		}

		Swal.fire({
			title: '적용하시겠습니까?',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '적용',
			cancelButtonText: '취소'
		}).then(async (result) => {
			if (result.isConfirmed) {
				$result = await $.ajax({
					url: '/search/carrayover_update',
					type: "post",
					dataType: "json",
					data: {
						year 	: year,
						pre 	: pre,
						next 	: next
					}
				})

				alert($result.message)
				
				$("#carryover-modal").modal('hide')
				location.reload()
			}
		})
	})
</script>
<style>
	#carryover-modal-price-pre-text, #carryover-modal-price-next-text {
		float: right;
	}
</style>
