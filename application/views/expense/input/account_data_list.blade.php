<div id="data-list-div">
	<h6 class="card-title">검색 결과 <span id="list-count" style="color: red">{{ array_key_exists('list', $data) ? (count($data['list']) > 0 ? number_format($data['list'][0]['TOTAL_CNT']) : 0) : '' }}</span></h6>
	<div class="table-responsive pt-3">
		<table class="table table-bordered">
			<thead>
			<tr class="text-center">
				<th>
					선택
				</th>
				<th>
					계좌별명
				</th>
				<th>
					은행
				</th>
				<th>
					예금주
				</th>
				<th>
					계좌번호
				</th>
			</tr>
			</thead>
			<tbody>
			@if($data['list'][0]['TOTAL_CNT'] == 0)
				<tr class="text-center">
					<td colspan="8">
						<p>데이터가 없습니다.</p>
					</td>
				</tr>
			@else
				@for($idx=0;$idx<count($data['list']);$idx++)
					<tr class="text-center">
						<td>
							<input type="radio" class="form-check-input" name="account-modal-radios"
								   value="{{ $data['list'][$idx]['NO'] }}"
								   data-nickname="{{ $data['list'][$idx]['NICK_NAME'] }}"
								   data-name="{{ $data['list'][$idx]['NAME'] }}"
								   data-holder="{{ $data['list'][$idx]['HOLDER'] }}"
								   data-account="{{ $data['list'][$idx]['ACCOUNT'] }}"
							>
							<i class="input-helper"></i></label>
						</td>
						<td>
							{{ $data['list'][$idx]['NICK_NAME'] }}
						</td>
						<td>
							{{ $data['list'][$idx]['NAME'] }}
						</td>
						<td>
							{{ $data['list'][$idx]['HOLDER'] }}
						</td>
						<td>
							{{ $data['list'][$idx]['ACCOUNT'] }}
						</td>
					</tr>
				@endfor
			@endif
			</tbody>
		</table>


		@if(array_key_exists('pagination', $data))
			@if(array_key_exists('paging', $data['pagination']))
				<div>
					{{ $data['pagination']['paging'] }}
				</div>
			@endif
		@endif
	</div>
</div>
<script>
	$("#account-modal-list-pagenation li").click(async function() {
		const page = $(this).find('a').attr('data-ci-pagination-page')

		if (page !== undefined) {
			const result = await $.ajax({
				url: '/expense/account',
				type: "post",
				dataType: "html",
				data: {
					page : page
				}
			})
			$("#account-modal-data-list-div").html(result)
		}
	})
</script>
