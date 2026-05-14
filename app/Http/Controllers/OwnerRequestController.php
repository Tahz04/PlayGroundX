<?php

namespace App\Http\Controllers;

use App\Models\OwnerRequest;
use App\Models\User;
use App\Notifications\OwnerRequestNotification;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OwnerRequestController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $ownerRequests = $user->ownerRequests()->latest()->get();
        $canRequestOwner = !$user->isOwner()
            && $ownerRequests->where('status', 'pending')->isEmpty();

        return view('profile', compact('user', 'ownerRequests', 'canRequestOwner'));
    }

    public function requestOwner(Request $request)
    {
        $user = Auth::user();

        if ($user->role && $user->role->name !== 'customer') {
            return back()->with('error', 'Chỉ khách hàng mới có thể gửi yêu cầu trở thành chủ sân.');
        }

        if ($user->ownerRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Bạn đã có yêu cầu đang chờ xử lý.');
        }

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
            'image_1' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'image_2' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'image_1.required' => 'Vui lòng tải lên ảnh giấy tờ mặt trước.',
            'image_1.mimes'    => 'Ảnh mặt trước phải là file JPG, PNG hoặc PDF.',
            'image_1.max'      => 'Ảnh mặt trước không được vượt quá 2MB.',
            'image_2.required' => 'Vui lòng tải lên ảnh giấy tờ mặt sau.',
            'image_2.mimes'    => 'Ảnh mặt sau phải là file JPG, PNG hoặc PDF.',
            'image_2.max'      => 'Ảnh mặt sau không được vượt quá 2MB.',
        ]);

        $paths = [];
        foreach (['image_1', 'image_2'] as $field) {
            $file     = $request->file($field);
            $filename = time() . '_' . $field . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());
            $paths[$field] = $file->storeAs('owner_requests', $filename, 'public');
        }

        $ownerRequest = $user->ownerRequests()->create([
            'status'  => 'pending',
            'message' => $data['message'] ?? null,
            'image_1' => $paths['image_1'],
            'image_2' => $paths['image_2'],
        ]);

        $user->update(['status' => 'pending_owner']);

        // Thông báo in-app cho tất cả admin
        $notification = new OwnerRequestNotification(
            $ownerRequest,
            'submitted',
            "Người dùng <strong>{$user->name}</strong> muốn trở thành chủ sân."
        );
        User::whereHas('role', fn($q) => $q->where('name', 'admin'))->each(
            fn($admin) => $admin->notify($notification)
        );

        // Thông báo Telegram
        TelegramService::sendMessage(
            "🏟️ <b>YÊU CẦU CHỦ SÂN MỚI</b>\n" .
            "👤 <b>Người dùng:</b> {$user->name}\n" .
            "📧 <b>Email:</b> {$user->email}\n" .
            ($data['message'] ? "💬 <b>Lời nhắn:</b> {$data['message']}\n" : '') .
            "📋 <b>Giấy tờ:</b> Đã nộp 2 ảnh\n" .
            "👉 Vào trang admin để xem xét và duyệt."
        );

        return back()->with('success', 'Yêu cầu trở thành chủ sân đã được gửi. Vui lòng chờ quản trị viên duyệt.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
