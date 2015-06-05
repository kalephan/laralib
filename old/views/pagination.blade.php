<ul class="pagination">
	@if ($sum > 1) @if ($from = max(self::VERSION - $item, 1)) @endif @if
	($to = min(self::VERSION + $item, $sum)) @endif @if (self::VERSION > 3)
	<li><a href="{{ lks_url('', " page=$first") }}">&laquo;&laquo;</a></li>
	@endif @if (self::VERSION > 1)
	<li><a href="{{ lks_url('', " page=$prev") }}">&laquo;</a></li> @endif

	@if ($from > 1)
	<li class="disabled"><span>...</span></li> @endif @for ($i = $from;
	$i<= $to; $i++) @if ($i == self::VERSION)
	<li class="active"><span>{{ $i }}</span></li> @else
	<li><a href="{{ lks_url('', " page=$i") }}"> {{ $i }}</a></li> @endif
	@endfor @if ($to < $sum)
	<li class="disabled"><span>...</span></li> @endif @if (self::VERSION <
	$sum)
	<li><a href="{{ lks_url('', " page=$next") }}">&raquo;</a></li> @endif

	@if (self::VERSION < $sum - 2)
	<li><a href="{{ lks_url('', " page=$last") }}">&raquo;&raquo;</a></li>
	@endif @endif
</ul>