<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th colspan="8" class="text-dark">
							<h5>전체 조회</h5>
						</th>
					</tr>
					<tr>
						<th width="25%">항목</th>
						<th width="25%">현장</th>
						<th width="25%">온라인</th>
						<th width="25%">합계</th>
					</tr>
					</thead>
					<tbody>

					@if (count($data['total']) > 0)
						@foreach($data['total'] as $value)
							<tr>
								<td>
									<h6>{{ $value['name'] }}</h6>
								</td>
								<td>
									<h6>{{ number_format($value['offline']) }}원</h6>
								</td>
								<td>
									<h6>{{ number_format($value['online']) }}원</h6>
								</td>
								<td>
									<h6>{{ number_format($value['total']) }}원</h6>
								</td>
							</tr>
						@endforeach
					@endif
					<tr>
						<td>총계</th>
						<td>
							<h6>{{ number_format($sum['total']['offline']) }}원</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['total']['online']) }}원</h6>
						</td>
						<td>
							<h6>{{ number_format($sum['total']['total']) }}원</h6>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
