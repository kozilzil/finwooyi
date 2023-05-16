<div class="container-fluid page-body-wrapper full-page-wrapper">
	<div class="content-wrapper d-flex align-items-center auth px-0">
		<div class="row w-100 mx-0">
			<div class="col-lg-4 mx-auto">
				<div class="auth-form-light text-left py-5 px-4 px-sm-5">
					<div class="brand-logo">
						<img src="/assets/images/logo.svg" alt="logo">
					</div>
					<h4>{{ getenv('title') }}</h4>
					<h6 class="fw-light">관리자 로그인</h6>
					<form class="pt-3" id="login-form" method="post" action="/account/login">
						<div class="form-group">
							<input type="text" class="form-control form-control-lg" id="id" name="id" placeholder="아이디를 입력하세요.">
						</div>
						<div class="form-group">
							<input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="비밀번호를 입력하세요.">
						</div>
						<div class="mt-3">
							<button id="login-btn" class="btn btn-block btn-primary btn-lg font-weight-medium">로그인</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
