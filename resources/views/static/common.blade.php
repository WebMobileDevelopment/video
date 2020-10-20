@extends('layouts.user')

@section('content')

    <div class="y-content">
        
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                @include('notification.notify')
                
                <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 top">
                                <div class="spacing1">
                                    <h2 class="static-head">@if($model) {{$model->heading}} @else {{tr('model')}} @endif</h2>
                                    
                                    <div>
                                        @if($model) <?php echo $model->description; ?> @else {{tr('model')}} @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

@endsection