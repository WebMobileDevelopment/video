<!DOCTYPE html>
<html>

<head>
    <title>{{$title}}</title>
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

            <th>{{tr('id')}}</th>
            <th>{{tr('video')}}</th>
            <th>{{tr('username')}}</th>
            <th>{{tr('payment_id')}}</th>
            <th>{{tr('amount')}}</th>
            <th>{{tr('admin_live_commission')}}</th>
            <th>{{tr('user_live_commission')}}</th>
            <th>{{tr('paid_date')}}</th>
            <th>{{tr('status')}}</th>
            <th >{{tr('created')}}</th>
            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $payment_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>{{$payment_details->getVideoPayment ? $payment_details->getVideoPayment->title : ''}}</td>

                <td>{{$payment_details->user ? $payment_details->user->name : '-'}}</td>

                <td>{{$payment_details->payment_id}}</td>


                <td>{{Setting::get('currency')}} {{$payment_details->amount}}</td>

                <td>{{Setting::get('currency')}} {{$payment_details->admin_amount}}</td>

                <td>
                    {{Setting::get('currency')}} {{$payment_details->user_amount}}
                </td>

                <td> {{$payment_details->created_at->diffForHumans()}}
                </td>

                <td> 
                    @if($payment_details->status)
                        {{tr('paid')}}
                    @else
                        {{tr('not_paid')}}
                    @endif
                </td>


                <td>{{convertTimeToUSERzone($payment_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($payment_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>


            </tr>
        @endforeach
    </table>

</body>

</html>