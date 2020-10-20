
<div class="row">

    <div class="col-md-12">
        
        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{ route('admin.users.index') }}" class="btn btn-default pull-right">{{ tr('view_users') }}</a>
            </div>

            <form class="form-horizontal" action="{{ Setting::get('admin_delete_control') == YES ? '#' : route('admin.users.save') }}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">
                
                    <input type="hidden" name="user_id" value="{{ $user_details->id }}">
                    
                    <input type="hidden" name="timezone" value="" id="userTimezone">

                    <div class="row">

                        <div class="col-lg-3 text-center">
                            
                            <input type="file" name="picture" id="picture" onchange="loadFile(this, 'picture_preview')" style="width: 200px;display: none" accept="image/jpeg, image/png" />

                            <img id="picture_preview" style="width: 150px;height: 150px;cursor: pointer;" src="{{ asset('placeholder.png') ?: $user_details->picture }}" onclick="return $('#picture').click()" />
                        </div>

                        <div class="col-lg-9">
                            
                            <div class="form-group">

                                <div class="col-lg-6">
                                    <label for="{{ tr('name') }}">{{ tr('name') }} *</label>
                                    <input type="text" name="name" value="{{  old('name') ?: $user_details->name }}" class="form-control" id="username" placeholder="{{ tr('name') }} *" title="{{ tr('username_notes') }}" required >
                                </div>

                                <div class="col-lg-6">
                                    <label for="{{ tr('email') }}">{{ tr('email') }} *</label>
                                    <input type="email" required class="form-control" value="{{ old('email') ?: $user_details->email }}" id="email" name="email" placeholder="{{ tr('email') }} *" maxlength="255" required >
                                </div>

                            </div>

                            <div class="form-group">

                                <div class="col-lg-6">
                                    <label for="{{ tr('dob') }}">{{ tr('enter_dob') }} *</label>
                                    <input type="text" name="dob" class="form-control" placeholder="{{ tr('enter_dob') }} *" id="dob" required autocomplete="off" value="{{ old('dob') ?: $user_details->dob }}" readonly required>
                                </div>

                                <div class="col-lg-6">
                                    <label for="{{ tr('mobile') }}">{{ tr('mobile') }}</label>
                                    <input type="text" name="mobile" class="form-control" id="mobile" placeholder="{{ tr('mobile') }}" minlength="6" maxlength="13" value="{{ old('mobile') ?: $user_details->mobile }}">

                                    <small style="color:brown">{{ tr('mobile_note') }}</small>
                                </div>

                            </div>

                            @if(!$user_details->id)

                                <div class="form-group">

                                    <div class="col-lg-6">
                                        <label for="exampleInputEmail1">{{ tr('password') }}*</label>
                                        <input type="password" required name="password" class="form-control" id="password" placeholder="{{ tr('password') }} *" minlength="6" title="{{tr('minimum_6_characters')}}" >
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="exampleInputEmail1">{{ tr('confirm_password') }} * </label>
                                        <input type="password" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{ tr('confirm_password') }} *" minlength="6" title="{{tr('minimum_6_characters')}}">
                                    </div>

                                </div>

                            @endif

                            <div class="form-group">

                                <div class="col-lg-6">
                                    <label for="{{ tr('paypal_email') }}">{{ tr('paypal_email') }} </label>
                                    <input type="text" name="paypal_email" class="form-control" placeholder="{{ tr('paypal_email') }} "  autocomplete="off" value="{{ old('paypal_email') ?: $user_details->paypal_email }}">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <br>

                    <div class="form-group">

                        <div class="col-lg-12">
                            <label for="{{ tr('dob') }}">{{ tr('description') }} </label>
                         
                            <textarea type="text" name="description" class="form-control" id="description" placeholder="{{ tr('description') }}" maxlength="255">
                            <?php echo $user_details->description ?></textarea>
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
