<?php

namespace App\Livewire\Admin\Promotion;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BannerUpload extends Component
{
    use WithFileUploads;

    public $banner;
    public $banners = []; // Lưu danh sách banner hiện có

    public function mount()
    {
        $this->loadBanners();
    }

    // Hàm load toàn bộ banner trong thư mục Banner
    public function loadBanners()
    {
        $this->banners = Storage::disk('public')->files('Banner');
    }

    public function save()
    {
        $this->validate([
            'banner' => 'required|image|max:2048',
        ]);

        // ✅ Lưu file vào storage/app/public/Banner
        $this->banner->store('Banner', 'public');

        // ✅ Reload lại danh sách
        $this->loadBanners();

        // ✅ Reset form để tránh giữ lại preview cũ
        $this->reset('banner');

        // ✅ Hiển thị thông báo
        session()->flash('message', 'Upload banner thành công!');
    }
        public function deleteBanner($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            $this->loadBanners();
            session()->flash('message', 'Đã xóa banner thành công!');
        }
    }

    public function render()
    {
        return view('livewire.admin.promotion.banner-upload');
    }
}
