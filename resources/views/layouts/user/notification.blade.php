@if (session('response'))
<div data-abide-error="" class=" callout {{ session('response')->success ? 'success' : 'alert' }}">
	@if(session('response')->success)
		<i class="fa fa-fw fa-check"></i>&nbsp; <strong>{{tr('success')}}!</strong> 
	@else
		<i class="fa fa-fw fa-times"></i>&nbsp; <strong>{{tr('oh_snap')}}!</strong>
	@endif

	{{ session('response')->message }}
	
	<!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->

</div>
@endif