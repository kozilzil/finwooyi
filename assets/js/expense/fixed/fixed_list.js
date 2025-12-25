$(document).ready(() => {
	$("#data-list-pagination li a").each((idx, element) => {
		const page = $(element).attr("data-ci-pagination-page");

		$(element).click((e) => {
			e.preventDefault()
			fixed_list(page)
		})
	})

	$(".modify-btn").each((idx, element) => {
		$(element).click(async () => {
			const target = $(element).parent()
			await modifyBtnAddClickEvent(target)
			$(element).parent().find('.modify-btn').hide()
			$(element).parent().find('.delete-btn').hide()
			$(element).parent().find('.modify-complete-btn').show()
			$(element).parent().find('.cancel-btn').show()
		})
	})
	$(".cancel-btn").each((idx, element) => {
		$(element).hide()

		$(element).click(async () => {
			const target = $(element).parent()
			await cancelBtnAddClickEvent(target)
			$(element).parent().find('.modify-btn').show()
			$(element).parent().find('.delete-btn').show()
			$(element).parent().find('.modify-complete-btn').hide()
			$(element).parent().find('.cancel-btn').hide()
		})
	})
	$(".delete-btn").each((idx, element) => {
		$(element).click(() => {
			Swal.fire({
				title: '삭제하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: '삭제',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					const no = $(element).attr('data-value')
					const result = deleteBtnAddClickEvent(no)
					result.then((resolve) => {
						if ( resolve.status ) {
							Swal.fire({
								title: '삭제되었습니다.',
								icon: 'success',
								confirmButtonText: '확인'
							}).then(() => {
								fixed_list(1)
							})
						}
					})
				}
			})
		})
	})
	$(".modify-complete-btn").each((idx, element) => {
		$(element).hide()

		$(element).click(() => {
			const target = $($(element).parent()).siblings().children()

			const contents = $(target.eq(4).html()).val()
			const recipient = $(target.eq(5).html()).val()

			if (contents.trim().length == 0) {
				$(target.eq(4).html()).focus()
				Swal.fire({
					title: '상세내용을 입력하세요.',
					icon: 'error',
					confirmButtonText: '확인'
				})
				return
			}

			if (recipient.trim().length == 0) {
				$(target.eq(5).html()).focus()
				Swal.fire({
					title: '받는분 통장내용을 입력하세요.',
					icon: 'error',
					confirmButtonText: '확인'
				})
				return
			}

			Swal.fire({
				title: '적용하시겠습니까?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '적용',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					modifyCompleteBtnClickEvent(element)
				}
			})
		})
	})

	/**
	 * 수정버튼 이벤트
	 * @param target
	 * @returns {Promise<void>}
	 */
	async function modifyBtnAddClickEvent(target) {
		const siblingsList = $(target).siblings().children()
		// 주차
		const weeklyName = siblingsList.eq(1).html()
		let html = `<select name="type" class="form-control" data-before=${weeklyName}>`
		for(let idx =0; idx < 5; idx++) {
			html += `<option value="${idx + 1}" ${weeklyName.toString() === (idx+1)+'주차' ? "selected" : ''}>${idx+1}주차</option>`
		}
		html += `</select>`
		siblingsList.eq(1).html(html)

		// 헌금대분류 가져오기
		const typeData = {
			'is-income' : 'N',
			'parent'	: 0,
			'year'		: $("#start-date").val()
		}
		const offeringParentResult = await offering_list(typeData);
		if (offeringParentResult.status == true) {
			const offeringParentName = siblingsList.eq(2).html()
			let html = `<select name="type" class="form-control offering-type-parent" target="offering-type" data-before=${offeringParentName}>`
			for(let idx =0; idx < offeringParentResult.data.length; idx++) {
				html += `<option value="${offeringParentResult.data[idx]['NO']}" ${offeringParentName == offeringParentResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringParentResult.data[idx]['TITLE']}</option>`
			}
			html += `</select>`
			siblingsList.eq(2).html(html)
		}
		const offeringParent = siblingsList.eq(2).find('select')
		$(offeringParent).change(async () => {
			$('option:selected', offeringParent[0]).attr('selected',true).siblings().removeAttr('selected');

			// 헌금소분류 가져오기
			const detailTypeData = {
				'is-income' : 'N',
				'parent'	: offeringParent[0].value,
				'year'		: $("#start-date").val()
			}
			const offeringResult = await offering_list(detailTypeData);

			if (offeringResult.status == true) {
				const offeringName = $(siblingsList.eq(3).html()).attr('data-before')
				siblingsList.eq(3).html('')
				let html = `<select name="type" class="form-control offering-type" data-before=${offeringName}>`
				for(let idx =0; idx < offeringResult.data.length; idx++) {
					html += `<option value="${offeringResult.data[idx]['NO']}" ${offeringName == offeringResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringResult.data[idx]['TITLE']}</option>`
				}
				html += '</select>'
				siblingsList.eq(3).html(html)
			}


			const offering = siblingsList.eq(3).find('select')
			$(offering).change(async () => {
				$('option:selected', offering[0]).attr('selected',true).siblings().removeAttr('selected');
			})
		})

		const selectObj = siblingsList.eq(2).html()
		// 헌금소분류 가져오기
		const detailTypeData = {
			'is-income' : 'N',
			'parent'	: $(selectObj).val(),
			'year'		: $("#start-date").val()
		}
		const offeringResult = await offering_list(detailTypeData);
		if (offeringResult.status == true) {
			const offeringName = siblingsList.eq(3).html()
			let html = `<select name="type" class="form-control offering-type" data-before=${offeringName}>`
			for(let idx =0; idx < offeringResult.data.length; idx++) {
				html += `<option value="${offeringResult.data[idx]['NO']}" ${offeringName == offeringResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringResult.data[idx]['TITLE']}</option>`
			}
			html += '</select>'
			siblingsList.eq(3).html(html)
		}
		const offering = siblingsList.eq(3).find('select')
		$(offering).change(async () => {
			$('option:selected', offering[0]).attr('selected',true).siblings().removeAttr('selected');
		})

		// 금액
		const price = siblingsList.eq(4).html().replaceAll(',', '')
		siblingsList.eq(4).html(`<input type="text" class="form-control text-" placeholder="금액"
								   	style="text-align: right"
								   	oninput="this.value = this.value.replace(/[^0-9.]/g, '');"
								   	maxlength="15"
									value="${price}"
									data-before=${price}
							>`)
		const priceData = siblingsList.eq(4).find('input')
		$(priceData).keyup(() => {
			siblingsList.eq(4).find('input').attr('value', $(priceData).val())
		})

		// 상세내용
		const contents = siblingsList.eq(5).html()
		siblingsList.eq(5).html(`<input type="text" class="form-control" placeholder="${contents}" value="${contents}" data-before=${contents}>`)
		const contentsData = siblingsList.eq(5).find('input')
		$(contentsData).keyup(() => {
			siblingsList.eq(5).find('input').attr('value', $(contentsData).val())
		})

		// 계좌
		const recipient = siblingsList.eq(6).html()
		siblingsList.eq(6).html(`<input type="text" class="form-control" placeholder="${recipient}" value="${recipient}" data-before=${recipient}>`)
		const recipientData = siblingsList.eq(6).find('input')
		$(recipientData).keyup(() => {
			siblingsList.eq(6).find('input').attr('value', $(recipientData).val())
		})
	}

	/**
	 * 취소버튼 이벤트
	 * @param target
	 * @returns {Promise<void>}
	 */
	async function cancelBtnAddClickEvent(target) {
		const siblingsList = $(target).siblings().children()
		const weekly = $(siblingsList.eq(1).html()).attr('data-before')
		siblingsList.eq(1).html(`${weekly}`)

		const offeringTypeParent = $(siblingsList.eq(2).html()).attr('data-before')
		siblingsList.eq(2).html(`${offeringTypeParent}`)

		const offeringType = $(siblingsList.eq(3).html()).attr('data-before')
		siblingsList.eq(3).html(`${offeringType}`)

		let price = $(siblingsList.eq(4).html()).attr('data-before')
		price = price.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,')
		siblingsList.eq(4).html(`${price}`)

		const contents = $(siblingsList.eq(5).html()).attr('data-before')
		siblingsList.eq(5).html(`${contents}`)

		const recipient = $(siblingsList.eq(6).html()).attr('data-before')
		siblingsList.eq(6).html(`${recipient}`)
	}

	/**
	 * 삭제 이벤트
	 * @param target
	 * @returns {Promise<void>}
	 */
	async function deleteBtnAddClickEvent(no) {
		const result = await $.ajax({
			url: '/expense/fixed_delete',
			type: "post",
			dataType: "json",
			async: false,
			data: {
				no : no
			}
		})

		return result
	}

	/**
	 * 수정완료 이벤트
	 * @param element
	 * @returns {Promise<void>}
	 */
	function modifyCompleteBtnClickEvent(element) {
		const no = $(element).attr('data-value')
		const target = $($(element).parent()).siblings().children()
		const weekly = $(target.eq(1).html()).val()
		const offeringTypeNo = $(target.eq(3).html()).val()
		const price = $(target.eq(4).html()).val()
		const contents = $(target.eq(5).html()).val()
		const recipient = $(target.eq(6).html()).val()
		const page = $(element).attr('data-page')

		$.ajax({
			url: '/expense/fixed_update',
			type: "post",
			dataType: "json",
			async: false,
			data: {
				no 				: no,
				weekly			: weekly,
				price			: price,
				offeringTypeNo	: offeringTypeNo,
				contents		: contents,
				recipient		: recipient
			}
		}).then((result) => {
			if (result.status == true) {
				Swal.fire({
					title: '처리되었습니다.',
					icon: 'success',
					confirmButtonText: '확인'
				}).then(() => {
					fixed_list(page)
				})
			}
		})
	}
})

