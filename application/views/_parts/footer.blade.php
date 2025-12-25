
@foreach($data['data']['css'] AS $key => $css)
	<link href="{{ $css['href'] . '?v=' . filemtime(substr($css['href'], 1))}}" rel="{{ $css['rel'] }}" type="{{ $css['type'] }}" />
@endforeach
@foreach($data['data']['js'] AS $key => $js)
	<script type="{{ $js['type'] }}" {{ $js['src'] != "" ? 'src="'.$js['src'].'?v=' . filemtime(substr($js['src'], 1)) . '"' : "" }}>{{ $js['data'] }}</script>
@endforeach
