$(document).ready(() => {
	$("#data-list-pagination li a").each((idx, element) => {
		const page = $(element).attr("data-ci-pagination-page");

		$(element).click(async (e) => {
			e.preventDefault()
			await fixed_popup_list(page)
		})
	})
})

async function fixed_popup_list(page) {
	const contents = $("#account-modal-contents").val()

	const html = await $.ajax({
		url: '/expense/fixed_popup_list',
		type: "post",
		dataType: "html",
		data: {
			page : page,
			contents : contents
		}
	})

	await $('#fixed-modal-data-list-div').html(html)
}
