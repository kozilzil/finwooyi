<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-md-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title">
									<input type="text" class="form-control-sm date" id="check-date" name="check-date" placeholder="설정년도" readonly>
									<input type="text" class="form-control-sm date" id="start-date" name="start-date" placeholder="시작일" readonly>
									<input type="text" class="form-control-sm date" id="end-date" name="end-date" placeholder="종료일" readonly>
									<select id="type" name="type" class="form-control-sm" data-target="detail-type">
									</select>
									<select id="detail-type" name="detail-type" class="form-control-sm">
									</select>

									<input type="button" class="btn btn-sm btn-primary" id="excel-btn" value="엑셀 다운로드" style="position:absolute;right: 20px">
								</h4>
							</div>
						</div>
						<div class="table-responsive mt-1" id="data-list-div">

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>
