
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

<!-- PLAYLIST POPUPSTART -->

<div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$video->video_tape_id}}" role="dialog">
   
   <div class="modal-dialog">
      <!-- Modal content-->
      
      <div class="modal-content">
         
         <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">&times;</button>

            <h4 class="modal-title">{{tr('save_to')}}</h4>

         </div>

         <div class="modal-body">

            <div class="more-content" id="user-playlists-form">

               <form name="user-playlists" method="post" id="user-playlists" action="{{route('user.add.spam_video')}}">
                  
                  <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />

                  <?php /** @foreach($playlists as $report)  

                     <div class="report_list">

                        <label class="playlist-container">One
                           
                           <input type="checkbox">
                           
                           <span class="playlist-checkmark"></span>

                        </label>

                     </div>

                     <div class="clearfix"></div>
                  @endforeach */ ?>

                  <div class="pull-right">
                     <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                  </div>

                  <div class="clearfix"></div>

               </form>
            
            </div>
            
         </div>
      </div>
      <!-- modal content ends -->
   </div>

</div>

<!-- <div class="modal modal-top1"  role="dialog" id="global_playlist_id_{{$video->video_tape_id}}">
  
   <div class="modal-dialog modal-sm">

      <div class="modal-content">
         <form action="" method="POST">
               <div class="modal-header">

                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                  <h4 class="modal-title share-title">{{tr('save_to')}}</h4>

               </div>

               <div class="modal-body">
                  
                  <div>

                     <label class="playlist-container">One
                        <input type="checkbox" checked="checked">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Two
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Three
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Four
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>

                  </div>

               </div>

         </form>
      </div>
   
   </div>

</div>
 -->
<!-- PLAYLIST POPUPEND -->


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