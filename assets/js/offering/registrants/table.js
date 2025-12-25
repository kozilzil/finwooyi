// 달력처리
$("#start-date").datepicker({
	"format" 	: "yyyy-mm-dd",
	"autoclose"	: true
}).datepicker("setDate", 'now')
	.datepicker(coefficient_list())
// 달력변경
$(document).on('change', '#start-date', () => {
	coefficient_list()
})

async function coefficient_list() {
	const date = $('#start-date').val()

	const html = await $.ajax({
		url: '/offering/registrants_list',
		type: "post",
		dataType: "html",
		data: {
			date : date
		}
	})

	await $('#data-list-div').html(html)
}

$(document).on('click', '#excel-down-btn', () => {
	const date = $('#start-date').val()

	const url = `/offering/registrants_excel_download?date=${date}`
	window.open(url, "다운로드");
	// , "scrollbars=no,width=100,height=100,menubar=false"
})
