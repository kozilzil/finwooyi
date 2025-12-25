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

$(document).on("click", "#excel-btn", async function() {
	const year = $('#year').val()
	const type = $('#detail-type option:selected').val()

	const url = `/barcode/auto_excel_download?year=${year}&type=${type}`
	window.open(url, "다운로드");
})
