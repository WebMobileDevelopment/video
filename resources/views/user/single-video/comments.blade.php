<div class="v-comments">

    <!-- Comment count start -->
    <div class="pull-left">

        @if(count($comments) > 0) 
            <h3 class="mb-15"><span class="c-380" id="comment_count">{{count($comments)}}</span>&nbsp;{{tr('comments')}}</h3>
        
        @endif
   
   </div>

    <!-- Comment count end -->

   <div class="clearfix"></div>


   <div class="com-content">

        <!-- Check the user is logged in and  -->

        @if(Auth::check())

            @if(Auth::user()->id != $video->channel_created_by)
                
                <div class="image-form">
                    
                    <div class="comment-box1">
                        
                        <div class="com-image">
                        
                           <img class="comment-user-pic" src="{{Auth::user()->picture}}">
                           
                        </div>

                        <!--end od com-image-->
                        
                        <div id="comment_form">

                            <div>
                                
                                <form method="post" id="comment_sent" name="comment_sent" action="{{route('user.add.comment')}}">

                                    <input type="hidden" value="{{$video->video_tape_id}}" name="video_tape_id">

                                    <!-- check the user already rated -->
                                    @if($comment_rating_status)
                                        <input id="rating_system" name="ratings" type="number" class="rating comment_rating" min="1" max="5" step="1">
                                    @endif
                                    
                                    <textarea rows="10" id="comment" name="comments" placeholder="{{tr('add_comment_msg')}}"></textarea>

                                    <p class="underline"></p>

                                    <button class="btn pull-right btn-sm btn-info btn-lg top-btn-space" type="submit" id="comment_btn">{{tr('comment')}}</button>

                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        
                        </div>

                        <!--end of comment-form-->
                    </div>
                </div>

            @endif

        @endif


        @if(count($comments) > 0)

            <div class="feed-comment">

                <span id="new-comment"></span>

                <!-- List comments start -->
                
                @foreach($comments as $c =>  $comment)
                    
                    <div class="display-com">
                       
                        <div class="com-image">
                            
                            <img class="comment-user-pic" src="{{$comment->picture}}">                                    
                        </div>

                        <div class="display-comhead">

                            <span class="sub-comhead">
                              
                                <a><h5 class="pull-left">{{$comment->username}}</h5></a>

                                <a class="text-none">
                                    <p>{{$comment->diff_human_time}}</p>
                                </a>
                                @if($comment->rating > 0)
                                <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="{{$comment->rating}}"></p>
                                @endif
                                <p class="com-para">{{$comment->comment}}</p>

                            </span>
                        
                        </div>

                    </div>

                @endforeach

                <!-- List comments start -->

            </div>

        @else

            <div class="feed-comment">
                
                <span id="new-comment"></span>

            </div>

        @endif
   </div>
</div>