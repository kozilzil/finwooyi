$(document).ready(async function (e) {
	$('#contents').focus()
	$("#new-div").hide()

	await fixedSetting()
	await fixed_list(1)
})

async function fixedSetting() {
	// 헌금대분류 가져오기
	const typeData = {
		'is-income' : 'N',
		'parent'	: 0,
		'year'		: $("#start-date").val()
	}
	const result = offering_list(typeData);
	result.then((resolve) => {
		if (resolve.status == true) {
			let html = ''
			for(let idx =0; idx < resolve.data.length; idx++) {
				html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
			}
			$("#type").html(html)

			const target = $("#type").attr('data-target')

			detailTypeChange($(`#${target}`))
		} else {
			$("#type").html('')
			$("#detail-type").html('')
		}
	})
}

// 달력처리
$("#start-date").datepicker({
	"format" 	: "yyyy",
	"autoclose"	: true,
	viewMode: "years",
	minViewMode: "years"

}).datepicker("setDate", 'now')
$(document).on('change', '#start-date', async function() {
	await fixedSetting()
	await fixed_list(1)
})

// 내역입력 엔터이벤트
$(document).on('keyup', '#contents', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		$("#price").focus()
	}
})

// 금액입력 엔터이벤트
$(document).on('keyup', '#price', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		$("#etc").focus()
		return
	} else {
		const isChecked = $("#price-thousand-check")[0].checked
		let price = $('#price').val()
		if (isChecked) {
			price = price * 1000
		}

		$('#price-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
	}
})
$(document).on('change', '#price-thousand-check', function(e) {
	e.stopImmediatePropagation()

	const isChecked = $("#price-thousand-check")[0].checked
	let price = $('#price').val()
	if (isChecked) {
		price = price * 1000
	}

	$('#price-text').html((price + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') + '원')
})
// 지급등록 버튼 이벤트
$(document).on('click', '#register-btn', function(e) {
	e.stopImmediatePropagation()

	const contents = $('#contents').val()
	const price = $('#price').val()

	if (contents.trim().length == 0) {
		$('#contents').focus()
		Swal.fire({
			title: '지급내역를 입력하세요.',
			icon: 'error',
			confirmButtonText: '확인'
		})
		return
	}
	if (price == 0) {
		$('#price').focus()
		Swal.fire({
			title: '금액를 입력하세요.',
			icon: 'error',
			confirmButtonText: '확인'
		})
		return
	}

	const payMethod = $("#pay-method option:selected").val()
	const accountNo = $("#account-no").val()
	if ( (payMethod == 'bank' && accountNo == undefined) || (payMethod == 'bank' && accountNo == '') ) {
		Swal.fire({
			title: '지급할 계좌를 선택하세요.',
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
	}).then((result) => {
		if (result.isConfirmed) {
			register_offering()
		}
	})
})
// 지급방법 변경 이벤트
$(document).on('change', '#pay-method', function(e) {
	if ($(this).val() == 'bank') {
		$("#account-div").show()
		$("#recipient-div").show()
	} else {
		$("#account-div").hide()
		$("#recipient-div").hide()
	}
})

// 지급등록 AJAX
async function register_offering() {
	const type = $('#detail-type').val()
	const regDate = $('#start-date').val()
	const contents = $('#contents').val()
	const recipient = $("#recipient").val()
	const weekly = $("#weekly option:selected").val()

	const isChecked = $("#price-thousand-check")[0].checked
	let price = $('#price').val()
	if (isChecked) {
		price = price * 1000
	}

	const payMethod = $("#pay-method option:selected").val()
	const accountNo = $("#account-no").val()

	await $.ajax({
		url: '/expense/fixed_register',
		type: "post",
		dataType: "json",
		async: false,
		data: {
			type		: type,
			regDate		: regDate,
			contents	: contents,
			price		: price,
			payMethod	: payMethod,
			accountNo	: accountNo,
			recipient	: recipient,
			weekly		: weekly,
			year		: $("#start-date").val()
		}
	})

	fixed_list(1)
	$('#price').val('')
	$('#contents').val('')
	$("#recipient").val('')
	$("#account-no").val('')
	$("#account-text").html('')
	$("#pay-method option:eq(0)").prop('selected', true)
	$("#account-div").show()
	$("#recipient-div").show()
	$("#price-text").html('')
}

// 대분류 변경시 소분류 처리
$(document).on('change', '#type', function(e) {
	e.stopImmediatePropagation()

	const target = $(this).attr('data-target')

	detailTypeChange($(`#${target}`))
})

// 헌금소분류 처리
async function detailTypeChange(target) {
	const parent = $('#type option:selected').val()

	const typeData = {
		'is-income' : 'N',
		'parent'	: parent,
		'year'		: $("#start-date").val()
	}

	const result = offering_list(typeData);
	result.then((resolve) => {
		if (resolve.status == true) {
			let html = ''
			for(let idx =0; idx < resolve.data.length; idx++) {
				html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
			}
			$(target).html(html)
		}
	})
}

// 헌금분류 가져오기(대분류/소분류 공통함수)
async function offering_list(data) {
	const result = await $.ajax({
		url: '/offering/offering_list',
		type: "post",
		dataType: "json",
		data: data
	})

	return result
}

async function fixed_list(page) {
	const year = $('#start-date').val().toString().substr(0, 4)
	const html = await $.ajax({
		url: '/expense/fixed_list',
		type: "post",
		dataType: "html",
		data: {
			page : page,
			year : year
		}
	})

	await $('#data-list-div').html(html)
}
