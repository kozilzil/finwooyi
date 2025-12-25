'use strict'

const date = new Date()
const year = date.getFullYear()
const startDate = new Date(year, 0, 1)
const endDate = new Date(year, 11, 31)
// 달력처리
$("#year").datepicker({
	format 		: "yyyy",
	autoclose	: true,
	viewMode: "years",
	minViewMode: "years"
}).datepicker("setDate", startDate)

$(document).on("change", "#year", async function() {
	await offeringSetting()
})

$(document).ready(async function (e) {
	await offeringSetting()
})


async function offeringSetting() {
	// 헌금대분류 가져오기
	const typeData = {
		'is-income' : 'Y',
		'parent'	: 0,
		'year'		: $("#year").val()
	}
	const result = await offering_list(typeData)
	if (result.status == true) {
		let html = ''
		for(let idx =0; idx < result.data.length; idx++) {
			html += `<option value="${result.data[idx]['NO']}">${result.data[idx]['TITLE']}</option>`
		}
		$("#type").html(html)

		const target = $("#type").attr('data-target')

		await detailTypeChange($(`#${target}`))
	} else {
		$("#type").html('')
		$("#detail-type").html('')
	}
}

// 대분류 변경시 소분류 처리
$(document).on('change', '#type', async function(e) {
	e.stopImmediatePropagation()

	const target = $(this).attr('data-target')

	await detailTypeChange($(`#${target}`))
})

// 헌금소분류 처리
async function detailTypeChange(target) {
	const parent = $('#type option:selected').val()

	const typeData = {
		'is-income' : 'Y',
		'parent'	: parent,
		'year'		: $("#year").val()
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

$(document).on("click", "#register-btn", async function() {
	let registerNameNo = ""
	let registerEtcNo = ""
	const name = $("#name").val()
	const detailType = $("#detail-type option:selected").val()
	const etc = $("#etc").val()

	if (detailType == undefined) {
		alert('헌금상세분류를 선택하세요.')
		return false
	}

	if (name == "") {
		alert('이름을 입력하세요.')
		return false
	}

	// 이름
	await user_list(name).then((resolve) => {
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
			registerNameNo = resolve.data[0]['NO']
		} else {
			let totalCnt = 10
			if (totalCnt > cnt) {
				totalCnt = cnt
			}

			for(let idx = 0; idx < totalCnt; idx++) {
				dataJson[resolve.data[idx]['NO']] = resolve.data[idx]['NAME']
				if (name == resolve.data[idx]['NAME']) {
					isEqualsName = true
					registerNameNo = resolve.data[idx]['NO']
				}
			}
		}

		if (!isEqualsName) {
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
						registerNameNo = value
					}
				}
			})
		}
	})

	// ETC
	if (etc != "") {
		await user_list(etc).then((resolve) => {
			const cnt = resolve.data[0]['TOTAL_CNT']
			let isEqualsName = false
			const dataJson = {}

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
				registerEtcNo = resolve.data[0]['NO']
			} else {
				let totalCnt = 10
				if (totalCnt > cnt) {
					totalCnt = cnt
				}

				for(let idx = 0; idx < totalCnt; idx++) {
					dataJson[resolve.data[idx]['NO']] = resolve.data[idx]['NAME']
					if (name == resolve.data[idx]['NAME']) {
						isEqualsName = true
						registerEtcNo = resolve.data[idx]['NO']
					}
				}
			}

			if (!isEqualsName) {
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
							registerEtcNo = value
						}
					}
				})
			}
		})
	}

	let code = ""
	code += "A"
	for(let idx =0; idx < 4-registerNameNo.length; idx++) {
		code += "0"
	}
	code += registerNameNo

	code += "B"
	for(let idx =0; idx < 4-detailType.length; idx++) {
		code += "0"
	}
	code += detailType

	code += "C"
	for(let idx =0; idx < 4-registerEtcNo.length; idx++) {
		code += "0"
	}
	code += registerEtcNo

	$("#data-type").html($("#detail-type option:selected").html())
	$("#data-name").html(name)
	$("#data-etc").html(etc)
	$("#data-code").html(code)
	//
	// Swal.fire({
	// 	title: '등록하시겠습니까?',
	// 	showCancelButton: true,
	// 	confirmButtonColor: '#3085d6',
	// 	cancelButtonColor: '#d33',
	// 	confirmButtonText: '등록',
	// 	cancelButtonText: '취소'
	// }).then((result) => {
	// 	if (result.isConfirmed) {
	//
	// 	}
	// })
})

// 이름리스트 AJAX
async function user_list(name) {
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
