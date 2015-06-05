@if (count($images))
    @foreach ($images as $img)
        <div class="image_prefix_item">
            {{lks_style($img, $style)}}
            @if (isset($delete_link)) {{$delete_link}} @endif
        </div>
    @endforeach
@endif