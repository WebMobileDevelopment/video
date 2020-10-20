@extends('layouts.user')

@section('content')
	
	<div class="row" style="margin-top:10px;margin-bottom:10px;min-height:500px;">

		<div class="large-8 columns">

            <section class="content content-with-sidebar">
                <!-- newest video -->
                <div class="main-heading removeMargin">
                    <div class="row secBg padding-14 removeBorderBottom">
                        <div class="medium-8 small-8 columns">
                            <div class="head-title">
                                <i class="fa fa-user"></i>

                                @if($about)
                                	<h4>{{$about->heading}}</h4>
                                @else
                                	<h4>{{tr('about')}} {{Setting::get('site_name')}}</h4>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row secBg">
                    <div class="large-12 columns">
                        <article class="page-content">
                        	@if($about)
                        		<?= $about->description; ?>
                            @else
                            	<p>{{tr('about_text_content')}}</p>
                            @endif
                        </article>
                    </div>
                </div>
            
            </section>

        </div>

	</div>

@endsection