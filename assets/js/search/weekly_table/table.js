const date = new Date()
const year = date.getFullYear()
const startDate = new Date(year, 0, 1)
// 달력처리
$("#start-date").datepicker({
	format 		: "yyyy-mm-dd",
	autoclose	: true
}).datepicker("setDate", 'now')
	.datepicker(get_weekly_table_data())
// 달력변경
$(document).on('change', '#start-date', () => {
	get_weekly_table_data()
})

function get_weekly_table_data() {

}

$(document).on('click', '#excel-btn', () => {
	const date = $('#start-date').val()

	const url = `/search/weekly_table_excel_download?date=${date}`
	window.open(url, "다운로드");
})
