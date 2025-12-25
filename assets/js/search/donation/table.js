
// 달력처리
$("#date").datepicker({
	"format" 	: "yyyy",
	"autoclose"	: true,
	viewMode: "years",
	minViewMode: "years"
}).datepicker("setDate", 'now')

$(document).on('click', '.donation-list-btn', function() {
	const userNo = $(this).val()
	const year = $("#date").val()
	location.href = `/search/donation_detail/${userNo}/${year}`
})
