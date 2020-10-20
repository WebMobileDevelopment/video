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

            <th >{{tr('username')}}</th>

            <th>{{tr('email')}}</th>

            <th>{{tr('no_of_channels')}}</th>

            <th>{{tr('no_of_videos')}}</th>

            <th>{{tr('mobile')}}</th>

            <th >{{tr('dob')}}</th>

            <th >{{tr('age_limit')}}</th>

            <th >{{tr('user_type')}}</th>

            <th >{{tr('picture')}}</th>

            <th >{{tr('status')}}</th>
            @if(Setting::get('email_verify_control'))
            <th >{{tr('email_verification')}}</th>
            @endif
            <th >{{tr('validity_days')}}</th>

            <th >{{tr('redeems')}}</th>

            <th >{{tr('payment_mode')}}</th>

            <th >{{tr('total_earning')}}</th>

            <th >{{tr('wallet_balance')}}</th>

            <th >{{tr('paid_amount')}}</th>

            <th >{{tr('description')}}</th>

            <th >{{tr('push_status')}}</th>

            <th >{{tr('device_type')}}</th>

            <th >{{tr('login_by')}}</th>

            <th >{{tr('timezone')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $user_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>{{$user_details->name}}</td>

                <td>{{$user_details->email}}</td>

                <td>{{$user_details->getChannel ? $user_details->getChannel->count() :"0"}}</td>

                <td>{{$user_details->getChannelVideos ? $user_details->getChannelVideos->count() : "0"}}</td>

                <td>{{$user_details->mobile}}</td>

                <td>{{$user_details->dob}}</td>

                <td>{{$user_details->age_limit}}</td>

                <td>
                    @if($user_details->user_type)
                       <span class="text-green"> {{tr('paid_user')}} </span>
                    @else
                       <span class="text-red"> {{tr('normal_user')}} </span>
                    @endif

                </td>

                <td> {{$user_details->picture}} </td>

                <td> 
                    @if($user_details->status)
                        {{tr('approved')}}
                    @else  
                        {{tr('pending')}}
                    @endif
                </td>

                @if(Setting::get('email_verify_control'))
                    <td>
                        @if($user_details->is_verified)
                            {{tr('verified')}}
                        @else                      
                            {{tr('verify')}}
                        @endif
                    </td>
                @endif

                <td>
                    @if($user_details->user_type)
                        {{get_expiry_days($user_details->id)['days']}} days
                    @endif
                </td>

                <td>
                    {{Setting::get('currency')}} {{$user_details->userRedeem ? $user_details->userRedeem->remaining : 0}}
                </td>


                <td>
                    {{$user_details->payment_mode}}
                </td>

                <td>
                    {{Setting::get('currency')}} {{$user_details->userRedeem ? $user_details->userRedeem->total : "0.00"}}
                </td>

                <td>
                    {{Setting::get('currency')}} {{$user_details->userRedeem ? $user_details->userRedeem->remaining : "0.00"}}
                </td>

                <td>
                    {{Setting::get('currency')}} {{$user_details->userRedeem ? $user_details->userRedeem->paid : "0.00"}}
                </td>

                <td>
                   <?php echo $user_details->description ?>
                </td>

                 <td>
                    @if($user_details->push_status)

                        {{tr('enabled')}}
                    @else
                        {{tr('disabled')}}
                    @endif
                </td>

                <td>{{$user_details->device_type}}</td>

                <td>{{$user_details->login_by}}</td>

                <td>{{$user_details->timezone}}</td>

                <td>{{convertTimeToUSERzone($user_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($user_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            </tr>
        @endforeach
    </table>

</body>

</html>