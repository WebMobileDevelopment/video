<form  action="{{ Setting::get('admin_delete_control') == YES ? '#' : route('admin.pages.save') }}" method="POST" enctype="multipart/form-data" role="form">

    <div class="box-body">

        <input type="hidden" name="page_id" value="{{ $page_details->id }}">
   
        @if($page_details->id != '')

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="type">*{{ tr('page_type') }}</label>
                    <input type="text" class="form-control" name="type" id="title" value="{{  $page_details->type }}" placeholder="{{ tr('enter_type') }}" disabled="true">
                </div>
            </div>

        @else

            <div class="col-sm-6">                 
                <div class="form-group">

                    <label for="select2">*{{tr('page_type')}}</label>
                    <select id="select2" name="type" class="form-control" required>
                        <option value="" selected="true">{{tr('choose')}} {{tr('page_type')}}</option>
                        <option value="about">{{tr('about')}}</option>
                        <option value="terms">{{tr('terms')}}</option>
                        <option value="privacy">{{tr('privacy')}}</option>
                        <option value="contact">{{tr('contact')}}</option>
                        <option value="help">{{tr('help')}}</option>
                        <option value="others">{{tr('others')}}</option>
                    </select>            
                </div>
            </div>
        @endif                
        <div class="col-sm-6">
            <div class="form-group">
                <label for="title">*{{ tr('heading') }}</label>
                <input type="text" class="form-control" name="title" required value="{{  old('title') ?: $page_details->title }}" id="title" placeholder="{{ tr('enter_heading') }}">
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-group">
            <div class="col-sm-12">
                <label for="description">*{{ tr('description') }}</label>

                <textarea id="ckeditor" name="description" class="form-control" required placeholder="{{ tr('enter_text') }}" required>{{ old('description') ?: $page_details->description }}</textarea>
                
            </div>
        </div>

    </div>

    <div class="box-footer">
            <button type="reset" class="btn btn-danger">{{ tr('cancel') }}</button>
            
            <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif) >{{ tr('submit') }}</button> 
    </div>

</form>