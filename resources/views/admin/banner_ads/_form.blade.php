
<div class="row">

    <div class="col-md-12">
        
        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b>{{tr('create_banner_ad')}}</b>
                <a href="{{route('admin.banner-ads.list')}}" style="float:right" class="btn btn-default">{{tr('banner_ads')}}</a>
            </div>

            <form  action="{{Setting::get('admin_delete_control') ? '' : route('admin.banner-ads.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">
                    <input type="hidden" name="id" value="{{$model->id}}">

                    <input type="hidden" name="position" value="{{$model->position}}">

                    <div class="form-group">
                        <label for="title">{{tr('title')}}</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="{{tr('enter_title')}}" value="{{$model->title}}">
                    </div>

                    <div class="form-group">
                        <label for="heading">{{tr('image')}}</label>
                        <input type="file" name="file" id="file" accept="image/jpeg,image/png" @if(!$model->id) required @endif>

                        <p><small>{{tr('banner_notes')}}</small></p>
                    </div>

                    <div class="form-group">
                        <label for="url">{{tr('link')}}</label>
                        <input type="url" class="form-control" name="link" id="link" placeholder="{{tr('enter_link')}}" value="{{$model->link}}" required>
                    </div>

                    <div class="form-group">
                        
                        <label for="description">{{tr('description')}}</label>

                        <textarea id="ckeditor" name="description" class="form-control" placeholder="{{tr('enter_text')}}">{{$model->description}}</textarea>
                        
                    </div>

                </div>

              <div class="box-footer">
                    <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button> 
                    
                    @if(Setting::get('admin_delete_control'))
                        <button type="button" class="btn btn-success pull-right" disabled>{{tr('submit')}}</button>
                    @else

                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>

                    @endif
              </div>

            </form>
        
        </div>

    </div>

</div>
   
@section('scripts')
    <script src="http://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor' );
    </script>
@endsection
