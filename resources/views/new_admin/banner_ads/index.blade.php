@extends('layouts.admin')

@section('title', tr('banner_ads'))

@section('content-header')

    {{ tr('banner_ads') }}

    <br>
    <!-- <small class="header-note">** {{ tr('banner_ads_note') }} <a target="_blank" href="http://prntscr.com/hx6e61">http://prntscr.com/hx6e61</a>**</small> -->

@endsection

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-book"></i> {{ tr('banner_ads') }}</li>
@endsection

@section('content')

    <div class="row">

        <div class="col-xs-12">
        
            @include('notification.notify')

            <div class="box box-primary">

                <div class="box-header label-primary">
                    <b>{{ tr('banner_ads') }}</b>
                    <a href="{{ route('admin.banner_ads.create') }}" style="float:right" class="btn btn-default">{{ tr('create_banner_ad') }}</a>
                </div>

                <div class="box-body">

                    <table id="example1" class="table table-bordered table-striped">

                        <thead>
                            <tr>
                              <th>{{ tr('id') }}</th>
                              <th>{{ tr('title') }}</th>
                              <th>{{ tr('image') }}</th>
                              <th>{{ tr('position') }}</th>
                              <th>{{ tr('status') }}</th>
                              <th>{{ tr('action') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $cnt = count($banner_ads); ?>

                            @foreach($banner_ads as $i => $banner_ad_details)
                    
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    
                                    <td>
                                        <a href="{{ $banner_ad_details->link }}" target="_blank">{{ $banner_ad_details->title }}</a>
                                    </td>
                                   
                                    <td><img src="{{ $banner_ad_details->file }}" style="height: 60px" />
                                    </td>
                                    
                                    <td>{{ $banner_ad_details->position }}</td>
                                   
                                    <td>                                        
                                        @if($banner_ad_details->status == DEFAULT_TRUE)
                                            <span class="label label-success">{{ tr('approved') }}</span>
                                        @else
                                            <span class="label label-warning">{{ tr('pending') }}</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="dropdown">                                            
                                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ tr('action') }}
                                                <span class="caret"></span>
                                            </button>

                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">                    
                                                <li>          
                                                    <a href="{{ route('admin.banner_ads.view', ['banner_ad_id' => $banner_ad_details->id]  ) }}"><b>{{ tr('view') }}</b></a>
                                                </li>

                                                @if(Setting::get('admin_delete_control') == YES)
                                                    <li><a href="javascript:;" class="btn disabled"><b>{{ tr('edit') }}</b></a>></li>

                                                    <li><a href="javascript:;" class="btn disabled" style="text-align: left" onclick="return confirm(&quot;{{ tr('admin_banner_ad_delete_confirmation', $banner_ad_details->title ) }}&quot;);"><b>{{ tr('delete') }}</b></a></li>
                                                
                                                @else

                                                    <li><a href="{{ route('admin.banner_ads.edit', ['banner_ad_id' => $banner_ad_details->id] ) }}"><b>{{ tr('edit') }}</b></a></li>
                                                    
                                                    <li>
                                                        <a href="{{ route('admin.banner_ads.delete',['banner_ad_id' => $banner_ad_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_banner_ad_delete_confirmation', $banner_ad_details->title ) }}&quot;);" ><b>{{ tr('delete') }}</b></a>
                                                    </li>
                                                    
                                                @endif

                                                <li>
                                                    <a data-toggle="modal" data-target="#position_{{ $banner_ad_details->id }}" style="cursor: pointer;">
                                                        <b>{{ tr('change_position') }}</b>
                                                    </a>
                                                </li>

                                                <li>
                                                    @if($banner_ad_details->status == DEFAULT_TRUE )               
                                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.banner_ads.status', ['banner_ad_id' => $banner_ad_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_banner_ad_decline_confirmation', $banner_ad_details->title) }}&quot;)"><b>{{ tr('decline') }}</b></a></li>
                                                    
                                                    @else
                                                    
                                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.banner_ads.status',['banner_ad_id' => $banner_ad_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_banner_ad_approve_confirmation', $banner_ad_details->title) }}&quot;)" ><b>{{ tr('approve') }}</b></a></li>
                                                    @endif
                                                </li>                          

                                            </ul>

                                        </div>
                                        
                                    </td>
                                
                                </tr>

                                <!-- Modal -->
                                
                                <div id="position_{{ $banner_ad_details->id }}" class="modal fade" role="dialog">
                                    
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                        
                                            <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                <h4 class="modal-title">{{ tr('change_position') }}</h4>

                                            </div>

                                            <form action="{{ route('admin.banner_ads.position', ['banner_ad_id' => $banner_ad_details->id] ) }}" method="post">
                                                <div class="modal-body">

                                                    <input type="hidden" name="position" id="position_name_{{ $banner_ad_details->id }}" value="{{ $banner_ad_details->position }}">
                                                    
                                                    @for($i = 1; $i <= $cnt; $i++)


                                                        <button type="button" id="{{ $banner_ad_details->id }}_{{ $i }}" class="{{ $i == $banner_ad_details->position ? 'btn btn-danger' : 'btn btn-default' }}" onclick="selectCurrentPosition({{ $cnt }}, {{ $i }}, {{ $banner_ad_details->id }}, this.id)">{{ $i }}</button>

                                                    @endfor

                                                </div>
                                              
                                                <div class="modal-footer">
                                                
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('close') }}</button>

                                                    <button type="submit" class="btn btn-success">{{ tr('submit') }}</button>
                                                </div>

                                            </form>
                                        
                                        </div>

                                    </div>
                                
                                </div>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('scripts')

<script type="text/javascript">
    
function selectCurrentPosition(no_of_positions, current_position, id, btnid) {

    console.log(id);

    console.log(btnid);

    console.log(no_of_positions);
    
    for(var i = 0; i < no_of_positions; i++) {

        $("#"+id+"_"+i).removeClass('btn-success');
        
        $("#"+id+"_"+i).addClass('btn-default');

    }

    // console.log(current_position);

    $("#"+btnid).removeClass('btn-default');

    $("#"+btnid).addClass('btn-success');

    $("#position_name_"+id).val(current_position);

}
</script>
@endsection