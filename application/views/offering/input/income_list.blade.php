@php
    $registrants = $data['registrants'] ?? [];
    $page = $data['page'] ?? 1;
    $limit = $data['limit'] ?? 0;
@endphp

<div class="col-12 grid-margin stretch-card">
		<div class="card card-rounded">
			<div class="card-body">
				<div class="table-responsive  mt-1">
					<table class="table select-table" id="list-table">
						<thead>
						<tr>
							<th colspan="8" class="text-dark">
								<h5>합계 : {{ number_format($registrants[0]['TOTAL_PRICE'] ?? 0) }}</h5>
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
							@if (($registrants[0]['TOTAL_CNT'] ?? 0) > 0)
								@for($idx = 0; $idx < count($registrants); $idx++)
									<tr>
										<td>
											<div class="form-check-flat mt-0">
												<a href="/management/user_write/{{ $registrants[$idx]['NO'] }}">{{ ($registrants[0]['TOTAL_CNT'] ?? 0) - $idx - (($page -1) * $limit) }}</a>
											</div>
										</td>
									<td class="user-name">
										<h6>{{ $registrants[$idx]['USER_NAME'] }}</h6>
									</td>
									<td class="price">
										<h6>{{ number_format($registrants[$idx]['PRICE'] ?? 0, 0) }}</h6>
									</td>
									<td class="offering-type-parent-name">
										<h6>{{ $registrants[$idx]['OFFERING_TYPE_PARENT_NAME'] }}</h6>
									</td>
									<td class="offering-type-name">
										<h6>{{ $registrants[$idx]['OFFERING_TYPE_NAME'] }}</h6>
									</td>
									<td class="etc">
										<h6>{{ $registrants[$idx]['ETC'] }}</h6>
									</td>
									<td class="is-online">
										<h6>{{ $registrants[$idx]['IS_ONLINE'] == 'Y' ? '온라인' : '오프라인' }}</h6>
									</td>
									<td>
										<input type="button" class="btn-sm btn-primary modify-btn" value="수정" data-value="{{ $registrants[$idx]['NO'] }}">
										<input type="button" class="btn-sm btn-danger delete-btn" value="삭제" data-value="{{ $registrants[$idx]['NO'] }}" data-page="{{ $page }}" data-count="{{ $registrants[0]['TOTAL_CNT'] ?? 0 }}">
										<input type="button" class="btn-sm btn-primary modify-complete-btn" value="수정완료" data-value="{{ $registrants[$idx]['NO'] }}" data-page="{{ $page }}" >
										<input type="button" class="btn-sm btn-danger cancel-btn" value="취소" data-value="{{ $registrants[$idx]['NO'] }}">
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
<script src="/assets/js/offering/input/income_list.js"></script>
