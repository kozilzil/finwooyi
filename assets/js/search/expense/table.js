const date = new Date()
const year = date.getFullYear()
const startDate = new Date(year, 0, 1)
const endDate = new Date(year, 11, 31)
// 달력처리
$("#check-date").datepicker({
	format 		: "yyyy",
	autoclose	: true,
	viewMode: "years",
	minViewMode: "years"
}).datepicker("setDate", startDate)
// 달력처리
$("#start-date").datepicker({
	format 		: "yyyy-mm-dd",
	autoclose	: true,
	minDate		: startDate,
	maxDate		: endDate,
	allowInvalid: true
}).datepicker("setDate", startDate)
// 달력처리
$("#end-date").datepicker({
	format 		: "yyyy-mm-dd",
	autoclose	: true,
	minDate		: new Date($("#check-date").val() + "-01-01"),
	maxDate		: new Date($("#check-date").val() + "-12-31")
}).datepicker("setDate", endDate)

// 달력변경
$(document).on('change', '#check-date', async function() {
	const year = $("#check-date").val()
	$("#start-date").val(`${year}-01-01`)
	$("#end-date").val(`${year}-12-31`)
	await typeChange()
})
// 달력변경
$(document).on('change', '#start-date', async function() {
	const year = $("#check-date").val()
	const startDate = $("#start-date").val()
	const endDate = $("#end-date").val()
	const startDateValue = new Date(startDate)
	if ( year.toString() !== startDateValue.getFullYear().toString()) {
		await $("#start-date").datepicker("setDate", `${year}-01-01`)
	}
	if (startDate > endDate) {
		await $("#start-date").datepicker("setDate", `${year}-01-01`)
	}
	await total_list()
})
// 달력변경
$(document).on('change', '#end-date', async function() {
	const year = $("#check-date").val()
	const startDate = $("#start-date").val()
	const endDate = $("#end-date").val()
	const endDateValue = new Date(endDate)
	if ( year != endDateValue.getFullYear()) {
		await $("#end-date").datepicker("setDate", `${year}-12-31`)
	}
	if (startDate > endDate) {
		await $("#end-date").datepicker("setDate", `${year}-12-31`)
	}
	await total_list()
})

async function total_list() {
	const startDate = $('#start-date').val()
	const endDate 	= $('#end-date').val()
	const type 		= $('#detail-type option:selected').val()
	const year  	= $("#check-date").val()

	const html = await $.ajax({
		url: '/search/expense_list',
		type: "post",
		dataType: "html",
		data: {
			startDate 	: startDate,
			endDate		: endDate,
			type		: type,
			year 		: year
		}
	})

	await $('#data-list-div').html(html)
}
$(document).ready(async function (e) {
	await typeChange()
})
async function typeChange() {
	// 헌금대분류 가져오기
	const typeData = {
		'is-income': 'N',
		'parent': 0,
		'year'		: $("#check-date").val()
	}
	const result = offering_list(typeData);
	result.then((resolve) => {
		if (resolve.status == true) {
			let html = ''
			for (let idx = 0; idx < resolve.data.length; idx++) {
				html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
			}
			$("#type").html(html)

			const target = $("#type").attr('data-target')

			detailTypeChange($(`#${target}`))
		} else {
			$("#type").html('')
			$("#detail-type").html('')
			total_list()
		}
	})
}
// 대분류 변경시 소분류 처리
$(document).on('change', '#type', async function(e) {
	e.stopImmediatePropagation()

	const target = $(this).attr('data-target')

	await detailTypeChange($(`#${target}`))
})
$(document).on('change', '#detail-type', async function(e) {
	e.stopImmediatePropagation()

	await total_list()
})
// 헌금소분류 처리
async function detailTypeChange(target) {
	const parent = $('#type option:selected').val()

	const typeData = {
		'is-income' : 'N',
		'parent'	: parent,
		'year'		: $("#check-date").val()
	}

	const result = offering_list(typeData);
	result.then((resolve) => {
		if (resolve.status == true) {
			let html = ''
			for(let idx =0; idx < resolve.data.length; idx++) {
				html += `<option value="${resolve.data[idx]['NO']}">${resolve.data[idx]['TITLE']}</option>`
			}
			$(target).html(html)
			total_list()
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

// 엑셀 다운로드
$(document).on('click', '#excel-btn', function () {
	const startDate = $('#start-date').val()
	const endDate 	= $('#end-date').val()
	const type 		= $('#detail-type option:selected').val()

	const url = `/search/expense_excel_download?startDate=${startDate}&endDate=${endDate}&type=${type}`
	window.open(url, "다운로드");
})
