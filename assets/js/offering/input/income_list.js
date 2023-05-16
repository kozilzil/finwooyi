$(document).ready(() => {
	$("#data-list-pagination li a").each((idx, element) => {
		const page = $(element).attr("data-ci-pagination-page");

		$(element).click(async (e) => {
			e.preventDefault()
			await income_list(page)
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

			let cnt = 0
			$(".cancel-btn").each((idx2, element2) => {
				if (element2.style.display != "none") {
					cnt++
				}
			})
			if (cnt == 0) {
				$("#registrants-table").css('width', '100%')
			}
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
								const page = $(element).attr('data-page')
								const totalCnt = $(element).attr('data-count')

								income_list(1)
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
			const name = $(target.eq(1).html()).val()
			const price = $(target.eq(2).html()).val()

			if (name.trim().length == 0) {
				target.eq(1).find('input').focus()
				Swal.fire({
					title: '헌금자를 입력하세요.',
					icon: 'error',
					confirmButtonText: '확인'
				})
				return
			}

			if (price <= 0) {
				target.eq(2).find('input').val(0)
				target.eq(2).find('input').focus()
				Swal.fire({
					title: '정확한 헌금금액을 입력하세요.',
					icon: 'error',
					confirmButtonText: '확인'
				})
				return
			}

			const result = (async () => {
				return await $.ajax({
					url: '/management/user_list',
					type: "get",
					dataType: "json",
					data: {
						type	: 'name',
						content	: name,
						page	: 1
					}
				})
			})()
			result.then((resolve) => {
				const cnt = resolve.data[0]['TOTAL_CNT']
				let isEqualsName = false
				const dataJson = {}

				if (cnt == 0) {
					Swal.fire({
						icon: 'error',
						title: '등록되지 않은 이름입니다.',
						confirmButtonColor: '#3085d6',
						confirmButtonText: '확인'
					})

					return
				} else if (cnt == 1) {
					isEqualsName = true
				} else {
					let totalCnt = 10
					if (totalCnt > cnt) {
						totalCnt = cnt
					}

					for(let idx = 0; idx < totalCnt; idx++) {
						dataJson[resolve.data[idx]['NAME']] = resolve.data[idx]['NAME']
						if (name == resolve.data[idx]['NAME']) {
							isEqualsName = true
						}
					}
				}

				if (isEqualsName) {
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
				} else {
					Swal.fire({
						title: `${cnt}의 인원이 검색되었습니다.`,
						input: 'select',
						inputPlaceholder: `(최대 10명 표시)`,
						inputOptions: dataJson,
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: '적용',
						cancelButtonText: '취소',
						inputValidator: (value) => {
							if (value == '') {
								Swal.fire({
									title: '등록할 성도를 선택하세요.',
									icon: 'error',
									confirmButtonText: '확인'
								})
							} else {
								target.eq(1).find('input').attr('value', value)
								modifyCompleteBtnClickEvent(element)
							}
						}
					})
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
		const userName = siblingsList.eq(1).html()
		siblingsList.eq(1).html(`<input type="text" class="form-control" placeholder="${userName}" value="${userName}" data-before=${userName}>`)
		const userNameData = siblingsList.eq(1).find('input')
		$(userNameData).keyup(() => {
			siblingsList.eq(1).find('input').attr('value', $(userNameData).val())
		})

		const price = siblingsList.eq(2).html().replaceAll(',', '')
		siblingsList.eq(2).html(`<input type="text" class="form-control text-" placeholder="금액"
								   	style="text-align: right"
								   	oninput="this.value = this.value.replace(/[^0-9.]/g, '');"
								   	maxlength="15"
									value="${price}"
									data-before=${price}
							>`)
		const priceData = siblingsList.eq(2).find('input')
		$(priceData).keyup(() => {
			siblingsList.eq(2).find('input').attr('value', $(priceData).val())
		})

		// 헌금대분류 가져오기
		const typeData = {
			'is-income' : 'Y',
			'parent'	: 0
		}
		const offeringParentResult = await offering_list(typeData);
		if (offeringParentResult.status == true) {
			const offeringParentName = siblingsList.eq(3).html()
			let html = `<select name="type" class="form-control offering-type-parent" target="offering-type" data-before=${offeringParentName}>`
			for(let idx =0; idx < offeringParentResult.data.length; idx++) {
				html += `<option value="${offeringParentResult.data[idx]['NO']}" ${offeringParentName == offeringParentResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringParentResult.data[idx]['TITLE']}</option>`
			}
			html += `</select>`
			siblingsList.eq(3).html(html)
		}
		const offeringParent = siblingsList.eq(3).find('select')
		$(offeringParent).change(async () => {
			$('option:selected', offeringParent[0]).attr('selected',true).siblings().removeAttr('selected');

			// 헌금소분류 가져오기
			const detailTypeData = {
				'is-income' : 'Y',
				'parent'	: offeringParent[0].value
			}
			const offeringResult = await offering_list(detailTypeData);

			if (offeringResult.status == true) {
				const offeringName = $(siblingsList.eq(4).html()).attr('data-before')
				siblingsList.eq(4).html('')
				let html = `<select name="type" class="form-control offering-type" data-before=${offeringName}>`
				for(let idx =0; idx < offeringResult.data.length; idx++) {
					html += `<option value="${offeringResult.data[idx]['NO']}" ${offeringName == offeringResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringResult.data[idx]['TITLE']}</option>`
				}
				html += '</select>'
				siblingsList.eq(4).html(html)
			}


			const offering = siblingsList.eq(4).find('select')
			$(offering).change(async () => {
				$('option:selected', offering[0]).attr('selected',true).siblings().removeAttr('selected');
			})
		})

		const selectObj = siblingsList.eq(3).html()
		// 헌금소분류 가져오기
		const detailTypeData = {
			'is-income' : 'Y',
			'parent'	: $(selectObj).val()
		}
		const offeringResult = await offering_list(detailTypeData);
		if (offeringResult.status == true) {
			const offeringName = siblingsList.eq(4).html()
			let html = `<select name="type" class="form-control offering-type" data-before=${offeringName}>`
			for(let idx =0; idx < offeringResult.data.length; idx++) {
				html += `<option value="${offeringResult.data[idx]['NO']}" ${offeringName == offeringResult.data[idx]['TITLE'] ? "selected" : ''}>${offeringResult.data[idx]['TITLE']}</option>`
			}
			html += '</select>'
			siblingsList.eq(4).html(html)
		}
		const offering = siblingsList.eq(4).find('select')
		$(offering).change(async () => {
			$('option:selected', offering[0]).attr('selected',true).siblings().removeAttr('selected');
		})

		const etc = siblingsList.eq(5).html()
		siblingsList.eq(5).html(`<input type="text" class="form-control" placeholder="추가입력" value="${etc}" data-before="${etc}">`)
		const etcData = siblingsList.eq(5).find('input')
		$(etcData).keyup(() => {
			siblingsList.eq(5).find('input').attr('value', $(etcData).val())
		})

		const isOnline = siblingsList.eq(6).html()
		const isOnlineHtml = `<select name="is-online" class="form-control" data-before="${isOnline}"><option value="N" ${isOnline == '오프라인' ? 'selected' : ''}>오프라인</option><option value="Y" ${isOnline == '온라인' ? 'selected' : ''}>온라인</option></select>`
		siblingsList.eq(6).html(isOnlineHtml)
		const isOnlineSelect = siblingsList.eq(6).find('select')
		$(isOnlineSelect).change(() => {
			$('option:selected', isOnlineSelect[0]).attr('selected',true).siblings().removeAttr('selected');
		})
	}

	/**
	 * 취소버튼 이벤트
	 * @param target
	 * @returns {Promise<void>}
	 */
	async function cancelBtnAddClickEvent(target) {
		const siblingsList = $(target).siblings().children()
		const userName = $(siblingsList.eq(1).html()).attr('data-before')
		siblingsList.eq(1).html(`${userName}`)

		let price = $(siblingsList.eq(2).html()).attr('data-before')
		price = price.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,')
		siblingsList.eq(2).html(`${price}`)

		const offeringTypeParent = $(siblingsList.eq(3).html()).attr('data-before')
		siblingsList.eq(3).html(`${offeringTypeParent}`)

		const offeringType = $(siblingsList.eq(4).html()).attr('data-before')
		siblingsList.eq(4).html(`${offeringType}`)

		const etc = $(siblingsList.eq(5).html()).attr('data-before')
		siblingsList.eq(5).html(`${etc}`)

		const isOnline = $(siblingsList.eq(6).html()).attr('data-before')
		siblingsList.eq(6).html(`${isOnline}`)
	}

	/**
	 * 삭제 이벤트
	 * @param target
	 * @returns {Promise<void>}
	 */
	async function deleteBtnAddClickEvent(no) {
		const result = await $.ajax({
			url: '/offering/offering_delete',
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
		const name = $(target.eq(1).html()).val()
		const price = $(target.eq(2).html()).val()
		const offeringTypeNo = $(target.eq(4).html()).val()
		const etc = $(target.eq(5).html()).val()
		const isOnline = $(target.eq(6).html()).val()
		const page = $(element).attr('data-page')

		$.ajax({
			url: '/offering/offering_update',
			type: "post",
			dataType: "json",
			async: false,
			data: {
				no 				: no,
				name 			: name,
				price			: price,
				offeringTypeNo	: offeringTypeNo,
				etc				: etc,
				isOnline		: isOnline
			}
		}).then((result) => {
			if (result.status == true) {
				Swal.fire({
					title: '처리되었습니다.',
					icon: 'success',
					confirmButtonText: '확인'
				}).then(() => {
					income_list(page)
				})
			}
		})
	}
})
