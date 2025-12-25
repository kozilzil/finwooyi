<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table table-bordered select-table" id="list-table">
					<thead>
						<tr class="text-center">
							<th width="20%">입력자</th>
							<th width="20%">타입</th>
							<th width="20%">온라인</th>
							<th width="20%">오프라인</th>
							<th width="20%">총액</th>
						</tr>
					</thead>
					<tbody>

					@if (count($data) > 0)
						@foreach($data as $value)
							@foreach($value['list'] as $subKey => $subValue)
								<tr class="text-center">
									@if($value['firstKey'] == $subKey)
									<td rowspan="{{ count($value['list']) +1 }}">
										<h6>{{ $value['name'] }}</h6>
									</td>
									<td><h6>총합</h6></td>
									<td style="text-align: right">
										<h6>{{ number_format($value['subOnlineTotal']) }}원</h6>
									</td>
									<td style="text-align: right">
										<h6>{{ number_format($value['subOfflineTotal']) }}원</h6>
									</td>
									<td style="text-align: right">
										<h6>{{ number_format($value['subTotal']) }}원</h6>
									</td>
								</tr>
								<tr class="text-center">
									@endif
									<td>
										<h6>{{ $subValue['type'] }}</h6>
									</td>
									<td style="text-align: right">
										<h6>{{ number_format($subValue['online']) }}원</h6>
									</td>
									<td style="text-align: right">
										<h6>{{ number_format($subValue['offline']) }}원</h6>
									</td>
									<td style="text-align: right">
										<h6>{{ number_format($subValue['price']) }}원</h6>
									</td>
								</tr>
							@endforeach

						@endforeach
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
