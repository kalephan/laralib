@foreach ($menu as $key => $value)
    <li @if (!empty($value['li_attributes'])) {{$value['li_attributes']}} @endif >
        @if (!empty($value['path']))
            <a href="{{lks_url($value['path'])}}" @if (!empty($value['anchor_attributes'])) {{$value['anchor_attributes']}} @endif >
        @endif

        @if (!empty($value['title']))
            {{lks_lang($value['title'])}}
        @endif

        @if (!empty($value['path']))
            </a>
        @endif

        @if (!empty($value['#children']))
            <ul class="dropdown-menu" role="menu">
                {{ lks_render('menu-onelevel', array('menu' => $value['#children'])) }}
            </ul>
        @endif
    </li>
@endforeach