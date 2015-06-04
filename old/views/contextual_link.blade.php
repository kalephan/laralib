@if (count($contextual_link))
    <ul id="contextual_link" class="nav nav-tabs">
        @foreach ($contextual_link as $key => $value)
            <li class="contextual_link_item contextual_link_{{$key}}">{{$value}}</li>
        @endforeach
    </ul>
@endif