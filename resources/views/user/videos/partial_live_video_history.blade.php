@foreach($live_video_history->data as $i => $video)
<li class="sub-list row">
	<div class="main-history">
	     <div class="history-image">
	        <a><img src="{{$video->video_image}}"></a> 
	        @if($video->amount > 0)
	            <div class="video_amount">

	            {{tr('pay')}} - {{Setting::get('currency')}} {{$video->amount}}

	            </div>
	            
	        @endif
	        <div class="video_duration">
	            {{$video->paid_date}}
	        </div>                          
	    </div><!--history-image-->

	    <div class="history-title">
	        <div class="history-head row">
	            <div class="cross-title">
	                <h5 class="payment_class"><a>{{$video->title}}</a></h5>
	                

	                <span class="video_views">
	                     
	                    {{$video->paid_date}}
	                </span> 

	            </div> 
	                                   
	        </div> <!--end of history-head--> 

	        <div class="description">
	            <p>{{$video->description}}</p>
	        </div><!--end of description--> 


	        <div>
	        	@if($video->user_amount > 0)
	        	<span class="label label-success">${{$video->user_amount}}</span>
	        	@endif
	        	
	        </div>
	                                                        
	    </div><!--end of history-title--> 
	    
	</div><!--end of main-history-->
</li> 
@endforeach