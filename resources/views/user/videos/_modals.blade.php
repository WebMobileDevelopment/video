<div class="modal fade" id="report-form" role="dialog">
    
    <div class="modal-dialog">
       <!-- Modal content-->
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{tr('report_this_video')}}</h4>
            </div>
          
            <div class="modal-body">
                
                @if(Setting::get('is_spam'))
                   
                    @if(!$flaggedVideo)
                
                        <div class="more-content" id="report_video_form">
                
                            <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                              
                                @foreach($report_video as $report)  
                                   
                                    <div class="report_list">
                                        <label class="radio1">
                                            <input id="radio1" type="radio" name="reason" value="{{$report->value}}" required>
                                            
                                            <span class="outer"><span class="inner"></span></span>{{$report->value}}
                                        </label>
                                    </div>
                                    <div class="clearfix"></div>
                              
                                @endforeach

                                <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />

                                <p class="help-block"><small>{{tr('single_video_content')}}</small></p>
                                
                                <div class="pull-right">
                                    <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                                </div>
                                
                                <div class="clearfix"></div>
                
                            </form>
                       
                        </div>
                
                    @endif
                
                @endif
           
            </div>
        
        </div>
        <!-- modal content ends -->
    
    </div>

</div>

<!-- Login Modal -->

<div class="modal fade" id="login_error" role="dialog">
   
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{tr('authentication_error')}}</h4>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        {{tr('login_notes')}}   
                        <div class="clearfix"></div>
                        <br>
                        <div class="text-center">
                            <a href="{{route('user.login.form')}}"><button class="btn btn-sm btn-danger">{{tr('login')}}</button></a>
                        </div>
                    </div>
                </div>
            
            </div>
       </div>
       <!-- modal content ends -->
    
    </div>

</div>

<!-- Share Modal start -->

<div class="modal modal-top1"  role="dialog" id="popup1">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div>
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title share-title">{{tr('share')}}</h4>
            </div>
            <div class="modal-body">
               <div>
                  <a class="share-fb btn btn-primary btn-sm" target="_blank" href="http://www.facebook.com/sharer.php?u={{route('user.single',$video->video_tape_id)}}">
                  <i class="fa fa-facebook"></i>
                  </a>
                  <a class="share-twitter btn btn-info btn-sm" style="margin-left: 8px;" target="_blank" href="http://twitter.com/share?text={{$video->title}}...&url={{route('user.single',$video->video_tape_id)}}">
                  <i class="fa fa-twitter"></i>
                  </a> 
                  <input name="embed_link" class="form-control" id="embed_link" type="hidden" value="{{$embed_link}}">
                  <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#copy-embed" style="margin-left: 8px; margin-top: -1px;" title="{{tr('copy_embedded_link')}}" id="copy-embed1">
                  <i class="fa fa-link"></i>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>

</div>

<!-- Share Modal end -->

<!-- EMBED LINK start -->

<div class="modal fade modal-top" id="copy-embed" role="dialog">
    
    <div class="modal-dialog modal-lg">
    
        <div class="modal-content content-modal">
    
            <div class="row">
    
                <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7 modal-bg-img zero-padding hidden-xs" style="background-image: url({{$video->default_image ? $video->default_image : asset('images/landing-9.png')}});">
                   <h4 class="video-title1">{{$video->title}}</h4>
                
                </div>

                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5 right-space">
                
                    <div class="copy-embed">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title hidden-xs">{{tr('embed_video')}}</h4>
                            <h4 class="modal-title visible-xs">{{$video->title}}</h4>
                        </div>
                       
                        <div class="modal-body">
                            <form onsubmit="return false;">
                                <div class="form-group">
                                   <textarea class="form-control" rows="5" id="embed_link_url" readonly>{{$embed_link}}</textarea>
                                   <p class="underline1"></p>
                                </div>
                            </form>
                        </div>
                        
                        <div class="modal-footer">
                            
                            <button class="btn btn-danger pull-right " onclick="copyTextToClipboard();" >{{tr('copy')}}</button>
                        </div>
                    
                    </div>
                
                </div>
            
            </div>
   
        </div>
   
   </div>

</div>

<!-- Embed link End -->

<!-- PLAYLIST POPUPSTART -->

<div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$video->video_tape_id}}" role="dialog">
   
   <div class="modal-dialog">
        
        <!-- Modal content-->
        <div class="modal-content">
        
            <!-- if user logged in let create, update playlist -->
             
            @if(Auth::check())
             
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h4 class="modal-title">{{tr('save_to')}}</h4>

                </div>
                @if(!empty($playlists))
                <div class="modal-body">

                    <div class="more-content" id="user-playlists-form">
                                              
                        <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />

                            @foreach($playlists as $playlist_details)  

                                <div class="report_list">

                                    <label class="playlist-container">{{ $playlist_details->title}}
                                      
                                    <input type="checkbox" onclick="playlist_video_update({{$video->video_tape_id}} , {{ $playlist_details->playlist_id }} , this)" id="playlist_{{ $playlist_details->playlist_id }}" @if($playlist_details->is_video_exists == YES) checked @endif>
                                    
                                    @if($playlist_details->is_video_exists == YES)
                                    
                                    <input type="hidden" name="playlist_ids[]" value="{{$playlist_details->playlist_id}}">

                                    @endif
                                                                 
                                    <span class="playlist-checkmark"></span>

                                    </label>

                                </div>

                                <div class="clearfix"></div>

                            @endforeach

                    </div>
                    
                    @else
     
                    <div id="user-playlists-form" style="padding-left: 10px;">
     
                        <div id="user_playlists">
                            <span id="no_playlist_text" style="padding-left: 10px;">{{tr('no_playlists')}}</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>   
                                
                @endif  
             
                <div class="modal-footer">
                    
                    <div class="more-content">
                    
                        <div onclick="$('#create_playlist_form').toggle()">

                            <label><i class="fa fa-plus"></i> {{tr('create_playlist')}}</label>

                        </div>
                       
                        <div id="create_playlist_form" style="display: none">
                        
                            <div class="form-group">
                                
                                <input type="text" name="playlist_title" id="playlist_title" class="form-control" placeholder="{{tr('playlist_name_placeholder')}}">

                                <div class="" style="display: none;">

                                    <label for="playlist_privacy">Privacy</label>
                                    
                                    <select id="playlist_privacy" name="playlist_privacy" class="form-control">
                                        <option value="PUBLIC">PUBLIC</option>
                                        <option value="PRIVETE">PRIVETE</option>
                                        <option value="UNLISTED">UNLISTED</option>
                                    </select>
                                
                                </div>
                            
                            </div>

                            <button class="btn btn-primary" onclick='playlist_save_video_add("{{ $video->video_tape_id }}")'>{{tr('create')}}</button>

                        </div>

                    </div>

                </div>

             <!-- if user not logged in ask for login -->

            @else

                <div class="menu4 top nav-space">
                      
                    <p>{{tr('signid_for_playlist')}}</p>

                    <a href="{{route('user.login.form')}}" class="btn btn-sm btn-primary">{{tr('login')}}</a>

                </div> 

            @endif 

        </div>
        <!-- modal content ends -->

   </div>

</div>

<!-- PLAYLIST POPUPEND -->

