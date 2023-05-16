<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table table-bordered select-table" id="list-table">
					<thead>
					<tr class="text-center">
						<th width="15%">대분류</th>
						<th width="15%">소분류</th>
						<th width="20%">계좌이체</th>
						<th width="15%">현금지급</th>
						<th width="15%">선지급</th>
						<th width="20%">합계</th>
					</tr>
					</thead>
					<tbody>

					@if (count($data) > 0)
						@foreach($data as $value)
							<tr class="text-center">
								@if( $value['parent']['count'] != 0 )
								<td rowspan="{{ $value['parent']['count'] }}">
									<h6>{{ $value['parent-name'] }}</h6>
								</td>
								@endif
								<td>
									<h6>{{ $value['name'] }}</h6>
								</td>
								<td>
									<h6>{{ number_format($value['bank']) }}</h6>
								</td>
								<td>
									<h6>{{ number_format($value['cash']) }}</h6>
								</td>
								<td>
									<h6>{{ number_format($value['payment']) }}</h6>
								</td>
								<td>
									<h6>{{ number_format($value['total']) }}</h6>
								</td>
							</tr>
						@endforeach
					@endif
					<tr class="text-center">
						<td colspan="2">
							<h6>총합</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['bank']) }}</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['cash']) }}</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['payment']) }}</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['total']) }}</h6>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
