{{-- We have 2 variable is $element & $entity --}}

<table class="table">
	@foreach($element as $key => $value)
	<tr class="entity_read_row entity_read_field_{{ $key }}">
		<td class="active entity_read_title">{{ $value['title'] }}</td>
		<td class="entity_read_value">@if(is_string($value['value'])) {{
			$value['value'] }} @endif</td>
	</tr>
	@endforeach
</table>