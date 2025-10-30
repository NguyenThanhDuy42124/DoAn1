<?php

namespace App\Livewire\Admin\Promotion;

use Livewire\Component;

class MainPagePromotionImage extends Component
{
    public $tab = 'banner'; // tab mặc định

    public function setTab($tab)
    {
        $this->tab = $tab;
    }
    public function render()
    {
        return view('livewire.admin.promotion.main-page-promotion-image')->layout('layouts.AdminDashboard');;
    }
}
