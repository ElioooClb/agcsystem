<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserAvatar extends Component
{
    public $user;
    public $size;

    public function __construct($user, $size = 'w-8 h-8')
    {
        $this->user = $user;
        $this->size = $size;
    }

    public function render()
    {
        return view('components.user-avatar');
    }
}
