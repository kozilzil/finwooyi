<div class="table-responsive ">
	<table class="table select-table" id="list-table">
		<thead>
		<tr>
			<th width="5%">
				<input type="checkbox" class="form-check-input" id="all-select-btn" style="margin-top:0;margin-left: 5px">
			</th>
			<th width="5%">주차</th>
			<th width="10%">지출타입(대분류)</th>
			<th width="10%">지출타입(상세)</th>
			<th width="15%">금액</th>
			<th width="15%">상세내용</th>
			<th width="15%">받는분통장표시</th>
			<th width="*">계좌</th>
		</tr>
		</thead>
		<tbody>
		@if ($data['registrants'][0]['TOTAL_CNT'] > 0)
			@for($idx = 0; $idx < count($data['registrants']); $idx++)
				<tr>
					<td>
						<input type="checkbox" class="form-check-input select-btn"
							   data-type="{{ $data['registrants'][$idx]['PAYMETHOD'] }}"
							   data-accountno="{{ $data['registrants'][$idx]['ACCOUNT_NO'] }}"
							   data-nickname="{{ $data['registrants'][$idx]['NICK_NAME'] }}"
							   data-holder="{{ $data['registrants'][$idx]['HOLDER'] }}"
							   data-account="{{ $data['registrants'][$idx]['ACCOUNT'] }}"
							   data-bankname="{{ $data['registrants'][$idx]['BANK_NAME'] }}"
							   data-parent="{{ $data['registrants'][$idx]['OFFERING_TYPE_PARENT_NO'] }}"
							   data-child="{{ $data['registrants'][$idx]['OFFERING_TYPE_NO'] }}"
							   data-contents="{{ $data['registrants'][$idx]['CONTENTS'] }}"
							   data-recipient="{{ $data['registrants'][$idx]['RECIPIENT'] }}"
							   data-price="{{ number_format($data['registrants'][$idx]['PRICE'], 0) }}"
							   style="margin-top:0;margin-left: 5px"
						>
					</td>
					<td>
						<h6>{{ $data['registrants'][$idx]['WEEKLY'] }}</h6>
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
<script src="/assets/js/expense/input/fixed_list.js"></script>
