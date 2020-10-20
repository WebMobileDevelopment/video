@extends('layouts.user')

@section('styles')

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/card.css')}}" />

@endsection

@section('content')

<div class="y-content">

    <div class="row y-content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10 profile-edit">
				
				<div class="profile-content profile-details">	

					<div class="row no-margin">

						<div class="col-lg-12">

                    		<h4 class="cards-head">{{tr('cards')}}</h4>

                    		@include('notification.notify')

                    		<div class="row">
                    			
                    			<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-1 col-lg-4">
                    				
                    				<div class="card-wrapper row">

				            			<div class="jp-card-container jp-card-container1">

					            			<div class="jp-card jp-card-visa jp-card-identified  top">

						            			<div class="jp-card-front">

							            			<div class="jp-card-logo jp-card-visa">

							            				{{tr('visa')}}

							            			</div>

						            				<div class="jp-card-lower">

						            					<div class="jp-card-shiny"></div>

							            				<div class="jp-card-cvc jp-card-display">•••</div>

							            				<div class="jp-card-number jp-card-display jp-card-invalid">XXXX XXXX XXXX XXXX</div>

							            				<div class="jp-card-name jp-card-display">{{Auth::user()->name}}</div>

							            				<div class="jp-card-expiry jp-card-display" data-before="month/year" data-after="validthru"><span id="jp-month">••</span>/<span id="jp-year">••</span></div>

													</div>

												</div>


											</div>

											<div class="jp-card jp-card-visa jp-card-identified jp-card-flipped col-lg-12 col-md-12 col-sm-12 col-xs-12 top">

												<div class="jp-card-back">

													<div class="jp-card-bar"></div>

													<div class="jp-card-cvc jp-card-display">•••</div>

													<div class="jp-card-shiny"></div>

												</div>

											</div>

										</div>

									</div>

                    			</div>
                    			
                    			<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-offset-1 col-md-5 col-lg-5">
                    				
                    				<form action="{{ route('user.card.add_card') }}" method="POST" id="payment-form" class="form-horizontal card">

								        <div class="row" id="card-payment">
								            <div>

								            	<input type="hidden" name="video_id" value="{{$video_id}}">

								            	<input type="hidden" name="subscription_id" value="{{$subscription_id}}">

								                <input id="id" name="id" type="hidden" required>

								                <div class="input-group-signup">
								                	<input type="text" name="card_name" placeholder="{{tr('card_name')}} (ex: visa)" class="form-control" value="{{old('card_name')}}" required onkeyup="$('.jp-card-name').html(this.value)">
								                </div>

								                <div class="input-group-signup">
								                    <input id="name" name="number" type="text" placeholder="{{tr('card_number')}}"  title="{{tr('card_number_notes')}}" class="form-control" required pattern="[0-9]{16,}" data-stripe="number" 
								                    onkeyup="card_number_onkey(this.value)"  maxlength="16">
								                </div>
								                <div class="input-group-signup ">
								                    <input id="email" name="cvv" type="text" placeholder="{{tr('cvv')}}" value ="{{old('cvv')}}"required="" class="form-control input-md" data-stripe="cvc" onkeyup="$('.jp-card-cvc').html(this.value)" maxlength="4" minlength="3" pattern="[0-9]{3,}">
								                </div>

								                <div class="input-group-signup">
								                    <input id="nationality" name="month" type="text" value="{{old('month')}}" required placeholder="{{tr('mm')}}" class="form-control" autocomplete="cc-exp" data-stripe="exp-month" onkeyup="$('#jp-month').html(this.value)" maxlength="2" pattern="[0-9]{2,}">
								                </div>

								                <div class="input-group-signup ">
								                    <input id="language" name="year" data-stripe="exp-year"
								                    autocomplete="cc-exp" value="{{old('year')}}" type="text" placeholder="{{tr('yy')}}" class="form-control" required onkeyup="$('#jp-year').html(this.value)" maxlength="2" pattern="[0-9]{2,}">
								                </div>

								                <div class="input-group-signup">

								                  <button class="btn btn-info" type="submit">{{tr('submit')}}</button>

								                </div>

								                <div class="clearfix"></div>

								                <div class="payment-errors text-danger col-lg-12"></div>

								                <br>

								            </div>
								        </div>

							        </form>
                    			
                    			</div>

                    		</div>
		            		
 							<p class="top1"></p>

					        <hr>

					        <div class="row">

						        @if(count($cards) > 0)

						           	<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">

						           		<h4 class="cards-head top">My Cards</h4>

						           		<p class="note-sec grey-clr"><small><b>Note : </b>{{tr('card_notes')}}</small></p>

						           		@foreach($cards as $card)

							           	@if(!$card->is_default)

						           		<div class="new-card">
							           		<div class="row">
							           			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
							           				<h4 class="new-card-name overflow">{{$card->card_name ? $card->card_name : tr('card_name')}}</h4>
							           			</div>
							           			<div class="col-xs-12 col-sm-7 col-md-4 col-lg-6">
							           				<h4 class="new-card-number overflow">PERSONAL*********{{$card->last_four}}</h4>
							           			</div>
							           			<!-- <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
							           				<h4 class="new-card-expiry">{{$card->month}} / {{$card->year}}</h4>
							           			</div> -->
							           			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">

							           				<form action="{{ route('user.card.default') }}" method="POST">
										                      <input type="hidden" name="_method" value="PATCH">
										                      <input type="hidden" name="card_id" value="{{ $card->id }}">

									           				<!-- <h4 class="new-card-close">
									           					<a href="#" type="submit" id="default-card"><span class="link-clr" title="{{tr('set_as_default')}}">{{tr('set_as_default')}}</span></a>
									           				</h4> -->

									           				<div class=" pull-right">
							           						<button type="submit" class="btn btn-link shadow-0" id="default-card" style="margin-right: 5px;"><i class="fa fa-check"  title="{{tr('set_as_default')}}"></i> {{tr('set_as_default')}}</button>

							           						<img src="{{asset('images/error.png')}}" class="default-card-img" onclick="$('#delete-card').click()" style="cursor: pointer;" title="{{tr('delete_card')}}">

							           					</div>
							           						<div class="clearfix"></div>
							           				</form>

						           					<form action="{{ route('user.card.delete') }}" method="POST" style="display: none">

									                    <input type="hidden" name="_method" value="DELETE">
									                    
									                    <input type="hidden" name="card_id" value="{{ $card->id }}">

									                    <button type="submit" class="text-white" id="delete-card"><i class="fa fa-times" title="{{tr('delete_card')}}"></i> {{tr('delete_card')}}</button>
									                </form>

							           			</div>
							           		</div>
						           		</div>

						           		@else

						           		<div class="new-card">
							           		<div class="row">
							           			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
							           				<h4 class="new-card-name overflow">{{$card->card_name ? $card->card_name : tr('card_name')}}</h4>
							           			</div>
							           			<div class="col-xs-12 col-sm-7 col-md-4 col-lg-6">
							           				<h4 class="new-card-number overflow">PERSONAL*********{{$card->last_four}}</h4>
							           			</div>
							           			<!-- <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
							           				<h4 class="new-card-expiry">{{$card->month}} / {{$card->year}}</h4>
							           			</div> -->
							           			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
							           				<div class="text-right">
							           					<img src="{{asset('images/success.png')}}" class="default-card-img" title="{{tr('default_card')}}">
							           				</div>
							           			</div>
							           		</div>
						           		</div>

						           		@endif

							           	@endforeach

							           	<?php /*<div class="row">

							           		@foreach($cards as $card)

							           		@if(!$card->is_default)

							           		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 top1">
							           			<div>
							           				<div class="card-title text-center">{{$card->card_name ? $card->card_name : tr('card_name')}}
							           					<img src="{{asset('images/success.png')}}" class="set-default" onclick="$('#default-card').click()" style="cursor: pointer;" title="{{tr('set_as_default')}}">

							           					<form action="{{ route('user.card.default') }}" method="POST" style="display: none">
										                      <input type="hidden" name="_method" value="PATCH">
										                      <input type="hidden" name="card_id" value="{{ $card->id }}">

										                      <button type="submit" class="text-white" id="default-card"><i class="fa fa-check"  title="{{tr('set_as_default')}}"></i> {{tr('set_as_default')}}</button>

										                </form>
									                   

							           					<img src="{{asset('images/error.png')}}" class="card-delete" onclick="$('#delete-card').click()" style="cursor: pointer;" title="{{tr('delete_card')}}">

							           					<form action="{{ route('user.card.delete') }}" method="POST" style="display: none">

										                    <input type="hidden" name="_method" value="DELETE">
										                    
										                    <input type="hidden" name="card_id" value="{{ $card->id }}">

										                    <button type="submit" class="text-white" id="delete-card"><i class="fa fa-times" title="{{tr('delete_card')}}"></i> {{tr('delete_card')}}</button>
										                </form>

							           				</div>
							           				<div class="card-details">
							           					
							           					<h5>PERSONAL*********{{$card->last_four}} <span class="pull-right">{{$card->month}} / {{$card->year}}</span></h5>
							           				</div>
							           			</div>
							           		</div>

							           		@else

							           		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 top1">
							           			<div>
							           				<div class="card-title text-center">{{$card->card_name ? $card->card_name : tr('card_name')}}</div>
							           				<div class="card-details">
							           					<h5>PERSONAL*********{{$card->last_four}} <span class="pull-right">{{$card->month}} / {{$card->year}}</span></h5>
							           				</div>
							           			</div>
							           		</div>
							           		@endif

							           		@endforeach

							           	</div> */?>
						           	</div>
						          	@else

						          	{{tr('no_card_details_found')}}


						        @endif

					        </div>
					        <br>
					        <br>
					        
					     </div>
				
					</div>
				
				</div>
				
			<div class="sidebar-back"></div> 
		</div>

	</div>

