<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\Negotiation;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    /** Customer gửi đề xuất giá */
    public function store(Request $request, Arena $arena)
    {
        if (!$arena->isActive()) {
            return back()->with('error', 'Sân này hiện không nhận đề xuất.');
        }

        $data = $request->validate([
            'booking_id'     => ['nullable', 'integer', 'exists:bookings,id'],
            'proposed_price' => ['required', 'integer', 'min:1000'],
            'message'        => ['nullable', 'string', 'max:500'],
        ], [
            'proposed_price.required' => 'Vui lòng nhập giá đề xuất.',
            'proposed_price.min'      => 'Giá đề xuất tối thiểu là 1.000 VNĐ.',
        ]);

        $negotiation = Negotiation::create([
            'booking_id'     => $data['booking_id'] ?? null,
            'arena_id'       => $arena->id,
            'user_id'        => Auth::id(),
            'proposed_price' => $data['proposed_price'],
            'message'        => $data['message'] ?? null,
            'status'         => 'pending',
        ]);

        // Thông báo Telegram cho chủ sân
        $user = Auth::user();
        TelegramService::sendMessage(
            "🤝 <b>ĐỀ XUẤT GIÁ MỚI</b>\n" .
            "👤 <b>Khách hàng:</b> {$user->name}\n" .
            "⚽ <b>Sân:</b> {$arena->name}\n" .
            "💰 <b>Giá hiện tại:</b> " . number_format($arena->price) . " VNĐ/giờ\n" .
            "💵 <b>Giá đề xuất:</b> " . number_format($data['proposed_price']) . " VNĐ/giờ\n" .
            ($data['message'] ? "💬 <b>Lời nhắn:</b> {$data['message']}" : "")
        );

        return back()->with('success', 'Đã gửi đề xuất giá thành công. Chủ sân sẽ phản hồi sớm.');
    }

    /** Owner xem danh sách thương lượng */
    public function index()
    {
        $arenaIds = Auth::user()->arenas()->pluck('id');

        $negotiations = Negotiation::with(['user', 'arena', 'booking'])
            ->whereIn('arena_id', $arenaIds)
            ->orderByRaw("FIELD(status, 'pending', 'accepted', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('owner.negotiations.index', compact('negotiations'));
    }

    /** Owner chấp nhận đề xuất */
    public function accept(Request $request, Negotiation $negotiation)
    {
        $this->authorizeOwner($negotiation);

        if ($negotiation->status !== 'pending') {
            return back()->with('error', 'Đề xuất này đã được xử lý.');
        }

        $data = $request->validate([
            'owner_note' => ['nullable', 'string', 'max:500'],
        ]);

        $negotiation->update([
            'status'     => 'accepted',
            'owner_note' => $data['owner_note'] ?? null,
        ]);

        // Nếu có booking, cập nhật số tiền thanh toán
        if ($negotiation->booking_id && $negotiation->booking && $negotiation->booking->payment) {
            $negotiation->booking->payment->update([
                'amount' => $negotiation->proposed_price,
            ]);
        }

        // Từ chối các đề xuất pending khác cho cùng sân từ cùng user
        Negotiation::where('arena_id', $negotiation->arena_id)
            ->where('user_id', $negotiation->user_id)
            ->where('id', '!=', $negotiation->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Đã chấp nhận đề xuất giá.');
    }

    /** Owner từ chối đề xuất */
    public function reject(Request $request, Negotiation $negotiation)
    {
        $this->authorizeOwner($negotiation);

        if ($negotiation->status !== 'pending') {
            return back()->with('error', 'Đề xuất này đã được xử lý.');
        }

        $data = $request->validate([
            'owner_note' => ['nullable', 'string', 'max:500'],
        ]);

        $negotiation->update([
            'status'     => 'rejected',
            'owner_note' => $data['owner_note'] ?? null,
        ]);

        return back()->with('success', 'Đã từ chối đề xuất giá.');
    }

    private function authorizeOwner(Negotiation $negotiation): void
    {
        if ($negotiation->arena->owner_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xử lý đề xuất này.');
        }
    }
}
