$(document).ready(() => {
	$("#input").focus()
})
// 이름입력 엔터이벤트
$(document).on('keyup', '#input', function(e) {
	e.stopImmediatePropagation()

	if (e.keyCode == 13) {
		const data = $("#input").val()
		if (data.length % 5 == 0) {
			const len = data.length
			if (data[0] != 'A' || data[5] != 'B') {
				return
			}

			$("#input").focus()
			$("#input").val('')
			const nameStrNo = data[1] + data[2] + data[3] + data[4] + ''
			const priceStrNo = data[6] + data[7] + data[8] + data[9] + ''
			let etcStrNo = ''
			const etcStrNoArr = []

			const typeResult = type_info(priceStrNo)
			typeResult.then((resolve) => {
				if (resolve.status == true) {
					$("#detail-type").val(resolve.data.TITLE)
				}
			})

			for(let idx=10; idx<len; idx++) {
				if (idx % 5 == 0) {
					if (data[idx] != 'C') {
						return
					}
				} else {
					etcStrNo += data[idx]
				}
			}

			for(let idx=0; idx<etcStrNo.length;idx++) {
				if (idx % 4 == 0) {
					etcStrNoArr.push(etcStrNo.substring(idx, idx+4))
				}
			}

			const userResult = user_info(nameStrNo)
			userResult.then((resolve) => {
				if (resolve.status == true) {
					$("#name").val(resolve.data.NAME)
				}
			})

			let etcName = ''
			for(let idx=0;idx<etcStrNoArr.length;idx++) {
				if(etcStrNoArr.length == 1 && etcStrNoArr[idx] == '0000') {
					$("#etc").val('')
				} else {
					const userResult = user_info(etcStrNoArr[idx])
					userResult.then((resolve) => {
						if (resolve.status == true) {
							if (idx != 0) {
								etcName += '/'
							}
							etcName += resolve.data.NAME
							$("#etc").val(etcName)
						}
					})
				}
			}
		}
	}
})
async function user_info(userNoStr) {
	const result = await $.ajax({
		url: '/management/user_info',
		type: "post",
		dataType: "json",
		data: {
			no : userNoStr
		}
	})

	return result
}
async function type_info(typeNoStr) {
	const result = await $.ajax({
		url: '/offering/offering_type_info',
		type: "post",
		dataType: "json",
		data: {
			no : typeNoStr
		}
	})

	return result
}
