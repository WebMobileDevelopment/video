<!DOCTYPE html>
<html>

<head>
    <title>{{$title}}</title>
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
</head>

<body>

    <table>

        <!------ HEADER START  ------>

        <tr class="first_row_design">
            <th>{{tr('s_no')}}</th>

            <th>{{tr('username')}}</th>

            <th>{{tr('picture')}}</th>


            <th>{{tr('created')}}</th>


            <th>{{tr('title')}}</th>

            <th>{{tr('payment')}}</th>

            <th>{{tr('is_streaming')}}</th>

            <th>{{tr('video_type')}}</th>

            <th>{{tr('description')}}</th>

            <th>{{tr('viewers_cnt')}}</th>

            <th>{{tr('start_time')}}</th>

            <th>{{tr('end_time')}}</th>

            <th>{{tr('no_of_minuts')}}</th>

            <th>{{tr('created_at')}}</th>

            <th>{{tr('updated')}}</th>

            <th>{{tr('user_commission')}}</th>

            <th>{{tr('admin_commission')}}</th>


        </tr>

        <!------ HEADER END  ------>

        @foreach($data as $i => $video_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>{{$video_details->user ? $video_details->user->name : ""}}</td>

                <td>{{$video_details->user ?  $video_details->user->picture : asset('placeholder.png') }}</td>

                <td>{{$video_details->created_at ? $video_details->created_at->diffForHumans() : '-'}}</td>

                <td>
                {{$video_details->title}}
                </td>

                <td>
              
                    @if($video_details->payment_status)

                        {{tr('payment')}}

                    @else

                        {{tr('free')}}

                    @endif
                </td>

                <td>

                 @if($video_details->is_streaming)

                    @if(!$video_details->status)

                        {{tr('video_call_started')}}

                    @else

                        {{tr('video_call_ended')}}

                    @endif

                @else

                    {{tr('video_call_initiated')}}

                @endif

                </td>
                <td>
                    {{$video_details->browser_name ? $video_details->browser_name : "-"}}
                </td>

                <td>
                 @if($video_details->type == TYPE_PUBLIC)

                   {{TYPE_PUBLIC}}

                @else

                    {{TYPE_PRIVATE}}

                @endif
                </td>

                <td>
                {{$video_details->description}}
                </td>

                <td>
                {{$video_details->viewer_cnt ? $video_details->viewer_cnt : "0"}}
                </td>

                <td>
                {{$video_details->start_time ? $video_details->start_time : "-"}}
                </td>

                <td>
                {{$video_details->end_time ? $video_details->end_time : "-"}}
                </td>

                <td>
                {{$video_details->no_of_minutes}}
                </td>

                <td>
                {{convertTimeToUSERzone($video_details->created_at,Auth::guard('admin')->user()->timezone)}}
                </td>

                <td>
                {{convertTimeToUSERzone($video_details->updated_at,Auth::guard('admin')->user()->timezone)}}
                </td>

                <td>
                {{Setting::get('currency')}} {{user_commission($video_details->id)}}
                </td>

                <td>
                {{Setting::get('currency')}} {{admin_commission($video_details->id)}}
                </td>

            </tr>

        @endforeach
    </table>

</body>

</html>