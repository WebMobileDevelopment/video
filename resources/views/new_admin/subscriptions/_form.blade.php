
<div class="row">

    <div class="col-md-12">
    
        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">

                <b>@yield('title')</b>

                <a href="{{ route('admin.subscriptions.index') }}" style="float:right" class="btn btn-default">{{ tr('view_subscriptions') }}</a>
            </div>

            <form class="form-horizontal" action="{{ Setting::get('admin_delete_control') == YES ? '#' :  route('admin.subscriptions.save') }}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="subscription_id" value="{{ $subscription_details->id }}">

                <input type="hidden" name="unique_id" value="{{ $subscription_details->unique_id }}">

                <div class="box-body">

                    <div class="form-group">

                        <div class="col-md-6">
                            <label for="title" class="">{{ tr('title') }} *</label>

                            <input type="text" required name="title" class="form-control" id="title" value="{{ old('title') ?: $subscription_details->title }}" placeholder="{{ tr('title') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="">{{ tr('amount') }} *</label>

                            <input type="number" required name="amount" class="form-control" id="amount" placeholder="{{ tr('amount') }}" step="any" value="{{ old('amount') ?: $subscription_details->amount }}"  maxlength="5">
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-md-6">                       
                            <label for="plan" class="">{{ tr('plan') }} * <br><span class="text-red">
                            <b>{{ tr('plan_note') }} (0 means Endless)</b></span>
                            </label>

                            <input type="number" min="1" max="12" required name="plan" class="form-control" id="plan" value="{{ old('plan') ?: $subscription_details->plan }}" title="{{ tr('month_of_plans') }}" placeholder="{{ tr('plans') }}">
                        </div>

                        <div class="col-md-6">                       
                            <label for="limit_data" class="">Limit of data in Mb<br><span class="">&nbsp</span></label>
                            <input type="number" min="1" max="100" required name="limit_data" class="form-control option-input col-xs-4 col-sm-2" id="limit_data" value="{{ old('limit_data') ?: $subscription_details->limit_data }}">
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="form-group">
                        <div class="col-xs-6 col-sm-6 col-lg-6">
                            <label class="control-label col-xs-6 col-sm-6 zero-padding" for="ppv_income">Managing their PPV for their income</label>
                            <div class="col-xs-6 col-sm-6">
                                <label class="radio-inline width-100" for="record_live">
                                    <input type="checkbox" id="ppv_income" class="option-input radio" name="ppv_income" value="1" {{ $subscription_details->ppv_income ? 'checked': '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-6">
                            <label class="control-label col-xs-6 col-sm-6 zero-padding" for="content_num">Content + 18</label>
                            <div class="col-xs-6 col-sm-6">
                                <label class="radio-inline width-100" for="content_num">
                                    <input type="checkbox" id="content_num" class="option-input radio" name="content_num" value="1" {{ $subscription_details->content_num ? 'checked': '' }}>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-6 col-sm-6 col-lg-6">
                            <label class="control-label col-xs-6 col-sm-6 zero-padding" for="ads_income">Managing their own ads for their income</label>
                            <div class="col-xs-6 col-sm-6">
                                <label class="radio-inline width-100" for="ads_income">
                                    <input type="checkbox" id="ads_income" class="option-input radio" name="ads_income" value="1" {{ $subscription_details->ads_income ? 'checked': '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6">
                            <label class="control-label col-xs-6 col-sm-6 zero-padding" for="ads_us">Ads from us</label>
                            <div class="col-xs-6 col-sm-6">
                                <label class="radio-inline width-100" for="ads_us">
                                    <input type="checkbox" id="ads_us" class="option-input radio" name="ads_us" value="1" {{ $subscription_details->ads_us ? 'checked': '' }}>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description" class="">{{ tr('description') }}</label>
                            <textarea id="ckeditor" name="description" required class="form-control" placeholder="{{ tr('description') }}">{{ old('description') ?: $subscription_details->description }}</textarea>
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
