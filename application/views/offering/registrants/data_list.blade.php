<div class="col-12 grid-margin stretch-card">
	<div class="card card-rounded">
		<div class="card-body">
			<div class="table-responsive  mt-1">
				<table class="table select-table" id="list-table">
					<thead>
					<tr>
						<th colspan="2" class="text-dark">
							<h5>헌금자 리스트</h5>
						</th>
					</tr>
					</thead>
					<tbody>
						@foreach($data as $key => $value)
							@if ( $value != null)
								<tr>
									<td width="10%">
										<h6>
											{{ $value[0]['OFFERING_NAME'] }}
										</h6>
									</td>
									<td>
										<h6>
										@for($idx = 0; $idx < count($data[$key]); $idx++)
											{{ $value[$idx]['NAME'] }}@if ( $value[$idx]['ETC'] != '')/{{ $value[$idx]['ETC'] }}@endif
										@endfor
										</h6>
									</td>
								</tr>
								<tr>
									<td></td>
									<td>
										<h6>
											이상 {{ count($data[$key]) }}명
										</h6>
									</td>
								</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>
