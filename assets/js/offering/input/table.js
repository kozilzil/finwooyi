$(document).ready(function (e) {
	$('#name').focus()

	// 헌금대분류 가져오기
	const typeData = {
		'is-income' : 'Y',
		'parent'	: 0
	}
	const result = offering_list(typeData);
	result.then(async (resolve) => {
		if (resolve.status == true) {
			let html = ''
			for(let idx =0; idx < resolve.data.length; idx++) {
				html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
			}
			$("#type").html(html)

			const target = $("#type").attr('data-target')

			await detailTypeChange($(`#${target}`))
		}
	})
})

// 달력처리
$("#start-date").datepicker({
	"format" 	: "yyyy-mm-dd",
	"autoclose"	: true
}).datepicker("setDate", 'now')
	.datepicker(income_list(1))
// 달력변경
$(document).on('change', '#start-date', async function() {
	await income_list(1)
})

// 이름입력 엔터이벤트
$(document).on('keyup', '#name', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		const data = $("#name").val()
		if (data.length % 5 == 0) {
			const len = data.length
			if ( (['a', 'A', 'ㅁ'].indexOf(data[0]) == -1) || (['b', 'B', 'ㅠ'].indexOf(data[5]) == -1) ) {
				return
			}
			const nameStrNo = data[1] + data[2] + data[3] + data[4] + ''
			const priceStrNo = data[6] + data[7] + data[8] + data[9] + ''
			let etcStrNo = ''
			const etcStrNoArr = []

			for(let idx=10; idx<len; idx++) {
				if (idx % 5 == 0) {
					if ( ['C', 'c', 'ㅊ'].indexOf(data[idx]) == -1) {
						return
					}
				} else {
					etcStrNo += data[idx]
				}
			}

			for(let idx=0; idx<etcStrNo.length;idx++) {
				if (idx % 4 == 0) {
					etcStrNoArr.push(etcStrNo.substring(idx, idx+4))
				}
			}

			const userResult = user_info(nameStrNo)
			userResult.then((resolve) => {
				if (resolve.status == true) {
					$("#name").val(resolve.data.NAME)
				}
			})

			let etcName = ''
			for(let idx=0;idx<etcStrNoArr.length;idx++) {
				if(etcStrNoArr.length == 1 && etcStrNoArr[idx] == '0000') {
					$("#etc").val('')
				} else {
					const userArrayResult = user_info(etcStrNoArr[idx])
					userArrayResult.then((resolve) => {
						if (resolve.status == true) {
							if (idx != 0) {
								etcName += '/'
							}
							etcName += resolve.data.NAME
							console.log(etcName)
							$("#etc").val(etcName)
						}
					})
				}
			}

			const typeResult = type_info(priceStrNo)
			typeResult.then((resolve) => {
				if (resolve.status == true) {
					$("#type").val(resolve.data.PARENT_NO).prop("selected", true)
					const parent = $('#type option:selected').val()
					const typeData = {
						'is-income' : 'Y',
						'parent'	: parent
					}

					const result = offering_list(typeData);
					result.then((resolve) => {
						if (resolve.status == true) {
							let html = ''
							for(let idx =0; idx < resolve.data.length; idx++) {
								html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
							}
							$("#detail-type").html(html)
						}
					}).then(() => {
						$("#detail-type").val(resolve.data.NO).prop("selected", true)
					})
				}
			})
			$("#price").focus()

			return
		} else {
			$("#price").focus()
			return
		}
	}
})
async function user_info(userNoStr) {
	const result = await $.ajax({
		url: '/management/user_info',
		type: "post",
		dataType: "json",
		data: {
			no : userNoStr
		}
	})

	return result
}
async function type_info(typeNoStr) {
	const result = await $.ajax({
		url: '/offering/offering_type_info',
		type: "post",
		dataType: "json",
		data: {
			no : typeNoStr
		}
	})

	return result
}

// 금액입력 엔터이벤트
$(document).on('keyup', '#price', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		$("#etc").focus()
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
// 추가입력 엔터이벤트
$(document).on('keyup', '#etc', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		$("#register-btn").click()
		return
	}
})
// 헌금등록 버튼 이벤트
$(document).on('click', '#register-btn', function(e) {
	e.stopImmediatePropagation()

	const name = $('#name').val()
	const price = $('#price').val()

	if (name.trim().length == 0) {
		$('#name').focus()
		Swal.fire({
			title: '헌금자를 입력하세요.',
			icon: 'error',
			confirmButtonText: '확인'
		})
		return
	}

	if (price <= 0) {
		$('#price').val(0)
		$('#price').focus()
		Swal.fire({
			title: '정확한 헌금금액을 입력하세요.',
			icon: 'error',
			confirmButtonText: '확인'
		})
		return
	}

	const result = user_list()
	result.then((resolve) => {
		const cnt = resolve.data[0]['TOTAL_CNT']
		let isEqualsName = false
		const dataJson = {}
		$('#name').focus()

		if (cnt == 0) {
			Swal.fire({
				icon: 'error',
				title: '등록되지 않은 이름입니다.',
				confirmButtonColor: '#3085d6',
				confirmButtonText: '확인'
			})

			return
		} else if (cnt == 1) {
			isEqualsName = true
		} else {
			let totalCnt = 10
			if (totalCnt > cnt) {
				totalCnt = cnt
			}

			for(let idx = 0; idx < totalCnt; idx++) {
				dataJson[resolve.data[idx]['NAME']] = resolve.data[idx]['NAME']
				if (name == resolve.data[idx]['NAME']) {
					isEqualsName = true
				}
			}
		}

		if (isEqualsName) {
			Swal.fire({
				title: '등록하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '등록',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					register_offering(name)
				}
			})
		} else {
			Swal.fire({
				title: `${cnt}의 인원이 검색되었습니다.`,
				input: 'select',
				inputPlaceholder: `(최대 10명 표시)`,
				inputOptions: dataJson,
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '등록',
				cancelButtonText: '취소',
				inputValidator: (value) => {
					if (value == '') {
						Swal.fire({
							title: '등록할 성도를 선택하세요.',
							icon: 'error',
							confirmButtonText: '확인'
						})
					} else {
						register_offering(value)
					}
				}
			})
		}
	})
})

// 헌금등록 AJAX
async function register_offering(name) {
	const type = $('#detail-type').val()
	const regDate = $('#start-date').val()
	const etc = $('#etc').val()
	const isOnline = $('#is-online option:selected').val()

	const isChecked = $("#price-thousand-check")[0].checked
	let price = $('#price').val()
	if (isChecked) {
		price = price * 1000
	}

	await $.ajax({
		url: '/offering/offering_register',
		type: "post",
		dataType: "json",
		async: false,
		data: {
			type		: type,
			name		: name,
			regDate		: regDate,
			etc			: etc,
			is_online	: isOnline,
			price		: price
		}
	})

	income_list(1)
	$('#name').val('')
	$('#price').val('')
	$('#etc').val('')
	$("#price-text").html('')
}

// 이름리스트 AJAX
async function user_list() {
	const name = $('#name').val()

	const result = await $.ajax({
		url: '/management/user_list_for_register',
		type: "get",
		dataType: "json",
		data: {
			type	: 'name',
			content	: name,
			page	: 1
		}
	})

	return result
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
		'is-income' : 'Y',
		'parent'	: parent
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

async function income_list(page) {
	const date = $('#start-date').val()

	const html = await $.ajax({
		url: '/offering/income_list',
		type: "post",
		dataType: "html",
		data: {
			date : date,
			page : page
		}
	})

	await $('#data-list-div').html(html)
}
