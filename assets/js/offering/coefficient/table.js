// 달력처리
$("#start-date").datepicker({
	"format" 	: "yyyy-mm-dd",
	"autoclose"	: true
}).datepicker("setDate", 'now')
	.datepicker(coefficient_list())
// 달력변경
$(document).on('change', '#start-date', function() {
	coefficient_list()
})

async function coefficient_list() {
	const date = $('#start-date').val()

	const html = await $.ajax({
		url: '/offering/coefficient_list',
		type: "post",
		dataType: "html",
		data: {
			date : date
		}
	})

	await $('#data-list-div').html(html)
}
