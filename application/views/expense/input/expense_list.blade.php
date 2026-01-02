@php
    $registrants = isset($data['registrants']) ? $data['registrants'] : [];
    $totalCnt = isset($registrants[0]['TOTAL_CNT']) ? $registrants[0]['TOTAL_CNT'] : 0;
    $totalPrice = isset($registrants[0]['TOTAL_PRICE']) ? $registrants[0]['TOTAL_PRICE'] : 0;
@endphp

<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th colspan="8" class="text-dark">
							<h5>합계 : {{ number_format($totalPrice) }}</h5>
						</th>
					</tr>
					<tr>
						<th width="5%">NO</th>
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
					@if ($totalCnt > 0)
						@for($idx = 0; $idx < count($registrants); $idx++)
							<tr>
								<td>
									<div class="form-check-flat mt-0">
										<a href="/management/user_write/{{ $registrants[$idx]['NO'] }}">{{ $totalCnt - $idx - (($data['page'] -1) * $data['limit']) }}</a>
									</div>
								</td>
								<td>
									<h6>{{ $registrants[$idx]['OFFERING_TYPE_PARENT_NAME'] }}</h6>
								</td>
								<td>
									<h6>{{ $registrants[$idx]['OFFERING_TYPE_NAME'] }}</h6>
								</td>
								<td>
									<h6>{{ number_format(isset($registrants[$idx]['PRICE']) ? $registrants[$idx]['PRICE'] : 0, 0) }}</h6>
								</td>
								<td>
									<h6>{{ $registrants[$idx]['CONTENTS'] }}</h6>
								</td>
								<td>
									<h6>{{ $registrants[$idx]['RECIPIENT'] }}</h6>
								</td>
								<td class="account">
									@if ( $registrants[$idx]['NICK_NAME'] != '' )
									<h6>{{ $registrants[$idx]['NICK_NAME'] }}({{ $registrants[$idx]['HOLDER'] }} / {{ $registrants[$idx]['BANK_NAME'] }} / {{ $registrants[$idx]['ACCOUNT'] }})</h6>
									@endif
								</td>
								<td>
									<input type="button" class="btn-sm btn-primary modify-btn" value="수정" data-value="{{ $registrants[$idx]['NO'] }}">
									<input type="button" class="btn-sm btn-danger delete-btn" value="삭제" data-value="{{ $registrants[$idx]['NO'] }}" data-page="{{ $data['page'] }}" data-count="{{ $totalCnt }}">
									<input type="button" class="btn-sm btn-primary modify-complete-btn" value="수정완료" data-value="{{ $registrants[$idx]['NO'] }}" data-page="{{ $data['page'] }}" >
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
<script src="/assets/js/expense/input/expense_list.js"></script>
