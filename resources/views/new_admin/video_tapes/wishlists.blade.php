@extends('layouts.admin')

@section('title', tr('view_wishlist'))

@section('content-header')

{{tr('view_wishlist')}} 

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.video_tapes.index')}}"><i class="fa fa-user"></i> {{tr('videos')}}</a></li>
    <li class="active"> {{tr('view_wishlist')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($wishlists) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('date')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($wishlists as $i => $wishlist_details)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	
							      	<td>
							      		<a href="{{ route('admin.users.view' ,  ['user_id' => $wishlist_details->id] ) }}">{{$wishlist_details->username}}</a>
							      	</td>
							      	
							      	<td>{{$wishlist_details->title}}</td>
							      	
							      	<td>{{$wishlist_details->created_at}}</td>
								   
								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropup">

								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} 
								                  <span class="caret"></span>
								                </a>

								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{route('admin.users.wishlist.delete' , ['wishlist_id' => $wishlist_details->wishlist_id] )}}">{{tr('delete_wishlist')}}</a></li>
								                </ul>

              								</li>
            							</ul>
								    </td>
							    </tr>					

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_wishlist_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


