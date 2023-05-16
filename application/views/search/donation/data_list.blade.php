<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h6><input type="text" class="form-control-sm date" id="date" name="date" placeholder="검색년도" value="{{ $data['year'] }}" data-value="{{ $data['info']['NO'] }}" readonly>년 <b>{{ $data['info']['NAME'] }}</b> 헌금내역</h6>
							</div>
						</div>
						<div class="table-responsive  mt-1">
							<table class="table select-table">
								<thead>
								<tr>
									<th>NO</th>
									<th>대분류</th>
									<th>소분류</th>
									<th>금액</th>
									<th>등록일</th>
								</tr>
								</thead>
								<tbody>
								@if(count($data['list']) == 0)
									<tr>
										<td colspan="5" class="text-center">
											<h6>데이터가 존재하지 않습니다.</h6>
										</td>
									</tr>
								@else
									@for($idx = 0; $idx < count($data['list']); $idx++)
										<tr>
											<td>
												<div class="form-check-flat mt-0">
													{{ count($data['list']) - $idx }}
												</div>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['PARENT_TITLE'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['CHILD_TITLE'] }}</h6>
											</td>
											<td>
												<h6>{{ number_format($data['list'][$idx]['PRICE']) }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['REG_DATE'] }}</h6>
											</td>
										</tr>
									@endfor
									<tr>
										<td>
											<h6>총계</h6>
										</td>
										<td>
										</td>
										<td>
										</td>
										<td>
											<h6>{{ number_format($data['list'][0]['TOTAL_PRICE']) }}</h6>
										</td>
										<td>
										</td>
									</tr>
								@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	// 달력처리
	$("#date").datepicker({
		format 		: "yyyy",
		autoclose	: true,
		viewMode: "years",
		minViewMode: "years"
	})

	// 달력변경
	$(document).on('change', '#date', function() {
		const year = $(this).val()
		const userNo = $(this).attr('data-value')
		location.href = `/search/donation_detail/${userNo}/${year}`;
	})

</script>
