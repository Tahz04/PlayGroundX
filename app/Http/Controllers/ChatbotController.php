<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $action = $request->input('action', 'hello');

        $response = [
            'messages' => [],
            'options' => []
        ];

        switch ($action) {
            case 'hello':
                $response['messages'][] = 'Chào bạn! 👋 Mình là trợ lý ảo của PlayGroundX.';
                $response['messages'][] = 'Mình có thể giúp gì cho bạn hôm nay?';
                $response['options'] = [
                    ['action' => 'price', 'text' => '💰 Xem bảng giá sân'],
                    ['action' => 'booking_guide', 'text' => '📝 Hướng dẫn đặt sân'],
                    ['action' => 'payment', 'text' => '💳 Hỗ trợ thanh toán'],
                    ['action' => 'report_error', 'text' => '⚠️ Báo lỗi / Sự cố hệ thống'],
                    ['action' => 'contact_agent', 'text' => '📞 Gặp nhân viên hỗ trợ'],
                ];
                break;

            case 'price':
                $response['messages'][] = 'Giá thuê sân tại PlayGroundX thường dao động từ <b>200,000đ đến 500,000đ/giờ</b> tùy thuộc vào:';
                $response['messages'][] = '- Loại sân (Sân 5, Sân 7, Sân 11)<br>- Khung giờ (Giờ Vàng từ 17:30 - 20:30 thường cao hơn)<br>- Vị trí sân';
                $response['options'] = [
                    ['action' => 'find_arena', 'text' => '🔍 Đi tới trang Tìm Sân', 'url' => route('arenas.index')],
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            case 'booking_guide':
                $response['messages'][] = 'Việc đặt sân rất đơn giản, bạn chỉ cần làm theo 3 bước sau:';
                $response['messages'][] = '<b>Bước 1:</b> Truy cập trang "Lịch Trống" hoặc "Bản Đồ".<br><b>Bước 2:</b> Chọn sân và khung giờ bạn muốn chơi.<br><b>Bước 3:</b> Xác nhận thông tin và chọn phương thức thanh toán.';
                $response['options'] = [
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            case 'payment':
                $response['messages'][] = 'Hệ thống hỗ trợ 2 phương thức thanh toán:';
                $response['messages'][] = '1. <b>Thanh toán chuyển khoản</b>: Quét mã VietQR tiện lợi, tự động điền nội dung.<br>2. <b>Thanh toán tiền mặt</b>: Thanh toán trực tiếp tại sân trước giờ thi đấu.';
                $response['messages'][] = '<i>Lưu ý: Bạn sẽ được giảm thêm 10% nếu đặt từ 3 giờ trở lên!</i>';
                $response['options'] = [
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            // --- CÁC KỊCH BẢN LỖI ---
            case 'report_error':
                $response['messages'][] = 'Rất xin lỗi bạn vì sự bất tiện này. Bạn đang gặp phải sự cố nào dưới đây?';
                $response['options'] = [
                    ['action' => 'error_conflict', 'text' => '❌ Báo lỗi: Đã có người đặt (Trùng giờ)'],
                    ['action' => 'error_qr', 'text' => '📱 Báo lỗi: Không hiện mã QR thanh toán'],
                    ['action' => 'error_cancel', 'text' => '🛑 Lỗi: Không thể hủy đơn đặt sân'],
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            case 'error_conflict':
                $response['messages'][] = '<b>Sự cố báo trùng giờ:</b> Tình trạng này xảy ra khi có một khách hàng khác vừa đặt xong đúng khung giờ đó trước bạn chỉ vài giây.';
                $response['messages'][] = '<b>Cách xử lý:</b> Bạn vui lòng làm mới (F5) lại trang Lịch Trống để cập nhật khung giờ mới nhất và chọn một giờ khác nhé.';
                $response['options'] = [
                    ['action' => 'report_error', 'text' => '⬅️ Trở về danh sách lỗi'],
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            case 'error_qr':
                $response['messages'][] = '<b>Lỗi không hiện mã QR:</b> Do kết nối mạng đến ngân hàng tạo mã bị chậm.';
                $response['messages'][] = '<b>Cách xử lý:</b> Bạn có thể vào phần <b>Tài khoản > Lịch đặt sân</b>, bấm vào "Thanh toán ngay" để hệ thống tạo lại mã QR mới cho bạn.';
                $response['options'] = [
                    ['action' => 'report_error', 'text' => '⬅️ Trở về danh sách lỗi'],
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            case 'error_cancel':
                $response['messages'][] = '<b>Lỗi không thể hủy đơn:</b> Theo quy định, bạn chỉ có thể tự hủy đơn khi trạng thái đang là "Chờ xác nhận" (Pending).';
                $response['messages'][] = 'Nếu đơn đã được admin xác nhận hoặc đã thanh toán, nút Hủy sẽ bị khóa. Để hủy, bạn bắt buộc phải gọi điện trực tiếp cho admin / chủ sân để được hỗ trợ.';
                $response['options'] = [
                    ['action' => 'contact_agent', 'text' => '📞 Gọi admin ngay'],
                    ['action' => 'report_error', 'text' => '⬅️ Trở về danh sách lỗi'],
                ];
                break;

            case 'contact_agent':
                $response['messages'][] = 'Để được hỗ trợ trực tiếp, bạn vui lòng liên hệ:';
                $response['messages'][] = '📞 Hotline: <b>0986049032</b><br>📧 Email: <b>duythanhhang@playgroundx.vn</b><br>⏰ Giờ làm việc: 06:00 - 23:00 hàng ngày.';
                $response['options'] = [
                    ['action' => 'hello', 'text' => '🔙 Quay lại Menu chính'],
                ];
                break;

            default:
                $response['messages'][] = 'Xin lỗi, mình chưa hiểu ý bạn. Vui lòng chọn một trong các tùy chọn bên dưới nhé!';
                $response['options'] = [
                    ['action' => 'hello', 'text' => '🔙 Về Menu chính'],
                ];
                break;
        }

        return response()->json($response);
    }
}
