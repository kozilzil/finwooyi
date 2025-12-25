<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-md-12 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title">
									<input type="text" class="form-control-sm date" id="year" name="year" placeholder="선택년도" readonly>
									<input type="hidden" id="carryover-price-pre">
									<input type="hidden" id="carryover-price-next">

									<input type="button" class="btn btn-sm btn-secondary" id="carryover-btn" value="이월금 설정" style="position: absolute;right: 140px;">
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

@include('/search/total/modal/carryover')
