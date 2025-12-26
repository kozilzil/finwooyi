<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-md-4 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">등록</h4>

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
							<label for="is-online">헌금방식</label>
							<select id="is-online" name="is-online" class="form-control">
								<option value="N">오프라인</option>
								<option value="Y">온라인</option>
							</select>
						</div>
						<div class="form-group">
							<label for="name">헌금자</label>
							<input type="text" class="form-control" id="name" placeholder="헌금자">
						</div>
						<div class="form-group">
							<label for="price">금액</label>
							<input type="text" class="form-control" id="price" placeholder="금액"
								   style="text-align: right"
								   oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
								   maxlength="15"
							>
							<label class="form-check-label" style="position: absolute">
								<input type="checkbox" id="price-thousand-check" class="form-check-input">
								*천원
							</label>
							<div id="price-text"></div>
						</div>
						<div class="form-group">
							<label for="etc">추가입력</label>
							<input type="text" class="form-control" id="etc" placeholder="추가입력">
						</div>
						<input type="button" id="register-btn" class="btn btn-primary me-2" value="등록">
					</div>
				</div>
			</div>
			<div class="col-md-8 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="d-sm-flex justify-content-between align-items-start">
							<div>
								<h4 class="card-title">
									<input type="text" class="form-control-sm date" id="start-date" name="start-date" placeholder="시작일" readonly>
								</h4>
							</div>
						</div>
						<div class="table-responsive mt-1 list-scroll" id="data-list-div">

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>


