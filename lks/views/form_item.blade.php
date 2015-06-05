<div @if (isset($element['#id'])) id="{{$element['#id']}}" @endif 
     @if (isset($element['#class'])) class="{{$element['#class']}}" @endif>

	@if (!empty($element['#title']) && !in_array($element['#type'], ['radio', 'checkbox']))
	   <label class="form_item_label"> {{$element['#title']}}:
	       @if (!empty($element['#required'])) <span class="required">*</span> @endif
	   </label>
	@endif

	<div class="form_item_content">
		@if (!empty($element['#prefix'])) {!!$element['#prefix']!!} @endif

		{!!$element['#body']!!}
		
		@if (!empty($element['#title']) && in_array($element['#type'], ['radio', 'checkbox']))
		  <label class="form_item_sublabel"> {{$element['#title']}}:</label>
		@endif

		@if (!empty($element['#description']))
		  <div class="form_item_description help-block">{!!$element['#description']!!}</div>
		@endif
		
		@if (!empty($element['#error_message']))
		  <div class="form_error_messages alert alert-danger">{!!$element['#error_message']!!}</div>
		@endif
		
		@if (!empty($element['#suffix'])) {!!$element['#suffix']!!} @endif
	</div>
</div>