@extends('layouts.admin')

@section('title', tr('reviews'))

@section('content-header', tr('reviews'))

@section('styles')

<link rel="stylesheet" href="{{ asset('assets/css/star-rating.css') }}">

@endsection

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-flag"></i>{{ tr('reviews') }}</li>
@endsection

@section('content')

	<div class="row">
        
        <div class="col-xs-12">
          	
          	@include('notification.notify')

          	<div class="box box-info">
            
            	<div class="box-body table-responsive">

	            	@if(count($reviews) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('username') }}</th>
							      <th>{{ tr('title') }}</th>
							      <th>{{ tr('ratings') }}</th>
							      <th>{{ tr('comments') }}</th>
							      <th>{{ tr('created_at') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($reviews as $i => $review_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>{{ $review_details->name }}</td>

								      	<td>
								      		<a href="{{ route('admin.video_tapes.view' , ['video_tape_id' => $review_details->video_id] ) }}" target="_blank">{{ $review_details->title }}</a>
								      	</td>

								      	<td>
								      		<span style="display: none;">{{ $review_details->rating }}</span>
								      		<input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="{{ $review_details->rating }}">
								      	</td>

								      	<td>{{ $review_details->comment }}</td>

								      	<td>{{ $review_details->created_at->diffForHumans() }}</td>

								      	<td><a href="{{ route('admin.reviews.delete', ['user_rating_id'=>$review_details->rating_id] ) }}" title="{{ tr('delete') }}" class="btn btn-sm btn-danger" onclick="return confirm( {{ tr('admin_user_review_delete_confirmation') }});"><i class="fa fa-trash"></i></a></td>

								    </tr>

								@endforeach

							</tbody>

						</table>

					@else
						<h3 class="no-result">{{ tr('no_result_found') }}</h3>
					@endif

            	</div>

         	</div>

        </div>

    </div>

@endsection



@section('scripts')

    <script type="text/javascript" src="{{ asset('assets/js/star-rating.js') }}"></script>

    <script type="text/javascript">
    	$('.view_rating').rating({disabled: true, showClear: false});
    </script>
@endsection