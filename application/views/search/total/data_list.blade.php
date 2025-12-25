<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th colspan="8" class="text-dark">
							<h5>총계정조회</h5>
						</th>
					</tr>
					<tr>
						<th colspan="2">날짜</th>
						<th width="30%">항목</th>
						<th width="25%">수입</th>
						<th width="25%">지출</th>
					</tr>
					</thead>
					<tbody id="total-list-body">
					@if (count($data) > 0)
						@foreach($data as $idx => $value)
							<tr>
								<td>
									@if(array_key_exists('month-chk', $value))
										<h6>{{ $value['MONTH'] }}</h6>
									@endif
								</td>
								<td>
									@if(array_key_exists('day-chk', $value))
										<h6>{{ $value['DAY'] }}</h6>
									@endif
								</td>
								<td>
									<h6>{{ $value['CHILD_TITLE'] }}</h6>
								</td>
								<td>
									@if($value['TYPE'] == 'INCOME')
										<h6>{{ number_format($value['PRICE']) }}원</h6>
									@endif
								</td>
								<td>
									@if($value['TYPE'] == 'EXPENSE')
										<h6>{{ number_format($value['PRICE']) }}원</h6>
									@endif
								</td>
							</tr>
							@if(array_key_exists('weekly-income', $value))
								<tr style="background-color: #f2f2f2;">
									<td colspan="3" class="text-center">
										<h6>주계</h6>
									</td>
									<td>
										<h6>{{ number_format($value['weekly-income']) }}원</h6>
									</td>
									<td>
										<h6>{{ number_format($value['weekly-expense']) }}원</h6>
									</td>
								</tr>
							@endif
							@if(array_key_exists('monthly-income', $value))
								<tr style="background-color: #f2f2f2;">
									<td colspan="3" class="text-center">
										<h6>월계</h6>
									</td>
									<td>
										<h6>{{ number_format($value['monthly-income']) }}원</h6>
									</td>
									<td>
										<h6>{{ number_format($value['monthly-expense']) }}원</h6>
									</td>
								</tr>
							@endif
							@if(array_key_exists('quarter-income', $value))
								<tr style="background-color: #f2f2f2;">
									<td colspan="3" class="text-center">
										<h6>분기계</h6>
									</td>
									<td>
										<h6>{{ number_format($value['quarter-income']) }}원</h6>
									</td>
									<td>
										<h6>{{ number_format($value['quarter-expense']) }}원</h6>
									</td>
								</tr>
							@endif
							@if(array_key_exists('total-income', $value))
								<tr style="background-color: #f2f2f2;">
									<td colspan="3" class="text-center">
										<h6>누계</h6>
									</td>
									<td>
										<h6>{{ number_format($value['total-income']) }}원</h6>
									</td>
									<td>
										<h6>{{ number_format($value['total-expense']) }}원</h6>
									</td>
								</tr>
							@endif
							@if ($value['CHILD_TITLE'] == '차년도 이월금' && ((int)$value['PRICE'] !== 0 || ((int)$value['total-income'] - (int)$value['total-expense'] == 0)))
								<tr>
									<td colspan="5" style="padding: 0px;border-bottom-color: red;border-bottom-style: double;border-bottom-width: medium;"></td>
								</tr>
							@endif
						@endforeach
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
