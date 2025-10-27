<?php

namespace App\Livewire;

use Livewire\Component;

class TestBinding extends Component
{
    public $message = 'Hello World'; // Giá trị ban đầu

    public function render()
    {
        return view('livewire.test-binding')->layout('layouts.SellerDashBoard');
    }
}