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

            <th>{{tr('s_no')}}</th>

            <th>{{tr('channel_name')}}</th>

            <th >{{tr('username')}}</th>

            <th >{{tr('picture')}}</th>

            <th >{{tr('cover')}}</th>

            <th >{{tr('status')}}</th>

            <th >{{tr('subscribers')}}</th>

            <th >{{tr('no_of_videos')}}</th>

            <th >{{tr('amount')}}</th>

            <th >{{tr('description')}}</th>

            <th >{{tr('created_at')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $channel_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>
                
                <td>{{$channel_details->name}}</td>

                <td>{{$channel_details->getUser ? $channel_details->getUser->name : ''}}</td>

                <td>{{$channel_details->picture}}</td>

                <td>{{$channel_details->cover}}</td>

                <td> 
                    @if($channel_details->status)
                        {{tr('approved')}}
                    @else  
                        {{tr('pending')}}
                    @endif
                </td>

                <td>{{$channel_details->getChannelSubscribers()->count()}}</td>

                <td >{{$channel_details->getVideoTape->count()}} </td>

                <td>{{Setting::get('currency')}} {{getAmountBasedChannel($channel_details->id)}}</td>

                <td><?php echo $channel_details->description ?></td>

                <td>{{convertTimeToUSERzone($channel_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($channel_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>


            </tr>
        @endforeach
    </table>

</body>

</html>