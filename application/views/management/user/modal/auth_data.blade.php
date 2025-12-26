@php
	$authMap = [];
	foreach ($auth as $a) {
		$authMap[$a['MENU_NO']] = $a['AUTH'];
	}
@endphp

<div class="mb-3">
	<label class="form-label fw-bold">권한 등급</label>
	<select id="role-select" class="form-control">
		<option value="4" @if(($user_role ?? 4) == 4) selected @endif>일반 (권한 없음)</option>
		<option value="3" @if(($user_role ?? 4) == 3) selected @endif>교역자 (헌금명단 보기)</option>
		<option value="2" @if(($user_role ?? 4) == 2) selected @endif>재정위원 (전체 권한)</option>
		<option value="1" @if(($user_role ?? 4) == 1) selected @endif>관리자 (전체 권한)</option>
	</select>
</div>

<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
		<th class="text-center vertical-middle" rowspan="2"><label class="margin-bottom-0 bold">권한명</label></th>
		<th class="text-center vertical-middle">
			전체반영
		</th>
	</tr>
	<tr>
		<th class="text-center vertical-middle">
			<select id="auth-all" name="auth-all" class="form-control">
				<option value="">권한없음</option>
				<option value="R">보기</option>
				<option value="W">쓰기</option>
				<option value="A">관리자</option>
			</select>
		</th>
	</tr>
	</thead>
	<tbody>
		@foreach($menu as $key => $value)
			<tr>
				<td class="text-left vertical-middle"><label class="margin-bottom-0">{{ $value['TITLE'] }}</label></td>
				<td class="text-center vertical-middle">
					<select id="auth-parent-{{ $key }}" name="auth-parent-{{ $key }}" class="form-control">
						<option value="" data-no="{{ $value['NO'] }}" @if(($authMap[$value['NO']] ?? '')=='') selected @endif>권한없음</option>
						<option value="R" data-no="{{ $value['NO'] }}" @if(($authMap[$value['NO']] ?? '')=='R') selected @endif>보기</option>
						<option value="W" data-no="{{ $value['NO'] }}" @if(($authMap[$value['NO']] ?? '')=='W') selected @endif>쓰기</option>
						<option value="A" data-no="{{ $value['NO'] }}" @if(($authMap[$value['NO']] ?? '')=='A') selected @endif>관리자</option>
					</select>
				</td>
			</tr>
			@foreach($value['child'] as $cKey => $cValue)
				@if( $value['NO'] == $cValue['PARENT'] )
					<tr>
						<td class="text-left vertical-middle"><label class="margin-bottom-0"> - {{ $cValue['TITLE'] }}</label></td>
						<td class="text-center vertical-middle">
							<select id="auth-child-{{ $cKey }}" name="auth-child-{{ $cKey }}" class="form-control">
								<option value="" data-no="{{ $cValue['NO'] }}" @if(($authMap[$cValue['NO']] ?? '')=='') selected @endif>권한없음</option>
								<option value="R" data-no="{{ $cValue['NO'] }}" @if(($authMap[$cValue['NO']] ?? '')=='R') selected @endif>보기</option>
								<option value="W" data-no="{{ $cValue['NO'] }}" @if(($authMap[$cValue['NO']] ?? '')=='W') selected @endif>쓰기</option>
								<option value="A" data-no="{{ $cValue['NO'] }}" @if(($authMap[$cValue['NO']] ?? '')=='A') selected @endif>관리자</option>
							</select>
						</td>
					</tr>
				@endif
			@endforeach

		@endforeach
	</tbody>
</table>

<script>
	// 서버에서 내려준 authMap을 사용해 현재 권한을 즉시 반영
	(function() {
		const role = {{ $user_role ?? 4 }};
		$('#role-select').val(role);
		@foreach($authMap as $mNo => $authVal)
			// menu_no에 해당하는 option을 찾아 select 값 설정
			(function(menuNo, authVal) {
				const $opt = $("option[data-no='" + menuNo + "']");
				if ($opt.length) {
					$opt.closest('select').val(authVal);
				}
			})({{ $mNo }}, "{{ $authVal }}");
		@endforeach
	})();
</script>
