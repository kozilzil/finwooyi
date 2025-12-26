<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-md-4 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">지출부</h4>
						<div class="form-group">
							<input type="text" class="form-control-sm date" id="start-date" name="start-date" placeholder="시작일">
						</div>
						<div class="form-group">
							<label for="type">주차</label>
							<select id="weekly" name="weekly" class="form-control">
								@for($idx = 0; $idx < 5; $idx++)
									<option value="{{ $idx+1 }}">{{ $idx+1 }}주차</option>
								@endfor
							</select>
						</div>
						<div class="form-group">
							<label for="type">대분류</label>
							<select id="type" name="type" class="form-control" data-target="detail-type">
							</select>
						</div>
						<div class="form-group">
							<label for="detail-type">상세분류</label>
							<select id="detail-type" name="detail-type" class="form-control">
							</select>
						</div>
						<div class="form-group">
							<label for="contents" id="contents-lbl">상세내용</label>
							<input type="text" class="form-control" id="contents" placeholder="상세내용">
						</div>
						<div class="form-group" id="recipient-div">
							<label for="recipient" id="recipient-lbl">받는분 통장표시</label>
							<input type="text" class="form-control" id="recipient" placeholder="받는분 통장내역">
						</div>

						<div class="form-group">
							<label for="price">금액</label>
							<input type="text" class="form-control" id="price" placeholder="금액"
								   style="text-align: right"
								   oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
								   maxlength="15"
							>
							<label class="form-check-label" style="position: absolute">
								<input type="checkbox" id="price-thousand-check" class="form-check-input">
								*천원
							</label>
							<div id="price-text"></div>
						</div>


						<div class="form-group">
							<label for="pay-method">지불방법</label>
							<select id="pay-method" name="pay-method" class="form-control">
								<option value="bank">계좌이체</option>
								<option value="payment">선지급</option>
								<option value="cash">현금지금</option>
							</select>
						</div>
						<div id="account-div">
							<input type="hidden" id="account-no" value="">
							<div class="form-group">
								<input type="button" class="btn btn-sm btn-success form-control" id="bank-select" value="계좌선택">
								<div id="account-text"></div>
							</div>
						</div>
						<input type="button" id="register-btn" class="btn btn-primary me-2" value="등록">
					</div>
				</div>
			</div>
			<div class="col-md-8 grid-margin stretch-card">
				<div class="card card-rounded">
					<div class="card-body">
						<div class="table-responsive mt-1 list-scroll" id="data-list-div">

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- content-wrapper ends -->
</div>

@include('/expense/input/modal/account')
