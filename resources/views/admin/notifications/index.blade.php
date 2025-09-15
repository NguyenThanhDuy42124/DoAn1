@if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Tiêu đề</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Nội dung</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-3">
                <label>Gửi đến</label>
                <select name="target" class="form-control">
                    <option value="all">Tất cả người dùng</option>
                    <option value="buyer">Người mua</option>
                    <option value="seller">Người bán</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Gửi</button>
        </form>