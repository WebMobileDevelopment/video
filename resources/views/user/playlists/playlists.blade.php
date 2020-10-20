@foreach($video_tapes as $i => $video_tape_details)

  <li class="sub-list row">

      <div class="main-history">

          <div class="history-image">

              <a href="{{route('user.single', $video_tape_details->video_tape_id)}}">
                  <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video_tape_details->video_image}}" class="slide-img1 placeholder" />
              </a>

              @if($video_tape_details->ppv_amount > 0)
                  
                  <!--  @---if(!$video_tape_details->pay_per_view_status)
                      <div class="video_amount">

                      {{tr('pay')}} - {{Setting::get('currency')}}{{$video_tape_details->ppv_amount}}

                      </div>
                  @---endif -->

              @endif

              <div class="video_duration">
                  {{$video_tape_details->duration}}
              </div> 

          </div>

          <div class="history-title">
             
             <div class="history-head row">
                
                <div class="cross-title1">
                   
                   <h5><a href="{{route('user.single', $video_tape_details->video_tape_id)}}">{{$video_tape_details->title}}</a></h5>
                   
                   <span class="video_views">
                       <div><a href="{{route('user.channel',$video_tape_details->channel_id)}}">{{$video_tape_details->channel_name}}</a></div>
                       <i class="fa fa-eye"></i> {{$video_tape_details->watch_count}} {{tr('views')}} <b></b> 
                   </span> 

                </div> 
                  
                  @if(Auth::check())
                  
                      @if($playlist_details->user_id == Auth::user()->id)                                               
                      <div class="cross-mark1">
                              
                          <a onclick="return confirm(&quot;{{ substr($video_tape_details->title, 0 , 15)}}.. {{tr('user_playlist_video_remove_confirm') }}&quot;)" href="{{route('user.playlists.video_remove' , ['video_tape_id' => $video_tape_details->video_tape_id, 'playlist_id' => $playlist_details->playlist_id])}}"><i class="fa fa-times" aria-hidden="true"></i></a>

                      </div>
                      
                      @endif

                  @endif

                  <!--end of cross-mark-->  

                  <!-- @todo save to playlist : on click pop playlist to add and create playlist -->

              </div> <!--end of history-head--> 

              <div class="description">
                  <?php //$video_tape_details->description ?>
              </div><!--end of description--> 
                                                  
          </div><!--end of history-title--> 
             
      </div><!--end of main-history-->

  </li>   

  @endforeach