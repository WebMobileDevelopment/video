
<div class="row">

    <div class="col-md-12">
        
        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{ route('admin.sub_admins.index') }}" class="btn btn-default pull-right">{{ tr('sub_admin_view') }}</a>
            </div>

            <form class="form-horizontal" action="{{ route('admin.sub_admins.save') }}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <input type="hidden" name="timezone" value="{{ $sub_admin_details->timezone }}" id="userTimezone">

                    <input type="hidden" name="sub_admin_id" value="{{ $sub_admin_details->id }}">

                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label">* {{ tr('name') }}</label>

                        <div class="col-sm-10">
                            <input type="text" required name="name" title="{{ tr('only_alphanumeric') }}" class="form-control" id="username" placeholder="{{ tr('name') }}" value="{{ old('name') ?: $sub_admin_details->name }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">* {{ tr('email') }}</label>
                        <div class="col-sm-10">
                            <input type="email" maxlength="255" required class="form-control" id="email" name="email" placeholder="{{ tr('email') }}" value="{{ old('email') ?: $sub_admin_details->email  }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile" class="col-sm-2 control-label">{{ tr('mobile') }}</label>

                        <div class="col-sm-10">
                            <input type="text" name="mobile" class="form-control" id="mobile" placeholder="{{ tr('mobile') }}" minlength="4" maxlength="16"  value="{{ old('mobile') ?: $sub_admin_details->mobile }}">
                            <br>
                             <small style="color:brown">{{ tr('mobile_note') }}</small>
                        </div>
                    </div>

                    @if(!$sub_admin_details->id)

                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">* {{ tr('password') }}</label>

                        <div class="col-sm-10">
                            <input type="password" required  name="password"  title="{{ tr('password_notes') }}" class="form-control" id="password" placeholder="{{ tr('password') }}" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="col-sm-2  control-label">* {{ tr('password_confirmation') }}</label>

                        <div class="col-sm-10">
                            <input type="password" required title="{{ tr('password_notes') }}"  name="password_confirmation" class="form-control" id="password_confirmation" placeholder="{{ tr('password_confirmation') }}" value="">
                        </div>
                    </div>

                    @endif

                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">* {{ tr('description') }}</label>
                        <div class="col-sm-10">
                           <textarea class="form-control" name="description" placeholder="{{ tr('description') }}">{{ $sub_admin_details->description ? $sub_admin_details->description : old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                            <label for="picture" class="col-sm-2 control-label">{{ tr('picture') }}</label>
                            <div class="col-sm-10">
                                <input type="file" name="picture" id="picture" accept="image/jpeg,image/png">
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-xs-6 col-sm-6 col-lg-6">
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="users">{{tr('users')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="users">
                                        <input type="checkbox" id="users" class="option-input radio" name="users" value="1" {{ $sub_admin_details->users ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn hidden">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="sub_admins">{{tr('sub_admins')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="sub_admins">
                                        <input type="checkbox" id="sub_admins" class="option-input radio" name="sub_admins" value="1" {{ $sub_admin_details->sub_admins ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="channels">{{tr('channels')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="channels">
                                        <input type="checkbox" id="channels" class="option-input radio" name="channels" value="1" {{ $sub_admin_details->channels ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="categories">{{tr('categories')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="categories">
                                        <input type="checkbox" id="categories" class="option-input radio" name="categories" value="1" {{ $sub_admin_details->categories ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="tags">{{tr('tags')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="tags">
                                        <input type="checkbox" id="tags" class="option-input radio" name="tags" value="1" {{ $sub_admin_details->tags ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="videos">{{tr('videos')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="videos">
                                        <input type="checkbox" id="videos" class="option-input radio" name="videos" value="1" {{ $sub_admin_details->videos ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn hidden">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="custom_live_videos">{{tr('custom_live_videos')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="custom_live_videos">
                                        <input type="checkbox" id="custom_live_videos" class="option-input radio" name="custom_live_videos" value="1" {{ $sub_admin_details->custom_live_videos ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-6">
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="ads">{{tr('ads')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="ads">
                                        <input type="checkbox" id="ads" class="option-input radio" name="ads" value="1" {{ $sub_admin_details->ads ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="banner_ads_m">{{tr('banner_ads')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="banner_ads_m">
                                        <input type="checkbox" id="banner_ads_m" class="option-input radio" name="banner_ads_m" value="1" {{ $sub_admin_details->banner_ads_m ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="banner_videos">{{tr('banner_videos')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="banner_videos">
                                        <input type="checkbox" id="banner_videos" class="option-input radio" name="banner_videos" value="1" {{ $sub_admin_details->banner_videos ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="subscriptions">{{tr('subscriptions')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="subscriptions">
                                        <input type="checkbox" id="subscriptions" class="option-input radio" name="subscriptions" value="1" {{ $sub_admin_details->subscriptions ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="coupons">{{tr('coupons')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="coupons">
                                        <input type="checkbox" id="coupons" class="option-input radio" name="coupons" value="1" {{ $sub_admin_details->coupons ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group radio-btn">
                                <label class="control-label col-xs-6 col-sm-6 zero-padding" for="custom_push">{{tr('custom_push')}}</label>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="radio-inline width-100" for="custom_push">
                                        <input type="checkbox" id="custom_push" class="option-input radio" name="custom_push" value="1" {{ $sub_admin_details->custom_push ? 'checked': '' }}>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="reset" class="btn btn-danger">{{ tr('cancel') }}</button>
                        
                        <button type="submit" class="btn btn-success pull-right"  @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>                    
                    </div>
                
            </form>
        
        </div>

    </div>

</div>