</div>

@endsection

@section('scripts')

<script type="text/javascript" src="{{ asset('assets/js/card.js') }}"></script>

<script>
    $('#card-payment form').card({ container: $('.card-wrapper')});

    function card_number_onkey(value) {


    	$('.jp-card-number').html(value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 '));
    	
    }
</script>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
    // This identifies your website in the createToken call below
    Stripe.setPublishableKey('{{ Setting::get("stripe_publishable_key", "pk_test_AHFoxSxndSb5RjlwHpfceeYa")}}');
    
    var stripeResponseHandler = function (status, response) {
        var $form = $('#payment-form');

        console.log(response);

        if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
            $form.find('button').prop('disabled', false);
            alert(response.error.message);

        } else {
            // token contains id, last4, and card type
            var token = response.id;
            
            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" id="stripeToken" name="stripeToken" />').val(token));
             // alert(token);
            // and re-submit

            jQuery($form.get(0)).submit();

        }
    
    };

    $('#payment-form').submit(function (e) {
        
        if ($('#stripeToken').length == 0)
        {
            var $form = $(this);
            // Disable the submit button to prevent repeated clicks
            $form.find('button').prop('disabled', true);
            console.log($form);
            Stripe.card.createToken($form, stripeResponseHandler);

            // Prevent the form from submitting with the default action
            return false;
        }
    
    });


</script>
@endsection