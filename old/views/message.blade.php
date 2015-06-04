@if (count($message))
	@foreach($message as $key => $value)
		@if (count($value))
			@if ($key == 'error' && $key = 'danger') @endif

			<div class="alert alert-{{$key}}">
				{!!HTML::ul($value)!!}
			</div>
		@endif
	@endforeach
@endif