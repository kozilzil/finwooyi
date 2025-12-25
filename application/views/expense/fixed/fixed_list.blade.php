<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th width="5%">NO</th>
						<th width="5%">주차</th>
						<th width="10%">지출타입(대분류)</th>
						<th width="10%">지출타입(상세)</th>
						<th width="15%">금액</th>
						<th width="15%">상세내용</th>
						<th width="15%">받는분통장표시</th>
						<th width="15%">계좌</th>
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
								<td>
									<h6>{{ $data['registrants'][$idx]['WEEKLY'] }}주차</h6>
								</td>
								<td>
									<h6>{{ $data['registrants'][$idx]['OFFERING_TYPE_PARENT_NAME'] }}</h6>
								</td>
								<td>
									<h6>{{ $data['registrants'][$idx]['OFFERING_TYPE_NAME'] }}</h6>
								</td>
								<td>
									<h6>{{ number_format($data['registrants'][$idx]['PRICE'], 0) }}</h6>
								</td>
								<td>
									<h6>{{ $data['registrants'][$idx]['CONTENTS'] }}</h6>
								</td>
								<td>
									<h6>{{ $data['registrants'][$idx]['RECIPIENT'] }}</h6>
								</td>
								<td class="account">
									@if ( $data['registrants'][$idx]['NICK_NAME'] != '' )
										<h6>{{ $data['registrants'][$idx]['NICK_NAME'] }}({{ $data['registrants'][$idx]['HOLDER'] }} / {{ $data['registrants'][$idx]['BANK_NAME'] }} / {{ $data['registrants'][$idx]['ACCOUNT'] }})</h6>
									@endif
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
<script src="/assets/js/expense/fixed/fixed_list.js"></script>
