<?php

namespace App\Http\Controllers;

use App\Models\OwnerRequest;
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
        ]);

        $user->ownerRequests()->create([
            'status' => 'pending',
            'message' => $data['message'] ?? null,
        ]);

        $user->update(['status' => 'pending_owner']);

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
