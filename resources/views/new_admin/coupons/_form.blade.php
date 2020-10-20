<form action="{{ Setting::get('admin_delete_control') == YES ? '#'  : route('admin.coupons.save')}}" method="POST" class="form-horizontal" role="form">

	<input type="hidden" name="coupon_id" value="{{$coupon_details->id}}">

	<div class="box-body">

		<div class="form-group">
			
			<div class="col-sm-6">

				<label for="title" class="control-label"> * {{tr('title')}}</label>

				<input type="text" name="title" role="title" min="5" max="20" class="form-control" placeholder="{{tr('enter_coupon_title')}}" value="{{old('title') ?: $coupon_details->title }}" required>
				
			</div> 

			<div class="col-sm-6">

				<label for="coupon_code" class="control-label"> * {{tr('coupon_code')}}</label>

				<input type="text" name="coupon_code" min="5" max="10" class="form-control" pattern="[A-Z0-9]+" placeholder="{{tr('enter_coupon_code')}}" value="{{ old('coupon_code') ?: $coupon_details->coupon_code }}" required><span class="help-block">{{tr('note')}} : {{tr('coupon_code_note')}}</span>
			</div>
		</div>
		
		<div class="form-group">
			
			<div class="col-sm-6">

				<label for = "amount_type" class="control-label"> * {{tr('amount_type')}}</label>

				<select id ="amount_type" name="amount_type" class="form-control select2" required>

					<option>{{tr('choose')}} {{tr('amount_type')}}</option>

					<option value="{{PERCENTAGE}}" {{ $coupon_details->amount_type == 0 ? 'selected="selected"' : '' }}> {{tr('percentage_amount')}}
					</option>

					<option value="{{ABSOULTE}}" {{$coupon_details->amount_type == 1 ? 'selected="selected"' : '' }}> {{tr('absoulte_amount')}} 
					</option>
				
				</select> 

			</div>

			<div class="col-sm-6">
				<label for="amount" class="control-label"> * {{tr('amount')}}</label>
				<input type="number" name="amount" min="1" max="5000" step="any" class="form-control" placeholder="{{tr('amount')}}" value="{{old('amount') ?: $coupon_details->amount }}" required>
			</div>			
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<label for="amount" class="control-label"> * {{ tr('per_users_limit') }}</label>
				<input type="text" name="per_users_limit" class="form-control" placeholder="{{tr('per_users_limit')}}" value="{{old('per_users_limit') ?: $coupon_details->per_users_limit}}" required title="{{tr('per_users_limit_notes')}}">
			</div>

			<div class="col-sm-6">
				<label for="no_of_users_limit" class="control-label"> * {{ tr('no_of_users_limit') }}</label>
				<input type="text" name="no_of_users_limit" class="form-control" placeholder="{{tr('no_of_users_limit')}}" value="{{old('no_of_users_limit') ?: $coupon_details->no_of_users_limit}}" required title="{{tr('no_of_users_limit_notes')}}">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<label for="expiry_date" class="control-label"> * {{ tr('expiry_date') }}</label>
				<input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="{{tr('expiry_date_coupon')}}" value="{{ old('expiry_date') ?: date('d-m-Y',strtotime($coupon_details->expiry_date)) }}" required>
			</div>

			<div class="col-sm-6">
				<label for="description" class="control-label">{{ tr('description') }} </label>
				
				<textarea name="description" class="form-control" max="255">{{ old('description') ?: $coupon_details->description}}</textarea>
			</div>
		</div>

	</div>

	<div class="box-footer">
        <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
        <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
    </div>
    
</form>