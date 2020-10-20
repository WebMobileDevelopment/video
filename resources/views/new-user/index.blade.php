@extends('layouts.user')

@section('styles')

    <style type="text/css">
        
    .list-inline {
      text-align: center;
    }
    .list-inline > li {
      margin: 10px 5px;
      padding: 0;
    }
    .list-inline > li:hover {
      cursor: pointer;
    }
    .list-inline .selected img {
      opacity: 1;
      border-radius: 15px;
    }
    .list-inline img {
      opacity: 0.5;
      -webkit-transition: all .5s ease;
      transition: all .5s ease;
    }
    .list-inline img:hover {
      opacity: 1;
    }

    .item > img {
      max-width: 100%;
      height: auto;
      display: block;
    }

    .carousel-inner .active {

        background-color: none;
    }

    .carousel-inner .item {

        padding: 0px;

    }
    </style>
@endsection

@section('content')

    <div class="y-content">

        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-xs-12 col-sm-9 col-md-10">

                @include('notification.notify')

                @foreach($data as $section_data)

                @include('new-user.videos._videos', ['data' => $section_data])

               @endforeach



            </div>

        </div>

    </div>

@endsection