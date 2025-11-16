<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    /**
     * Phương thức private chung để hiển thị trang tĩnh.
     */
    private function showStaticPage(string $title, string $content): View
    {
        return view('pages.static-page', compact('title', 'content'));
    }

    // ==========================================
    // === NHÓM HỖ TRỢ KHÁCH HÀNG
    // ==========================================

    /**
     * Hiển thị trang Câu hỏi thường gặp (FAQ).
     */
    public function faq(): View
    {
        $title = 'Câu hỏi thường gặp (FAQ)';
        $content = '
            <div class="accordion" id="faqAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Sự khác biệt giữa hàng Chính hãng VNA và hàng Nhập khẩu/Xách tay?
                    </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li><strong>Hàng Chính hãng VNA:</strong> Là hàng được phân phối chính thức tại thị trường Việt Nam (ví dụ: iPhone VNA). Bạn được hưởng bảo hành tại tất cả trung tâm bảo hành ủy quyền của hãng trên toàn quốc.</li>
                            <li><strong>Hàng Nhập khẩu/Xách tay:</strong> Là hàng được nhập từ thị trường nước ngoài. Sản phẩm này thường có giá tốt hơn nhưng sẽ do <strong>DKDSHOP</strong> trực tiếp bảo hành theo chính sách của cửa hàng, thay vì bảo hành tại hãng.</li>
                        </ul>
                        <p>Chúng tôi cam kết cả hai loại hàng đều là hàng chính hãng (Apple, Samsung...) và sẽ ghi rõ "Loại hàng" trong mô tả sản phẩm.</p>
                    </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Chính sách "Bảo hành Vàng" (Bảo hành rơi vỡ, vào nước) là gì?
                    </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>Mặc định, các gói bảo hành tiêu chuẩn sẽ <strong>KHÔNG</strong> bảo hành cho các lỗi do người dùng (rơi vỡ, cấn móp, vào nước). </p>
                        <p>"Bảo hành Vàng" là một gói dịch vụ mở rộng (có tính phí) mà bạn có thể mua kèm theo máy. Khi có gói này, bạn sẽ được hỗ trợ sửa chữa, thay thế miễn phí hoặc trợ giá ngay cả khi máy gặp các lỗi trên. Vui lòng xem chi tiết tại trang sản phẩm hoặc liên hệ nhân viên tư vấn.</p>
                    </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Tôi có thể kiểm tra máy Likenew (99%) trước khi thanh toán không?
                    </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p><strong>Tuyệt đối có!</strong> Đối với các sản phẩm đã qua sử dụng (Likenew 99%), chúng tôi khuyến khích khách hàng nên kiểm tra kỹ ngoại hình, màn hình, và các chức năng cơ bản trước khi thanh toán.</p>
                        <p>Nếu bạn mua hàng Online, bạn được quyền yêu cầu bưu tá cho đồng kiểm (chỉ kiểm tra ngoại quan) trước khi nhận hàng và thanh toán COD.</p>
                    </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Làm thế nào để đặt hàng Online?
                    </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Chọn sản phẩm, phiên bản (màu sắc, dung lượng) và bấm "Thêm vào giỏ hàng".</li>
                            <li>Vào giỏ hàng, kiểm tra lại đơn hàng và bấm "Tiến hành thanh toán".</li>
                            <li>Điền thông tin giao hàng, chọn phương thức vận chuyển và thanh toán (COD hoặc Chuyển khoản).</li>
                            <li>Xác nhận đơn hàng. Chúng tôi sẽ gọi điện xác nhận lại trước khi gửi.</li>
                        </ol>
                    </div>
                    </div>
                </div>

            </div>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Hiển thị trang Chính sách bảo hành.
     */
    public function warrantyPolicy(): View
    {
        $title = 'Chính sách bảo hành';
        $content = '
            <p class="lead">An tâm của bạn là ưu tiên hàng đầu của DKDSHOP. Chúng tôi cam kết cung cấp chính sách bảo hành rõ ràng, minh bạch cho mọi sản phẩm bán ra.</p>
            
            <h3>1. Thời hạn bảo hành</h3>
            <ul>
                <li><strong>Điện thoại - Máy tính bảng (Hàng Mới - New Fullbox):</strong> Bảo hành 12 tháng (hoặc 24 tháng tùy hãng) theo chính sách của nhà sản xuất.</li>
                <li><strong>Điện thoại - Máy tính bảng (Hàng Likenew 99%):</strong> Bảo hành 6 tháng tại DKDSHOP (bao gồm cả nguồn và màn hình).</li>
                <li><strong>Phụ kiện (Cáp, Sạc, Tai nghe...):</strong> Bảo hành 1 đổi 1 trong 3-6 tháng (tùy sản phẩm) nếu có lỗi nhà sản xuất.</li>
            </ul>

            <h3>2. Điều kiện bảo hành hợp lệ</h3>
            <ul>
                <li>Sản phẩm còn trong thời hạn bảo hành.</li>
                <li>Lỗi phát sinh là lỗi kỹ thuật từ phía nhà sản xuất.</li>
                <li>Sản phẩm còn nguyên tem bảo hành của DKDSHOP (không rách, tẩy xóa).</li>
                <li>Số IMEI trên máy phải trùng khớp với phiếu mua hàng.</li>
            </ul>

            <h3>3. Các trường hợp từ chối bảo hành (Bảo hành tiêu chuẩn)</h3>
            <p>Chúng tôi sẽ từ chối bảo hành (hoặc chỉ nhận sửa chữa tính phí) nếu máy gặp các tình trạng sau:</p>
            <ul>
                <li>Máy bị <strong>rơi vỡ, cấn móp, cong vênh</strong> so với thiết kế ban đầu.</li>
                <li>Máy bị <strong>vào nước</strong> (kể cả các máy có chuẩn chống nước IP68). Giấy quỳ bên trong máy đổi màu.</li>
                <li>Máy có dấu hiệu bị can thiệp phần cứng, tự ý sửa chữa.</li>
                <li>Màn hình bị <strong>sọc, chảy mực, đốm đen</strong> (đây là lỗi do tác động vật lý hoặc vào nước).</li>
                <li>Máy bị <strong>treo logo, mất boot, mất IMEI</strong> do người dùng can thiệp phần mềm (Root, Jailbreak, unlock bootloader sai cách).</li>
                <li>Máy bị khóa bởi tài khoản cá nhân (iCloud, Google Account, Samsung Account).</li>
            </ul>
            <p><em>Để được bảo vệ toàn diện, Quý khách vui lòng tham khảo các Gói Bảo hành Vàng (Bảo hành mở rộng) của chúng tôi.</em></p>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Hiển thị trang Chính sách đổi trả.
     */
    public function returnPolicy(): View
    {
        $title = 'Chính sách đổi trả 1 Đổi 1';
        $content = '
            <p class="lead">DKDSHOP áp dụng chính sách đổi trả linh hoạt nhằm mang lại sự hài lòng tối đa cho khách hàng.</p>
            
            <h3>1. Đổi trả trong 7 ngày đầu (1 Đổi 1)</h3>
            <p>Áp dụng 1 Đổi 1 (đổi máy mới cùng loại) nếu sản phẩm phát sinh <strong>lỗi phần cứng do nhà sản xuất</strong>.</p>
            
            <h3>2. Điều kiện đổi trả</h3>
            <ul>
                <li>Sản phẩm còn trong thời hạn 7 ngày kể từ ngày mua.</li>
                <li>Sản phẩm không có dấu hiệu trầy xước, cấn móp, rơi vỡ, vào nước.</li>
                <li>Còn đầy đủ hộp, phụ kiện, sách hướng dẫn, quà tặng (nếu có).</li>
                <li>Tem bảo hành phải còn nguyên vẹn.</li>
            </ul>

            <h3 class_="text-danger">3. Điều kiện bắt buộc (Rất quan trọng)</h3>
            <ul>
                <li>Máy phải đã được <strong>đăng xuất khỏi tất cả các tài khoản cá nhân</strong> như: iCloud, Google Account, Samsung Account, Mi Account...</li>
                <li>DKDSHOP sẽ <strong>từ chối đổi trả</strong> nếu máy bị khóa tài khoản mà khách hàng không cung cấp được mật khẩu.</li>
            </ul>

            <h3>4. Các trường hợp không áp dụng đổi trả</h3>
            <ul>
                <li>Sản phẩm bị trầy xước, cấn móp do lỗi người dùng.</li>
                <li>Sản phẩm không còn đầy đủ phụ kiện, hộp.</li>
                <li>Sản phẩm đã bị can thiệp phần mềm (Root, Jailbreak).</li>
                <li>Quý khách không thích sản phẩm (đổi trả vì lý do cá nhân). Trong trường hợp này, DKDSHOP có thể hỗ trợ nhập lại máy với một khoản phí khấu hao (từ 10-20% giá trị máy).</li>
            </ul>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Trang Tra cứu đơn hàng
     */
  
    // ==========================================
    // === NHÓM THÔNG TIN & CHÍNH SÁCH
    // ==========================================

    /**
     * Hiển thị trang Giới thiệu.
     */
    public function about(): View
    {
        $title = 'Về DKDSHOP - Chuyên gia Điện thoại';
        $content = '
            <p class="lead">DKDSHOP được thành lập với một mục tiêu duy nhất: trở thành điểm đến tin cậy hàng đầu cho những người yêu công nghệ tại Việt Nam, chuyên cung cấp điện thoại thông minh, máy tính bảng, và phụ kiện công nghệ.</p>
            
            <h3>Sứ mệnh</h3>
            <p>Chúng tôi mang đến cho khách hàng những sản phẩm công nghệ (Điện thoại, Phụ kiện) chính hãng, chất lượng đảm bảo, với mức giá cạnh tranh nhất. Sứ mệnh của DKDSHOP là "Minh bạch về nguồn gốc - Tận tâm về bảo hành".</p>

            <h3>Triết lý kinh doanh</h3>
            <ul>
                <li><strong>Chất lượng là trên hết:</strong> Chúng tôi nói không với hàng giả, hàng nhái. Mọi sản phẩm (kể cả Likenew) đều được kiểm tra KCS nghiêm ngặt trước khi đến tay khách hàng.</li>
                <li><strong>Giá cả cạnh tranh:</strong> Tối ưu hóa quy trình vận hành để mang đến mức giá tốt nhất, giúp công nghệ dễ dàng tiếp cận hơn với mọi người.</li>
                <li><strong>Hậu mãi là cốt lõi:</strong> Chúng tôi hiểu rõ đặc thù của ngành hàng điện tử. Chính sách bảo hành và đổi trả rõ ràng, nhanh chóng chính là lời cam kết lớn nhất của chúng tôi.</li>
            </ul>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Hiển thị trang Tuyển dụng.
     */
    public function careers(): View
    {
        $title = 'Cơ hội nghề nghiệp tại DKDSHOP';
        $content = '
            <h3>Gia nhập đội ngũ đam mê công nghệ</h3>
            <p class="lead">Bạn có đam mê với những chiếc smartphone mới nhất? Bạn thích "vọc vạch" công nghệ? Hãy về với đội của DKDSHOP!</p>
            
            <h3>Các vị trí đang tuyển dụng</h3>
            <p>Chúng tôi luôn tìm kiếm những tài năng:</p>
            <ul>
                <li><strong>Chuyên viên Tư vấn Bán hàng (Sales):</strong> Am hiểu về các dòng điện thoại (iPhone, Samsung, Xiaomi...), kỹ năng giao tiếp tốt.</li>
                <li><strong>Kỹ thuật viên (Phần cứng/Phần mềm):</strong> Chuyên sửa chữa, ép kính, hoặc "chạy" phần mềm, unlock, xử lý các lỗi iOS/Android.</li>
                <li><strong>Chuyên viên Review/Content Creator:</strong> Người "thổi hồn" vào sản phẩm, quay video, viết bài đánh giá điện thoại.</li>
                <li><strong>Nhân viên Vận hành Sàn (E-commerce):</strong> Quản lý đơn hàng online, làm việc với các sàn TMĐT.</li>
            </ul>

            <h3>Cách thức ứng tuyển</h3>
            <p>Gửi CV của bạn kèm một thư ngỏ ngắn gọn về email: <a href="mailto:tuyendung@dkdshop.com"><strong>tuyendung@dkdshop.com</strong></a></p>
            <p>Tiêu đề email ghi rõ: <strong>[DKDSHOP] Ứng tuyển vị trí [Tên vị trí] - [Họ và tên]</strong></p>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Hiển thị trang Chính sách bảo mật.
     */
    public function privacyPolicy(): View
    {
        $title = 'Chính sách bảo mật thông tin';
        $content = '
            <p>DKDSHOP cam kết bảo vệ thông tin cá nhân của bạn. Vui lòng đọc kỹ chính sách dưới đây.</p>

            <h3>1. Mục đích thu thập thông tin</h3>
            <p>Chúng tôi thu thập thông tin để:</p>
            <ul>
                <li>Xử lý đơn hàng: tên, SĐT, địa chỉ.</li>
                <li>Hỗ trợ bảo hành: lưu trữ IMEI/Serial Number của máy, lịch sử mua hàng.</li>
                <li>Chăm sóc khách hàng và gửi thông báo khuyến mãi (nếu bạn đồng ý).</li>
            </ul>

            <h3>2. Phạm vi thu thập</h3>
            <p>Bao gồm: Họ tên, Số điện thoại, Địa chỉ Email, Địa chỉ giao hàng, Lịch sử mua hàng (bao gồm IMEI/Serial sản phẩm).</p>
            <p>Chúng tôi <strong>KHÔNG</strong> lưu trữ thông tin thẻ tín dụng của bạn. Việc thanh toán online được xử lý qua cổng thanh toán thứ ba được bảo mật.</p>

            <h3>3. Cam kết bảo mật</h3>
            <p>DKDSHOP cam kết không chia sẻ, bán, hoặc cho thuê thông tin cá nhân của bạn cho bất kỳ bên thứ ba nào, ngoại trừ:</p>
            <ul>
                <li>Cho đơn vị vận chuyển để giao hàng.</li>
                <li>Khi có yêu cầu của cơ quan pháp luật.</li>
            </ul>
        ';
        return $this->showStaticPage($title, $content);
    }

    /**
     * Hiển thị trang Điều khoản sử dụng.
     */
    public function termsOfService(): View
    {
        $title = 'Điều khoản sử dụng dịch vụ';
        $content = '
            <p class="lead">Bằng việc truy cập và mua hàng tại DKDSHOP, bạn đồng ý với các điều khoản và điều kiện dưới đây.</p>

            <h3>1. Trách nhiệm về tài khoản cá nhân (iCloud, Google)</h3>
            <ul>
                <li>Khách hàng có trách nhiệm tự bảo quản tài khoản cá nhân của mình.</li>
                <li>Khi mang máy đến bảo hành hoặc sửa chữa, Quý khách vui lòng đăng xuất khỏi tất cả tài khoản.</li>
                <li>DKDSHOP không chịu trách nhiệm về dữ liệu cá nhân của khách. Chúng tôi cũng <strong>từ chối</strong> tiếp nhận bảo hành/đổi trả nếu máy bị khóa tài khoản mà không có mật khẩu.</li>
            </ul>

            <h3>2. Quy định về tình trạng sản phẩm</h3>
            <ul>
                <li><strong>Hàng New Fullbox:</strong> Là hàng mới 100%, chưa kích hoạt (hoặc trôi bảo hành đối với một số mã), đầy đủ hộp và phụ kiện từ nhà sản xuất.</li>
                <li><strong>Hàng Likenew 99%:</strong> Là hàng đã qua sử dụng, ngoại hình đẹp gần như mới (có thể có vết xước dăm rất nhỏ, không cấn móp). Sản phẩm đã được kiểm tra toàn bộ chức năng (KCS) và đảm bảo pin còn trên 85% (đối với iPhone).</li>
            </ul>

            <h3>3. Đặt hàng và Hủy đơn</h3>
            <ul>
                <li>DKDSHOP có quyền gọi điện xác nhận trước khi gửi hàng.</li>
                <li>Chúng tôi có quyền từ chối đơn hàng nếu phát hiện dấu hiệu gian lận, hoặc sai sót nghiêm trọng về giá cả do lỗi hệ thống.</li>
                <li>Khách hàng vui lòng kiểm tra hàng (đồng kiểm) với bưu tá. Nếu sản phẩm móp méo, vỡ, sai mẫu mã, vui lòng từ chối nhận hàng và liên hệ ngay với Hotline.</li>
            </ul>
        ';
        return $this->showStaticPage($title, $content);
    }
}