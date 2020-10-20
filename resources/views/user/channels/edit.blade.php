@extends('layouts.user')

@section('styles')

<!-- Add css file and inline css here -->
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

<style type="text/css">

.form-control {

	border-radius: 0px;
}

#c4-header-bg-container {
	background-image: url({{$model->cover}});
}

@media screen and (-webkit-min-device-pixel-ratio: 1.5),
screen and (min-resolution: 1.5dppx) {
	#c4-header-bg-container {
		background-image: url({{$model->cover}});
	}
}

#c4-header-bg-container .hd-banner-image {
	background-image: url({{$model->cover}});
}
</style>

@endsection 


@section('content')

@include('user.channels._form')

@endsection
