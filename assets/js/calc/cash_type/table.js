'use strict'
let isCtrl = false
$(document).on("keyup", ".price", function(e) {
	if(e.which == 17) {
		isCtrl = false
	}
})
$(document).on("keydown", ".price", function(e) {
	if(e.which == 17) {
		isCtrl = true
	}
	if (e.keyCode == 67 && isCtrl) {
		$(this).val('')
	}
	if (e.keyCode == 13) {
		calc($(this))
		const idx = $(this).attr('data-idx')
		if (idx > 0) {
			$(`#input-${idx-1}`).focus()
		}
	}
})
$(document).on("blur", ".price", function() {
	calc($(this))
})
function calc(_this) {
	const idx = $(_this).attr('data-idx')
	const price = $(_this).attr('data-price')
	const value = $(_this).val()
	const target = $("#sum-"+idx)

	$(target).val((price * value + '').replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'))
	$(target).attr('data-value', price * value)

	totalCalc()
}
function totalCalc() {
	let price = 0
	$(".sum").each(function() {
		const value = $(this).attr('data-value')
		if (value != undefined && value != '') {
			price += parseInt(value)
		}
	})
	$("#sum-total").val((price + "").replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'))
}

$(document).on("click", "#clear-btn", function() {
	$(".price").each(function() {
		$(this).val('')
		calc($(this))
	})
	totalCalc()
})
