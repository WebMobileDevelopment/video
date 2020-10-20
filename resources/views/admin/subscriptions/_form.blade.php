@include('notification.notify')

<div class="row">

    <div class="col-md-10 ">

        <div class="box box-primary">

            <div class="box-header label-primary">

                <b>@yield('title')</b>

                <a href="{{route('admin.subscriptions.index')}}" style="float:right" class="btn btn-default">{{tr('view_subscriptions')}}</a>
            </div>

            <form class="form-horizontal" action="{{route('admin.subscriptions.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="id" value="{{$data->id}}">

                <input type="hidden" name="unique_id" value="{{$data->unique_id}}">

                <div class="box-body">

                    <div class="col-md-12">

                    <div class="form-group">
                        <label for="title" class="">{{tr('title')}}</label>

                        <input type="text" required name="title" class="form-control" id="title" value="{{$data->title ? $data->title : old('title')}}" placeholder="{{tr('title')}}">
                    </div>

                    <?php /*<div class="form-group">

                        <label for="image" class="">
                            {{tr('image')}} 
                            <br><span class="text-red"><b>{{tr('subscription_image_note')}}</b></span>
                        </label>

                        <input type="file" required name="image" class="form-control" id="image" value="{{old('image')}}" placeholder="{{tr('image')}}" accept="image/png, image/jpeg" onchange="loadFile(this, 'image_preview')">

                        <br>

                        <img id="image_preview" style="width:100px;height:100px;" src="{{$data->picture ? $data->picture : asset('images/default-ad.jpg')}}">
                    </div> */?>

                    <div class="form-group">
                    
                        <label for="plan" class="">{{tr('plan')}} <br><span class="text-red"><b>{{tr('plan_note')}}</b></span></label>

                            <input type="number" min="1" max="12" pattern="[0-9][0-2]{2}"  required name="plan" class="form-control" id="plan" value="{{($data->plan) ? $data->plan : old('plan')}}" title="{{tr('month_of_plans')}}" placeholder="{{tr('plans')}}">
                    </div>

                    <div class="form-group">
                        <label for="amount" class="">{{tr('amount')}}</label>

                        <!-- <div class="col-sm-10"> -->
                            <input type="text" required name="amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" step="any" value="{{($data->amount) ? $data->amount : old('amount')}}" pattern="[0-9]{1,5}" maxlength="5">
                        <!-- </div> -->
                    </div>

                    <div class="form-group">

                        <label for="description" class="">{{tr('description')}}</label>

                        <!-- <div class="col-sm-10"> -->

                            <textarea id="ckeditor" name="description" required class="form-control" placeholder="{{tr('description')}}">{{($data->description) ? $data->description : old('description')}}</textarea>

                        <!-- </div> -->
                        
                    </div>

                    </div>

                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('cancel')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>
            </form>
        
        </div>

    </div>

</div>
