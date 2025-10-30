<section class="container-fluid px-0">
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @forelse ($banners as $index => $file)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $file) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                </div>
            @empty
                <div class="carousel-item active">
                    <img src="https://via.placeholder.com/1036x450?text=Chưa+có+banner" class="d-block w-100" alt="No Banner">
                </div>
            @endforelse
        </div>

        @if (count($banners) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" style="width: 20px; height: 20px;"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" style="width: 20px; height: 20px;"></span>
            </button>
        @endif
    </div>
</section>
