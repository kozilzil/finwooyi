<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title card-title-dash">
									성도별 기부금영수증
									<input type="text" class="form-control-sm date" id="date" name="date" placeholder="검색년도" value="" readonly>
								</h4>
							</div>
							<div>
								<form class="search-form" action="/search/donation" method="get">
									<div class="form-group">
										<div class="input-group">
											<select id="type" name="type" class="form-control">
												<option value="">전체</option>
												<option value="name" {{ $data['view_data']['data']['type'] == 'name' ? 'selected=selected' : '' }}>이름</option>
												<option value="id" {{ $data['view_data']['data']['type'] == 'id' ? 'selected=selected' : '' }}>아이디</option>
											</select>
											<input type="text" class="form-control" id="content" name="content" placeholder="내용" value="{{ $data['view_data']['data']['content'] }}">
											<div class="input-group-append">
												<button class="btn btn-primary btn-sm text-white form-control" type="submit"><i class="icon-search"></i>검색</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="table-responsive  mt-1">
							<table class="table select-table">
								<thead>
								<tr>
									<th>NO</th>
									<th>이름</th>
									<th>소속</th>
									<th>아이디</th>
									<th>헌금상세</th>
								</tr>
								</thead>
								<tbody>
								@if ($data['view_data']['data']['list'][0]['TOTAL_CNT'] > 0)
									@for($idx = 0; $idx < count($data['view_data']['data']['list']); $idx++)
										<tr>
											<td>
												<div class="form-check-flat mt-0">
													{{ $data['view_data']['data']['list'][0]['TOTAL_CNT'] - $idx - (($data['view_data']['data']['page'] -1) * $data['view_data']['data']['limit']) }}({{ $data['view_data']['data']['list'][$idx]['NO'] }})
												</div>
											</td>
											<td>
												<h6>{{ $data['view_data']['data']['list'][$idx]['NAME'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['view_data']['data']['list'][$idx]['OFFICE'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['view_data']['data']['list'][$idx]['ID'] }}</h6>
											</td>
											<td>
												<button class="btn btn-info btn-sm text-white form-control donation-list-btn" value="{{ $data['view_data']['data']['list'][$idx]['NO'] }}">헌금상세</button>
											</td>
										</tr>
									@endfor
								@endif
								</tbody>
							</table>

							@if(array_key_exists('pagination', $data['view_data']['data']))
								<div>
									{{ $data['view_data']['data']['pagination']['paging'] }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
