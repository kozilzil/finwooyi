<?php
$array = [
		['title' => '동전', 'price' => '1'],
		['title' => '1,000원권', 'price' => '1000'],
		['title' => '5,000원권', 'price' => '5000'],
		['title' => '10,000원권', 'price' => '10000'],
		['title' => '50,000원권', 'price' => '50000'],
		['title' => '100,000원권', 'price' => '100000'],
		['title' => '500,000원권', 'price' => '500000'],
		['title' => '1,000,000원권', 'price' => '1000000'],
		['title' => '5,000,000원권', 'price' => '5000000']
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
									<th width="15%"></th>
									<th width="20%">입력란</th>
									<th width="20%">권종별 합</th>
									<th width="5%"></th>
									<th width="20%">내역</th>
									<th width="*">금액</th>
								</tr>
								</thead>
								<tbody>
								@for($idx = 0; $idx < count($array); $idx++)
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
										<td></td>
										<td>
											<input type="text" class="form-control">
										</td><td>
											<input type="text" class="form-control text-price" id="text-input-{{ $idx }}"
												   data-idx="{{ $idx }}"
												   oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
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
									<td></td>
									<td></td>
									<td>
										<input type="text" class="form-control total2" id="text-sum-total" readonly value="0">
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title">
									<input type="text" class="form-control-sm date" id="date" name="date" placeholder="전산일" readonly>
								</h4>
							</div>
						</div>

						<div class="table-responsive  mt-1">
							<table class="table select-table">
								<tbody>
									<tr>
										<th width="40%">계수 총액</th>
										<th width="*" id="counter-total"></th>
									</tr>
									<tr>
										<th>전산 총액</th>
										<th id="computation-total"></th>
									</tr>
									<tr>
										<td>차액</td>
										<td id="calc-total">0</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>
