<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class MainPage extends Component
{
    public $banners = [];

    public function mount()
    {
        // Lấy tất cả ảnh trong thư mục Banner
        $this->banners = Storage::disk('public')->files('Banner');
    }

    public function render()
    {
        return view('livewire.main-page', [
            'banners' => $this->banners,
        ]);
    }
}
