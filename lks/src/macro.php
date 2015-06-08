<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Html\HtmlFacade as HTML;

/*
 We will create a navigation menu like this:
 {{ HTML::nav(array('login', 'register', 'wiki', 'forum', '/' => 'Home')) }}

 Which will output:
 <ul>
 <li><a href="yourdomain.com/login" class="active">Login</a></li>
 <li><a href="yourdomain.com/register">Register</a></li>
 <li><a href="yourdomain.com/wiki">Wiki</a></li>
 <li><a href="yourdomain.com/forum">Forum</a></li>
 <li><a href="yourdomain.com">Home</a></li>
 </ul>

 Just make sure to load your macro.
 */

// HTMLMacro.php
HTML::macro('nav', function($list, $attributes = null)
{
    $nav = [];
     
    // iterate through each element of the array and get url => link pairs
    foreach ($list as $key => $val)
    {
        // Sometimes we want to pass a condition and display link based on the condition
        // (ex: dispaly login vs. logout link based on if user is logged in),
        // in this case, an array will be passed instead of a string.
        // The first value will be the condition, 2nd and 3rd will be the links
        if (is_array($val))
        {
            $condition = isset($val['condition']) ? $val['condition'] : true;
            $link = [
                'true' => isset($val['url']) ? $val['url'] : '',
                'false' => isset($val['url_false']) ? $val['url_false'] : '',
            ];
             
            // check to see if condition passes
            $key = $condition ? $link['true'] : $link['false'];
             
            // if a second link isn't passed, then stop the current loop at this point
            // and skip to the next loop
            /* if (is_null($key))
            {
                continue;
            } */
             
            $val = isset($val['title']) ? $val['title'] : $key;
        }
         
        // Check to see if both url and link is passed
        // Many times, both url and link name will be the same, so we can avoid typing it twice
        // and just pass one value
        // In this case, the key will be numeric (when we just pass a value instead of key => value pairs)
        // We will have to set the url to equal the key instead
        switch ($url) {
            case '<none>':
                $url = 'abc';
                break;
                
            case '<front>':
                $url = '/';
                break;
                
            default:
                $url = is_numeric($key) ? $val : $key;
        }
         
        // If we are using controller routing (ex: HomeController@getIndex),
        // then we need to use URL::action() instead of URL::to()
        $url = (strpos($url, '@') !== false) ? URL::action($url) : URL::to(strtolower($url));
         
        // Set the active state automatically
        $class['class'] = (Request::url() === $url) ? 'active' : null;
         
        // Push the new list into the $nav array
        array_push($nav, HTML::link($url, $val, $class));
    }
     
    // Generate the unordered list
    $menu = HTML::ul($nav, $attributes);
    // HTML::ul() performs htmlentities on the list by default,
    // so we have to decode it back using HTML::decode()
    $menu = HTML::decode($menu);
     
    // Return the generated menu
    return $menu;
});

// Example.php

// Example 1
//{{ HTML::nav(['login', 'register', 'wiki', 'forum', '/' => 'Home']) }}

// Example 2: What if you want to display a link based on a condition like Auth::check()
//{{ HTML::nav([[Auth::check(), 'logout', 'login'], 'register', 'wiki', 'forum', '/' => 'Home']) }}
// Now the login link will be displayed if user is not logged in and vice versa