@extends('layouts.user')

@section('styles')

<style>

.redeem-content {
    margin:3em 0 1em 0;line-height: 1.8em;
}

table {
    box-shadow: 0px 1px 5px grey !important;
}
thead>tr>th {
    padding: 1% !important;
}
</style>

@endsection

@section('content')

    <div class="y-content">
    
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="history-content page-inner col-sm-9 col-md-10">

                @include('notification.notify')

                <div class="new-history">

                    <div class="content-head">

                        <div><h4>@if($channel) '{{$channel->name}}' {{tr('channel')}} @endif {{tr('subscribers')}}</h4></div>

                    </div>

                        @if(count($subscribers) > 0)
                        
                        <div class="row">
                            @foreach($subscribers as $i => $subscriber)

                            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 top">
                                <div class="sub-bg-img" style="background-image: url({{asset('images/popup-back.jpg')}});">
                                    <div class="sub-bg-overlay">
                                        <img src="{{$subscriber->user_image}}" alt="user-image">
                                        <h4>{{$subscriber->user_name}}</h4>
                                        <p>{{$subscriber->created_at->diffForHumans()}}</p>
                                        <a class="btn btn-sm btn-danger text-uppercase" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscriber->subscriber_id))}}"  onclick="return confirm(&quot;{{ $subscriber->user_name }} -  {{tr('user_unsubscribe_confirm') }}&quot;)"><i class="fa fa-times"></i>&nbsp;{{tr('un_subscribe')}}</a>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </div>

                        @if($subscribers)
                            <div class="row">
                                <div class="col-md-12">
                                    <div align="center" id="paglink"><?php echo $subscribers->links(); ?></div>
                                </div>
                            </div>
                        @endif

                        @else 

                            <div class="row">
                                <div class="col-md-12">
                                    <div align="center" id="paglink">{{tr('no_subscribers_found')}}</div>
                                </div>
                            </div>


                        @endif
            
                </div>
            
                <div class="sidebar-back"></div> 
            </div>
    
        </div>
    </div>

@endsection