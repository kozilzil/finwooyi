'use strict'
// 달력처리
$("#date").datepicker({
	"format" 	: "yyyy-mm-dd",
	"autoclose"	: true
}).datepicker("setDate", 'now')
	.datepicker(coefficient())
// 달력변경
$(document).on('change', '#date', async function() {
	await coefficient()
})


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
		let idxCnt = 0
		$(".price").each(function() {
			idxCnt++
		})
		if (idx < idxCnt) {
			$(`#input-${(parseInt(idx)+1)}`).focus()
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
	counterTotalCalc()
}
$(document).on("keyup", ".text-price", function(e) {
	if(e.which == 17) {
		isCtrl = false
	}
})
$(document).on("keydown", ".text-price", function(e) {
	if(e.which == 17) {
		isCtrl = true
	}
	if (e.keyCode == 67 && isCtrl) {
		$(this).val('')
	}
	if (e.keyCode == 13) {
		textCalc()
		const idx = $(this).attr('data-idx')
		let idxCnt = 0
		$(".text-price").each(function() {
			idxCnt++
		})
		if (idx < idxCnt) {
			$(`#text-input-${(parseInt(idx)+1)}`).focus()
		}
	}
})
$(document).on("blur", ".text-price", function() {
	textCalc()
})
function textCalc() {
	let price = parseInt("0")
	$(".text-price").each(function() {
		const addPrice = $(this).val()
		if (addPrice != "") {
			price += parseInt(addPrice)
		}
	})
	$("#text-sum-total").val((price + "").replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'))
	counterTotalCalc()
}
function counterTotalCalc() {
	const sumTotal = $("#sum-total").val()
	const textSum = $("#text-sum-total").val()
	const price = parseInt(sumTotal.replaceAll(",", "")) + parseInt(textSum.replaceAll(",", ""))

	$("#counter-total").html((price + "").replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'))

	const counterTotal = price
	const computationTotal = $("#computation-total").html().replaceAll(",", "")
	if (parseInt(counterTotal) - parseInt(computationTotal) < 0) {
		$("#calc-total").css("color", "red")
	} else {
		$("#calc-total").css("color", "")
	}
	const totalPrice = parseInt(counterTotal) - parseInt(computationTotal)
	$("#calc-total").html((totalPrice + "").replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'))
}

async function coefficient() {
	const date = $("#date").val()
	const result = await $.ajax({
		url: '/calc/coefficient',
		type: "post",
		dataType: "json",
		data: {
			date : date
		}
	})
	$("#computation-total").html(result.price)
	counterTotalCalc()
}
