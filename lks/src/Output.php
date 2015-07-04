<?php

namespace Kalephan\LKS;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

class Output
{

    private $data = [
        'title' => [],
        'head' => [],
        'breadcrumb' => [],
        'closure' => [],
        'bodyclass'=> [],
    ];

    private $message;
    private $message_key = [
        'error',
        'warning',
        'info',
        'success',
        'primary'
    ];
    private $page = '';
    private $path = [];

    public function __construct()
    {
        $this->message = new MessageBag();
    }

    public function closure()
    {
        $closure = new \stdClass();
        $closure->closure = $this->data['closure'];
        event('lks.outputClosure', $closure);
        $this->data['closure'] = $closure->closure;

        return implode('', $this->data['closure']);
    }

    public function closureAdd($item, $key = null)
    {
        if (! is_string($item)) {
            throw new \Exception('Closure item must is string.');
        } else {
            $this->data['closure'][] = $item;
        }
    }

    public function head()
    {
        $head = new \stdClass();
        $head->head = $this->data['head'];
        event('lks.outputHead', $head);
        $this->data['head'] = $head->head;

        return implode('', $this->data['head']);
    }

    public function headAdd($item, $key = null)
    {
        if (! is_string($item)) {
            throw new \Exception('Head item must is string.');
        } else {
            $this->data['head'][$key] = $item;
        }
    }

    public function title()
    {
        $title = new \stdClass();
        $title->title = $this->data['title'];
        $title->title[] = config('lks.sitename');

        event('lks.outputTitle', $title);

        return implode('|', $title->title);
    }

    public function pagetitle()
    {
        $title = new \stdClass();
        $title->title = $this->data['title'];
        event('lks.outputPageTitle', $title);

        return view('pagetitle')->with('title', $title->title);
    }

    public function titleAdd($title)
    {
        if (! is_string($title)) {
            throw new \Exception('Title item must is string.');
        }

        array_unshift($this->data['title'], $title);
    }

    public function bodyclass()
    {
        $bodyclass = new \stdClass();
        $bodyclass->bodyclass = $this->data['bodyclass'];
        event('lks.outputBodyclass', $bodyclass);
        $this->data['bodyclass'] = $bodyclass->bodyclass;

        return implode(' ', $this->data['bodyclass']);
    }

    public function bodyclassAdd($bodyclass)
    {
        if (! is_string($bodyclass)) {
            throw new \Exception('Title item must is string.');
        }

        $this->data['bodyclass'][] = $bodyclass;
    }

    public function breadcrumb()
    {
        $this->data['breadcrumb'] = lks_array_merge_deep(['/' => lks_lang('Trang chủ')], $this->data['breadcrumb']);

        $head = new \stdClass();
        $head->breadcrumb = $this->data['breadcrumb'];
        event('lks.outputBreadcrumb', $head);
        $this->data['breadcrumb'] = $head->breadcrumb;

        return view('breadcrumb', ['breadcrumb' => $this->data['breadcrumb']]);
    }

    public function breadcrumbAdd($items)
    {
        if (!is_array($items)) {
            throw new \Exception('Breadcrumb item must is array.');
        }

        $this->data['breadcrumb'] = lks_array_merge_deep($this->data['breadcrumb'], $items);
    }

    public function message()
    {
        $message = [];
        foreach ($this->message_key as $key) {
            $message[$key] = $this->message->get($key);
        }

        return view('message')->with('message', $message);
    }

    public function messageAdd($message, $type = 'error')
    {
        $this->message->add($type, $message);
    }

    public function messageGet($type = 'error')
    {
        $this->message->get($type = 'error');
    }

    public function page($page = null)
    {
        $page = $this->page ? $this->page : $page;

        $page_modal = "$page-modal";
        if (Request::ajax() && View::exists($page_modal)) {
            $page = $page_modal;
        }

        return $page;
    }

    public function pageSet($page)
    {
        $this->page = $page;
    }

    public function path()
    {
        return $this->path;
    }

    public function pathAdd($path)
    {
        return $this->path[] = $path;
    }
}
