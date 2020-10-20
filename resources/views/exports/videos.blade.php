<!DOCTYPE html>
<html>

<head>
    <title>{{$title}}</title>  
    <meta name="robots" content="noindex">
</head>
<style type="text/css">

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .first_row_design{
        background-color: #8B0000;
        color: #ffffff;
    }

    .row_col_design{
        background-color: #cccccc;
    }

    th{
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;

    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;

    }
    
</style>

<body>

    <table>

        <!-- HEADER START  -->

        <tr class="first_row_design">

            <th >{{tr('s_no')}}</th>

            <th >{{tr('channel')}}</th>

            <th >{{tr('category')}}</th>

            <th >{{tr('video_type')}} </th>

            <th >{{tr('title')}}</th>

            <th >{{tr('ppv')}}</th>

            <th >{{tr('is_ads')}}</th>

            <th >{{tr('duration')}}</th>

            <th >{{tr('age_limit')}}</th>

            <th >{{tr('publish_type')}}</th>

            <th >{{tr('admin_video_status')}}</th>

            <th >{{tr('user_video_status')}}</th>

            <th >{{tr('ratings')}}</th>

            <th >{{tr('views')}}</th>

            <th >{{tr('tags')}}</th>

            <th >{{tr('ads_view_revenue')}}</th>

            <th >{{tr('likes')}}</th>

            <th >{{tr('dislikes')}}</th>

            <th >{{tr('description')}}</th>

            <th >{{tr('reviews')}}</th>

            <th >{{tr('user_reviews')}}</th>

            <th >{{tr('admin_ppv_amount')}}</th>

            <th >{{tr('user_ppv_amount')}}</th>

            <th >{{tr('total')}}</th>

            <th >{{tr('default_image')}}</th>

            <th >{{tr('video')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $video_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>{{$video_details->getChannel ? $video_details->getChannel->name : "-"}}</td>
                
                <td>{{$video_details->category_name}}</td>

                <td>
                                        
                    @if($video_details->video_type == VIDEO_TYPE_UPLOAD) 
                        
                        {{tr('manual_upload')}}

                    @elseif($video_details->video_type == VIDEO_TYPE_YOUTUBE)

                        {{tr('youtube_links')}}

                    @else

                        {{tr('other_links')}}

                    @endif
                </td>

                <td>{{$video_details->title}}</td>

                <td>
                    @if($video_details->ppv_amount > 0)

                        <span class="text-green">{{tr('yes')}}</span>

                    @else

                        <span class="text-red">{{tr('no')}}</span>

                    @endif
                </td>

                <td>
                    @if($video_details->ad_status)
                        <span class="text-green">{{tr('yes')}}</span>
                    @else
                        <span class="text-red">{{tr('no')}}</span>
                    @endif
                </td>

                <td>{{$video_details->duration}}</td>


                <td>{{$video_details->age_limit ? '18+' :  'All Users'}}</td>

                <td>
                    @if($video_details->publish_status)
                        <span class="text-green">{{tr('now')}}</span>
                    @else
                        <span class="text-red">{{tr('later')}}</span>
                    @endif

                </td>
                <td>@if ($video_details->compress_status == 0) 

                        {{tr('compress')}}
                    @else 

                        @if($video_details->status)

                            {{tr('approved')}}

                        @else

                            {{tr('pending')}}

                        @endif

                    @endif
                </td>

                <td>
                    @if ($video_details->compress_status == 0) 

                            {{tr('compress')}} 
                    @else 

                        @if($video_details->is_approved)

                            {{tr('approved')}}

                        @else

                           {{tr('pending')}}

                        @endif

                    @endif
                </td>

                <td> {{$video_details->ratings}} </td>

                <td> 
                    {{number_format_short($video_details->watch_count)}}
                </td>

                <td>    
                    
                    @if($video_details->getVideoTags) 

                        @foreach($video_details->getVideoTags as $tags)

                            @if($tags->getTag)

                            {{$tags->getTag->name}},

                            @endif

                        @endforeach

                    @endif
                </td>
                <td>
                    {{Setting::get('currency')}} {{number_format_short($video_details->amount)}}
                </td>

                <td>
                    {{number_format_short($video_details->getLikeCount->count())}}
                </td>

                <td>
                    {{number_format_short($video_details->getDisLikeCount->count())}}
                </td>


                <td>
                    {{ $video_details->description }}
                </td>

                <td>
                    {{$video_details->reviews}}
                </td>

                <td>
                    {{number_format_short($video_details->getUserRatings->count())}}

                </td>

                <td>
                    {{Setting::get('currency')}} {{$video_details->admin_ppv_amount}}
                </td>

                <td>
                   {{Setting::get('currency')}} {{$video_details->user_ppv_amount}}
                </td>

                <td>
                    {{Setting::get('currency')}} {{$video_details->admin_amount + $video_details->user_amount}}
                </td>

                <td>{{$video_details->default_image}}</td>

                <td>{{$video_details->video}}</td>


                <td>{{convertTimeToUSERzone($video_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($video_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            </tr>
        @endforeach
    </table>

</body>

</html>