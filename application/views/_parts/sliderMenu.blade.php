<nav class="sidebar sidebar-offcanvas" id="sidebar">
	<ul class="nav">
		@foreach($data['view_data']['menu'] as $key => $item)
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="collapse" href="#ui-{{ $item['TYPE'] }}" aria-expanded="false" aria-controls="ui-income">
					<i class="mdi mdi-grid-large menu-icon"></i>
					<span class="menu-title">{{ $item['TITLE'] }}</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="ui-{{ $item['TYPE'] }}">
					<ul class="nav flex-column sub-menu">
						@foreach($item['child'] as $subKey => $subItem)
							<li class="nav-item">
								<a class="nav-link" href="{{ $subItem['URL'] }}">{{ $subItem['TITLE'] }}</a>
							</li>
						@endforeach
					</ul>
				</div>
			</li>
		@endforeach
	</ul>
</nav>
