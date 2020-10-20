<div class="row">

    <div class="col-md-12">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b>@yield('title')</b>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-default pull-right">{{ tr('categories') }}</a>
            </div>

            <form action="{{  Setting::get('admin_delete_control') == YES ? '#' : route('admin.categories.save') }}" method="post" enctype="multipart/form-data">

                <div class="box-body">

                    <input type="hidden" name="category_id" value="{{ $category_details->id }}">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="title" class="">{{ tr('name') }}*</label>

                            <input type="text" name="name" value="{{ old('name') ?: $category_details->name }}" maxlength="64" required class="form-control" placeholder="{{ tr('name') }}">

                        </div>

                        <div class="form-group">

                            <label for="image" class="">{{ tr('image') }}*</label>

                            <input type="file" name="image" id="image" onchange="loadFile(this, 'picture_preview')" style="width: 200px;" accept="image/png, image/jpeg" @if(!$category_details->id) required @endif/>
                            <p class="help-block">{{ tr('image_square') }}. {{ tr('upload_message') }}</p>

                            <center><img src="{{ $category_details->image ? $category_details->image : asset('images/default-ad.jpg') }}" id="picture_preview" style="width: auto;height: 200px;" /></center>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="description">{{ tr('description') }}*</label>

                            <textarea name="description" class="form-control" required id="description" required>{{ old('description') ?: $category_details->description }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
                    <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
                </div>

            </form>

        </div>

    </div>

</div>

@section('scripts')
<script type="text/javascript">
    function loadFile(event, id) {
        // alert(event.files[0]);
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(id);
            // alert(output);
            output.src = reader.result;
            //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
        };
        reader.readAsDataURL(event.files[0]);
    }
</script>

<script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description');
</script>
@endsection