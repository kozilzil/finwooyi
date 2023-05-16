<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title card-title-dash">성도 리스트</h4>
							</div>
							<div>
								<form class="search-form" action="/management/user" method="get">
									<div class="form-group">
										<div class="input-group">
											<select id="type" name="type" class="form-control">
												<option value="">전체</option>
												<option value="name" {{ $data['type'] == 'name' ? 'selected=selected' : '' }}>이름</option>
												<option value="id" {{ $data['type'] == 'id' ? 'selected=selected' : '' }}>아이디</option>
											</select>
											<input type="text" class="form-control" id="content" name="content" placeholder="내용" value="{{ $data['content'] }}">
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
									<th>등록일</th>
									<th>아이디</th>
									<th>삭제여부</th>
								</tr>
								</thead>
								<tbody>
								@if ($data['list'][0]['TOTAL_CNT'] > 0)
									@for($idx = 0; $idx < count($data['list']); $idx++)
										<tr>
											<td>
												<div class="form-check-flat mt-0">
													<a href="/management/user_write/{{ $data['list'][$idx]['NO'] }}">{{ $data['list'][0]['TOTAL_CNT'] - $idx - (($data['page'] -1) * $data['limit']) }}({{ $data['list'][$idx]['NO'] }})</a>
												</div>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['NAME'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['OFFICE'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['REG_DATE'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['ID'] }}</h6>
											</td>
											<td>
												<h6>{{ $data['list'][$idx]['IS_DELETE'] }}</h6>
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
						<div>
							<a class="btn btn-primary btn-lg text-white mb-0 me-0" type="button" href="/management/user_write"><i class="mdi mdi-account-plus"></i>신규등록</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
