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
						<option value="" data-no="{{ $value['NO'] }}">권한없음</option>
						<option value="R" data-no="{{ $value['NO'] }}">보기</option>
						<option value="W" data-no="{{ $value['NO'] }}">쓰기</option>
						<option value="A" data-no="{{ $value['NO'] }}">관리자</option>
					</select>
				</td>
			</tr>
			@foreach($value['child'] as $cKey => $cValue)
				@if( $value['NO'] == $cValue['PARENT'] )
					<tr>
						<td class="text-left vertical-middle"><label class="margin-bottom-0"> - {{ $cValue['TITLE'] }}</label></td>
						<td class="text-center vertical-middle">
							<select id="auth-child-{{ $cKey }}" name="auth-child-{{ $cKey }}" class="form-control">
								<option value="" data-no="{{ $cValue['NO'] }}">권한없음</option>
								<option value="R" data-no="{{ $cValue['NO'] }}">보기</option>
								<option value="W" data-no="{{ $cValue['NO'] }}">쓰기</option>
								<option value="A" data-no="{{ $cValue['NO'] }}">관리자</option>
							</select>
						</td>
					</tr>
				@endif
			@endforeach

		@endforeach
	</tbody>
</table>
