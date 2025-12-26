<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>{{ $view_data['title'] }}</title>

	@include('_parts/header', ['data' => $view_data['header']])

	<link rel="stylesheet" href="/vendors/feather/feather.css">
	<link rel="stylesheet" href="/vendors/mdi/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="/vendors/ti-icons/css/themify-icons.css">
	<link rel="stylesheet" href="/vendors/typicons/typicons.css">
	<link rel="stylesheet" href="/vendors/simple-line-icons/css/simple-line-icons.css">
	<link rel="stylesheet" href="/vendors/css/vendor.bundle.base.css">
	<!-- endinject -->
	<!-- Plugin css for this page -->
	<!--<link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">-->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="/assets/js/select.dataTables.min.css">
	<!-- End plugin css for this page -->
	<!-- inject:css -->
	<link rel="stylesheet" href="/assets/css/vertical-layout-light/style.css">
	<!-- endinject -->
	<link rel="shortcut icon" href="/assets/images/favicon.png" />
	<style>
		@media (min-width: 992px) {
			.sidebar {
				position: sticky;
				top: 0;
				height: 100vh;
				overflow-y: auto;
			}
			.sticky-panel {
				position: sticky;
				top: 20px;
			}
			.list-scroll {
				max-height: calc(100vh - 220px);
				overflow-y: auto;
				overflow-x: auto;
				padding-bottom: 8px; /* ensure horizontal bar is visible without scrolling 전체 페이지 */
			}
		}
		.brand-text {
			font-weight: 700;
			font-size: 20px;
			color: #1f3bb3;
			letter-spacing: 0.5px;
			text-transform: uppercase;
		}
		.brand-text-mini {
			font-weight: 800;
			font-size: 16px;
			color: #1f3bb3;
			letter-spacing: 0.5px;
			text-transform: uppercase;
		}
	</style>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link rel="stylesheet" href="/assets/css/bootstrap-datepicker3.css">
	<script src="/assets/js/bootstrap-datepicker.js" charset="UTF-8"></script>
</head>
<body>
	<div id="wrap">
		@foreach($view_data['modal'] as $modal)
			@include($modal['view_name'], ['data' => $modal['view_data']])
		@endforeach

		@if ($data['view_name'] != null)
			@include($view_name, ['data' => $data['view_data']])
		@endif
	</div>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

	<!-- plugins:js -->
	<script src="/vendors/js/vendor.bundle.base.js"></script>
	<!-- endinject -->
	<!-- Plugin js for this page -->
	<script src="/vendors/chart.js/Chart.min.js"></script>
	<script src="/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
	<script src="/vendors/progressbar.js/progressbar.min.js"></script>

	<!-- End plugin js for this page -->
	<!-- inject:js -->
	<script src="/assets/js/off-canvas.js"></script>
	<script src="/assets/js/hoverable-collapse.js"></script>

	<script src="/assets/js/settings.js"></script>
	<script src="/assets/js/todolist.js"></script>
	<!-- endinject -->
	<!-- Custom js for this page-->
	<script src="/assets/js/jquery.cookie.js" type="text/javascript"></script>
	<script src="/assets/js/dashboard.js"></script>
	<script src="/assets/js/Chart.roundedBarCharts.js"></script>
	<!-- End custom js for this page-->

	@include('_parts/footer', ['data' => ['data' => $view_data['footer']]])
</body>
</html>
