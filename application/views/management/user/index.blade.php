@include('/_parts/topMenu')
<div class="container-fluid page-body-wrapper">
    @include('/_parts/sliderMenu')

    @include('/management/user/table', ['data' => $data['data']])
</div>
