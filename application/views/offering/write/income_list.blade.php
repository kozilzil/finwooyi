<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th colspan="8" class="text-dark">
							<h5>합계 : {{ number_format($data['registrants'][0]['TOTAL_PRICE']) }}</h5>
						</th>
					</tr>
					<tr>
						<th width="5%">NO</th>
						<th width="15%">헌금자</th>
						<th width="15%">금액</th>
						<th width="15%">헌금타입(대분류)</th>
						<th width="15%">헌금타입(상세)</th>
						<th width="15%">추가입력</th>
						<th width="15%">온라인/오프라인</th>
						<th width="*">처리</th>
					</tr>
					</thead>
					<tbody>
					@if ($data['registrants'][0]['TOTAL_CNT'] > 0)
						@for($idx = 0; $idx < count($data['registrants']); $idx++)
							<tr>
								<td>
									<div class="form-check-flat mt-0">
										<a href="/management/user_write/{{ $data['registrants'][$idx]['NO'] }}">{{ $data['registrants'][0]['TOTAL_CNT'] - $idx - (($data['page'] -1) * $data['limit']) }}</a>
									</div>
								</td>
								<td class="user-name">
									<h6>{{ $data['registrants'][$idx]['USER_NAME'] }}</h6>
								</td>
								<td class="price">
									<h6>{{ number_format($data['registrants'][$idx]['PRICE'], 0) }}</h6>
								</td>
								<td class="offering-type-parent-name">
									<h6>{{ $data['registrants'][$idx]['OFFERING_TYPE_PARENT_NAME'] }}</h6>
								</td>
								<td class="offering-type-name">
									<h6>{{ $data['registrants'][$idx]['OFFERING_TYPE_NAME'] }}</h6>
								</td>
								<td class="etc">
									<h6>{{ $data['registrants'][$idx]['ETC'] }}</h6>
								</td>
								<td class="is-online">
									<h6>{{ $data['registrants'][$idx]['IS_ONLINE'] == 'Y' ? '온라인' : '오프라인' }}</h6>
								</td>
								<td>
									<input type="button" class="btn-sm btn-primary modify-btn" value="수정" data-value="{{ $data['registrants'][$idx]['NO'] }}">
									<input type="button" class="btn-sm btn-danger delete-btn" value="삭제" data-value="{{ $data['registrants'][$idx]['NO'] }}" data-page="{{ $data['page'] }}" data-count="{{ $data['registrants'][0]['TOTAL_CNT'] }}">
									<input type="button" class="btn-sm btn-primary modify-complete-btn" value="수정완료" data-value="{{ $data['registrants'][$idx]['NO'] }}" data-page="{{ $data['page'] }}" >
									<input type="button" class="btn-sm btn-danger cancel-btn" value="취소" data-value="{{ $data['registrants'][$idx]['NO'] }}">
								</td>
							</tr>
						@endfor
					@endif
					</tbody>
				</table>

				@if(array_key_exists('pagination', $data))
					<div>
						{{ $data['pagination']['paging'] }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
<script src="/assets/js/offering/write/income_list.js"></script>
