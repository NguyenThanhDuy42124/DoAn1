@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            @if (session()->has('message'))
            <div class="alert alert-success shadow-sm">
                {{ session('message') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger shadow-sm">
                {{ session('error') }}
            </div>
            @endif

            <div class="card profile-card">
                <div class="card-body">
                    <form action="{{ route('general.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="avatar-preview-wrapper">

                            @if($user->img == "" || $user->img == null)
                            <img id="preview" src="{{ asset('storage/profile_images/default.jpg') }}" alt="Ảnh đại diện mặc định" class="avatar-preview">
                            @else
                            <img id="preview" src="{{ asset('storage/' . $user->img) }}" alt="Ảnh đại diện" class="avatar-preview">
                            @endif

                        </div>
                        <div class="form-group">
                            <label for="img">Thay đổi ảnh đại diện</label>
                            <input type="file" class="form-control-file" id="img" name="img" accept="image/*">
                            @error('img') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <hr class="my-4">

                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nhập họ và tên đầy đủ" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Nhập địa chỉ email" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phoneNumber">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="{{ old('phoneNumber', $user->phoneNumber) }}" placeholder="Nhập số điện thoại" required>
                                    @error('phoneNumber') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateOfBirth">Ngày sinh</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="{{ old('dateOfBirth', $user->dateOfBirth) }}" required>
                                    @error('dateOfBirth') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="address" class="m-0">
                                    @if(Auth::user()->role == 'buyer')
                                    Địa chỉ giao hàng
                                    @elseif (Auth::user()->role == 'seller')
                                    Địa chỉ shop
                                    @endif
                                </label>
                                <button type="button" class="btn btn-link btn-sm" id="use-current-location" style="text-decoration: none;">
                                    <i class="fas fa-map-marker-alt me-1"></i> Dùng vị trí đã lưu
                                </button>
                            </div>
                            @if(Auth::user()->role == 'buyer')
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Nhập địa chỉ giao hàng" required>
                            @elseif (Auth::user()->role == 'seller')
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Nhập địa chỉ shop" required>
                            @endif
                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <h5 class="mt-4">Xác thực EKYC</h5>
                        <hr>
                        <p>Vui lòng tải lên các tài liệu sau để xác thực danh tính của bạn:</p>

                        <div class="form-group">
                            <label for="cccd_front_image">Ảnh mặt trước CCCD</label>
                            <input type="file" class="form-control-file" id="cccd_front_image" name="cccd_front_image" accept="image/*">
                            <img id="cccd_front_preview" class="img-thumbnail mt-2" style="max-width:200px; display:none;">

                            <label for="cccd_back_image" class="mt-3">Ảnh mặt sau CCCD</label>
                            <input type="file" class="form-control-file" id="cccd_back_image" name="cccd_back_image" accept="image/*">
                            <img id="cccd_back_preview" class="img-thumbnail mt-2" style="max-width:200px; display:none;">

                            <label for="selfie_image" class="mt-3">Ảnh chân dung (selfie)</label>
                            <input type="file" class="form-control-file" id="selfie_image" name="selfie_image" accept="image/*">
                            <img id="selfie_preview" class="img-thumbnail mt-2" style="max-width:200px; display:none;">
                            @error('cccd_front_image') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('cccd_back_image') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('selfie_image') <span class="text-danger">{{ $message }}</span> @enderror

                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-update-profile">Lưu lại thông tin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    document.getElementById('cccd_front_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('cccd_front_preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

    document.getElementById('cccd_back_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('cccd_back_preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

    document.getElementById('selfie_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('selfie_preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

</script>

<script>
    // Script cho sidebar (giữ nguyên)
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
    });

    // ===========================================
    // === LOGIC VỊ TRÍ (ĐÃ CẬP NHẬT) ===
    // ===========================================
    $('#use-current-location').on('click', function() {

        // 1. Lấy cả hai vị trí
        const chosenLocation = localStorage.getItem('userChosenLocation'); // Ưu tiên 1
        const detectedLocation = sessionStorage.getItem('userDetectedLocation'); // Ưu tiên 2

        // 2. Xác định vị trí có thẩm quyền (ưu tiên vị trí đã CHỌN)
        const authoritativeLocation = chosenLocation || detectedLocation;

        if (authoritativeLocation) {
            const addressInput = $('#address');
            const currentAddress = addressInput.val();

            // 3. Kiểm tra (không phân biệt hoa thường) xem vị trí đã có trong địa chỉ chưa
            if (currentAddress && !currentAddress.toLowerCase().includes(authoritativeLocation.toLowerCase())) {
                // Nếu đã có địa chỉ, nối thêm vào
                addressInput.val(currentAddress + ', ' + authoritativeLocation);
            } else if (!currentAddress) {
                // Nếu ô trống, điền vị trí vào
                addressInput.val(authoritativeLocation);
            }
            // (Nếu vị trí đã tồn tại, không làm gì cả để tránh lặp lại)

        } else {
            // 4. Nếu không tìm thấy vị trí nào
            alert('Không tìm thấy vị trí đã lưu. Vui lòng quay lại trang chủ, chọn hoặc cho phép truy cập vị trí, sau đó thử lại.');
        }
    });

    // Script xem trước ảnh (giữ nguyên)
    const input = document.getElementById('img');
    const preview = document.getElementById('preview');

    input.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block'; // Đảm bảo ảnh hiển thị khi chọn
        }
    });

    // Script xem trước ảnh EKYC
    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    document.getElementById('cccd_front_image').addEventListener('change', function() {
        previewImage(this, 'cccd_front_preview');
    });
    document.getElementById('cccd_back_image').addEventListener('change', function() {
        previewImage(this, 'cccd_back_preview');
    });
    document.getElementById('selfie_image').addEventListener('change', function() {
        previewImage(this, 'selfie_preview');
    });

</script>
@endsection
