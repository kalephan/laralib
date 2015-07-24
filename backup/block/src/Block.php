<?php

namespace Kalephan\Block;

class Block
{

    public function template($template, $data = [])
    {
        return view($template, $data);
    }
}
