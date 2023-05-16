const date = new Date()
const year = date.getFullYear()
const startDate = new Date(year, 0, 1)
const endDate = new Date(year, 11, 31)
// 달력처리
$("#start-date").datepicker({
	format 		: "yyyy-mm-dd",
	autoclose	: true
}).datepicker("setDate", startDate)
// 달력처리
$("#end-date").datepicker({
	format 		: "yyyy-mm-dd",
	autoclose	: true
}).datepicker("setDate", endDate)

// 달력변경
$(document).on('change', '.date', async function() {
	await total_list()
})

async function total_list() {
	const startDate = $('#start-date').val()
	const endDate = $('#end-date').val()

	const html = await $.ajax({
		url: '/search/total_list',
		type: "post",
		dataType: "html",
		data: {
			startDate 	: startDate,
			endDate		: endDate
		}
	})

	await $('#data-list-div').html(html)
}

$(document).ready(async () => {
	await total_list()
})

$(document).on('click', '#excel-btn', function () {
	const startDate = $('#start-date').val()
	const endDate = $('#end-date').val()

	const url = `/search/total_excel_download?startDate=${startDate}&endDate=${endDate}`
	window.open(url, "다운로드");
})
