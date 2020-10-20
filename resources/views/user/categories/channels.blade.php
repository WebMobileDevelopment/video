@foreach($channels as $i => $channel)


<li class="sub-list row">
	<div class="main-history">
		<div class="history-image">
		    <a href="{{route('user.channel', $channel->channel_id)}}" target="_blank"><img src="{{$channel->picture}}"></a>
		   
		    <div class="video_duration">
		        {{$channel->no_of_videos ? $channel->no_of_videos : 0}} {{tr('videos')}}
		    </div>                        
		</div><!--history-image-->

		<div class="history-title">
		    <div class="history-head row">
		        <div class="cross-title2">
		            <h5 class="payment_class"><a href="{{route('user.channel', $channel->channel_id)}}" target="_blank">{{$channel->title}}</a></h5>
		           	
		            <span class="video_views">
		                <i class="fa fa-eye"></i> {{$channel->no_of_subscribers}} {{tr('subscribers')}} <b>.</b> 
		                {{ common_date($channel->created_at) }}
		            </span>
		        </div> 

		                            <!--end of cross-mark-->                       
		    </div> <!--end of history-head--> 

		    <div class="description">
		        <p><?= $channel->description ?></p>
		    </div><!--end of description--> 

		                                               
		</div><!--end of history-title--> 

	</div><!--end of main-history-->
</li>    

@endforeach