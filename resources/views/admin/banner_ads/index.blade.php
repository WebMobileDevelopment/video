@extends('layouts.admin')

@section('title', tr('banner_ads'))

@section('content-header')

{{tr('banner_ads')}}

<br>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-book"></i> {{tr('banner_ads')}}</li>
@endsection

@section('content')

    @include('notification.notify')

    <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">

            <div class="box-header label-primary">
                <b>{{tr('banner_ads')}}</b>
                <a href="{{route('admin.banner-ads.create')}}" style="float:right" class="btn btn-default">{{tr('create_banner_ad')}}</a>
            </div>

            <div class="box-body">

                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                          <th>#{{tr('id')}}</th>
                          <th>{{tr('title')}}</th>
                          <th>{{tr('image')}}</th>
                          <th>{{tr('position')}}</th>
                          <th>{{tr('status')}}</th>
                          <th>{{tr('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $cnt = count($model); ?>

                        @foreach($model as $i => $result)
                
                            <tr>
                                <td>{{$i+1}}</td>
                                <td><a href="{{$result->link}}" target="_blank">{{$result->title}}</a></td>
                                <td><img src="{{$result->file}}" style="height: 60px" /></td>
                                <td>{{$result->position}}</td>
                                <td>
                                    
                                      @if($result->status)
                                            <span class="label label-success">{{tr('approved')}}</span>
                                        @else
                                            <span class="label label-warning">{{tr('pending')}}</span>
                                        @endif

                                </td>
                                
                                <td>

                                    <div class="dropdown">
                                        
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{tr('action')}}
                                            <span class="caret"></span>
                                        </button>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                                            <li>
                                                @if(Setting::get('admin_delete_control'))
                                                    <a href="javascript:;" class="btn disabled"><b>{{tr('edit')}}</b></a>
                                                @else
                                                    <a href="{{route('admin.banner-ads.edit', array('id' => $result->id))}}"><b>{{tr('edit')}}</b></a>
                                                @endif
                                            </li>

                                             <li>
                                                
                                                <a href="{{route('admin.banner-ads.view', array('id' => $result->id))}}"><b>{{tr('view')}}</b></a>
                                                
                                            </li>

                                            <li>

                                                <a data-toggle="modal" data-target="#position_{{$result->id}}" style="cursor: pointer;">
                                                    <b>
                                                        {{tr('change_position')}}
                                                    </b>
                                                </a>

                                                
                                            </li>

                                             <li>

                                                <a href="{{route('admin.banner-ads.status', $result->id)}}"><b>
                                                @if($result->status)
                                                    {{tr('decline')}}
                                                @else
                                                    {{tr('approve')}}
                                                @endif
                                                </b>
                                                </a>
                                            </li>
                                            <li>
                                                @if(Setting::get('admin_delete_control'))
                                                    <a href="javascript:;" class="btn disabled" style="text-align: left"><b>{{tr('delete')}}</b></a>

                                                @else
                                                    <a onclick="return confirm('Are you sure?')" href="{{route('admin.banner-ads.delete',array('id' => $result->id))}}"><b>{{tr('delete')}}</b></a>

                                                @endif

                                            </li>                                

                                        </ul>

                                    </div>
                                    
                                </td>
                            
                            </tr>

                            <!-- Modal -->
                            
                            <div id="position_{{$result->id}}" class="modal fade" role="dialog">
                                
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                    
                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                                            <h4 class="modal-title">{{tr('change_position')}}</h4>

                                        </div>

                                        <form action="{{route('admin.banner-ads.position', array('id'=>$result->id))}}" method="post">
                                            <div class="modal-body">

                                                <input type="hidden" name="position" id="position_name_{{$result->id}}" value="{{$result->position}}">
                                                
                                                @for($i = 1; $i <= $cnt; $i++)


                                                    <button type="button" id="{{$result->id}}_{{$i}}" class="{{$i == $result->position ? 'btn btn-danger' : 'btn btn-default'}}" onclick="selectCurrentPosition({{$cnt}}, {{$i}}, {{$result->id}}, this.id)">{{$i}}</button>

                                                @endfor

                                            </div>
                                          
                                            <div class="modal-footer">
                                            
                                                <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>

                                                <button type="submit" class="btn btn-success">{{tr('submit')}}</button>
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