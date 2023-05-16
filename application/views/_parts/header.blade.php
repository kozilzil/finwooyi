@foreach($data['css'] AS $key => $css)
	<link href="{{ $css['href'] . '?v=' . filemtime(substr($css['href'], 1))}}" rel="{{ $css['rel'] }}" type="{{ $css['type'] }}" />
@endforeach
<!--[if lt IE 9]>
<script src="/assets/js/common/html5Shiv.js"></script>
<![endif]-->
@foreach($data['js'] AS $key => $js)
	<script type="text/javascript" {{ $js['src'] != "" ? 'src="'.$js['src'].'?v=' . filemtime(substr($js['src'], 1)) . '"' : "" }}>{{ $js['data'] }}</script>
@endforeach

