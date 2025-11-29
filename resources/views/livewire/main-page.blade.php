<section class="container my-4"> {{-- Đổi container-fluid thành container để banner căn giữa đẹp hơn --}}
    
    <div class="hero-banner-wrapper">
        {{-- Thêm class 'carousel-fade' cho hiệu ứng mờ dần --}}
        <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            
            {{-- Indicators (Dấu chấm tròn dưới đáy) --}}
            @if (count($banners) > 1)
                <div class="carousel-indicators mb-3">
                    @foreach ($banners as $index => $file)
                        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @forelse ($banners as $index => $file)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        {{-- Wrapper cố định chiều cao --}}
                        <div class="banner-item-content">
                            <img src="{{ asset('storage/' . $file) }}" class="banner-img" alt="Banner {{ $index + 1 }}">
                            {{-- Lớp phủ gradient --}}
                            <div class="banner-overlay"></div>
                        </div>
                    </div>
                @empty
                    <div class="carousel-item active">
                        <div class="banner-item-content">
                            {{-- Ảnh placeholder cũng phải tuân thủ object-fit --}}
                            <img src="https://via.placeholder.com/1200x500?text=Chưa+có+banner" class="banner-img" alt="No Banner">
                        </div>
                    </div>
                @endforelse
            </div>

            @if (count($banners) > 1)
                <button class="carousel-control-prev custom-carousel-control" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-control-next custom-carousel-control" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @endif
        </div>
    </div>
</section>