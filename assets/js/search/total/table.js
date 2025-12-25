const date = new Date()
const year = date.getFullYear()
const startDate = new Date(year, 0, 1)
// 달력처리
$("#year").datepicker({
	format 		: "yyyy",
	autoclose	: true,
	viewMode: "years",
	minViewMode: "years"
}).datepicker("setDate", startDate)

// 달력변경
$(document).on('change', '#year', async function() {

	/*
	const year = $("#check-date").val()
	$result = await $.ajax({
		url: '/search/carrayover_data',
		type: "post",
		dataType: "json",
		data: {
			year 	: year
		}
	})
	$("#carryover-price-pre").val($result['data']['CARRYOVER_PRE'])
	$("#carryover-price-next").val($result['data']['CARRYOVER_NEXT'])
*/
	await total_list()
	/*
	if($("#total-list-body").find('tr').length > 0) {
		const tr = $("#total-list-body").find('tr')[0]
		const month = $($($(tr).find('td')[0]).find('h6')[0]).html()
		const day = $($($(tr).find('td')[1]).find('h6')[0]).html()
		if (month === '01' && day === '01') {
			$($($(tr).find('td')[0]).find('h6')[0]).html('')
			$($($(tr).find('td')[1]).find('h6')[0]).html('')
		}
		$("#total-list-body").prepend(`<tr><td><h6>01</h6></td><td><h6>01</h6></td><td><h6>전년도이월금</h6></td><td><h6>${$("#carryover-price-pre").val().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}원</h6></td><td></td></tr>`)
	}
	 */
})

async function total_list() {
	const year = $('#year').val()
	const startDate = `${year}-01-01`
	const endDate = `${year}-12-31`

	let html = await $.ajax({
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

$(document).on('click', '#excel-btn', function () {
	const year = $('#year').val()
	const startDate = `${year}-01-01`
	const endDate = `${year}-12-31`

	const url = `/search/total_excel_download?startDate=${startDate}&endDate=${endDate}`
	window.open(url, "다운로드");
})
