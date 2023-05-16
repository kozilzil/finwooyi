<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
	<div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start" id="slideer-menu-btn">
		<div class="me-3">
			<button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
				<span class="icon-menu"></span>
			</button>
		</div>
		<div>
			<a class="navbar-brand brand-logo" href="/">
				<img src="/assets/images/logo.svg" alt="logo" />
			</a>
			<a class="navbar-brand brand-logo-mini" href="/">
				<img src="/assets/images/logo-mini.svg" alt="logo" />
			</a>
		</div>
	</div>
	<div class="navbar-menu-wrapper d-flex align-items-top">
		<ul class="navbar-nav">
			<li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
				<h1 class="welcome-text"><span class="text-black fw-bold">우이중앙교회 재정부</span></h1>
			</li>
		</ul>
		<ul class="navbar-nav ms-auto">
			<li class="nav-item dropdown d-none d-lg-block user-dropdown">
				<a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="dropdown-item-icon mdi mdi-account text-primary"></i>{{ $this->session->userdata('info')['NAME'] }}
				</a>
				<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
					<a class="dropdown-item" href="/account/logout"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>로그아웃</a>
				</div>
			</li>
		</ul>
		<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
			<span class="mdi mdi-menu"></span>
		</button>
	</div>
</nav>
<script>
	$("#slideer-menu-btn").click(() => {
		$('.sidebar-offcanvas').toggle();
		$('.main-panel').width('100%')
	})
</script>
