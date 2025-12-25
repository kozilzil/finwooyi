<?php
	$array = [
			['title' => '동전', 'price' => '1'],
			['title' => '1,000원권', 'price' => '1000'],
			['title' => '5,000원권', 'price' => '5000'],
			['title' => '10,000원권', 'price' => '10000'],
			['title' => '50,000원권', 'price' => '50000'],
			['title' => '100,000원권', 'price' => '100000']
	];
?>
<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">

			<div class="col-md-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="table-responsive  mt-1">
							<table class="table select-table" id="list-table">
								<thead>
								<tr>
									<th width="30%"></th>
									<th width="30%">입력란</th>
									<th width="*">권종별 합</th>
								</tr>
								</thead>
								<tbody>
								@for($idx = count($array)-1; $idx >= 0; $idx--)
									<tr>
										<td>
											<h6>{{ $array[$idx]['title'] }}</h6>
										</td>
										<td>
											<input type="text" class="form-control price" id="input-{{ $idx }}"
												   placeholder="{{ $array[$idx]['price'] }}"
												   oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
												   data-idx="{{ $idx }}"
												   data-price="{{ $array[$idx]['price'] }}">
										</td>
										<td>
											<input type="text" class="form-control sum" id="sum-{{ $idx }}" readonly value="0">
										</td>
									</tr>
								@endfor
								<tr>
									<td>
										<h6>합계</h6>
									</td>
									<td></td>
									<td>
										<input type="text" class="form-control total" id="sum-total" readonly value="0">
									</td>
								</tr>
								</tbody>
							</table>
							<input type="button" id="clear-btn" class="btn btn-primary me-2" value="초기화">
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>
