<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-md-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="form-group">
							<input type="text" class="form-control-sm date" id="year" name="year" placeholder="시작연도">
						</div>
						<div class="form-group">
							<label for="name">헌금대분류</label>
							<select id="type" name="type" class="form-control" data-target="detail-type">
							</select>
						</div>
						<div class="form-group">
							<label for="name">헌금상세분류</label>
							<select id="detail-type" name="detail-type" class="form-control">
							</select>
						</div>
						<div class="form-group">
							<label for="name">헌금자</label>
							<input type="text" class="form-control" id="name" placeholder="헌금자">
						</div>
						<div class="form-group">
							<label for="etc">추가입력</label>
							<input type="text" class="form-control" id="etc" placeholder="추가입력">
						</div>
						<input type="button" id="register-btn" class="btn btn-primary me-2" value="생성">
					</div>
				</div>
			</div>

			<div class="col-md-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="table-responsive  mt-1">
							<table class="table select-table" id="list-table">
								<thead>
								<tr>
									<th width="20%">헌금이름</th>
									<th width="20%">헌금자</th>
									<th width="30%">공동헌금자</th>
									<th width="*">헌금자코드</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										<h6 id="data-type"></h6>
									</td>
									<td>
										<h6 id="data-name"></h6>
									</td>
									<td>
										<h6 id="data-etc"></h6>
									</td>
									<td>
										<h6 id="data-code"></h6>
									</td>
								</tr>
								</tbody>
							</table>

							@if(array_key_exists('pagination', $data))
								<div>
									{{ $data['pagination']['paging'] }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>
