<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Arena $arena)
    {
        $user = Auth::user();

        // Chỉ customer đã có booking completed mới được đánh giá
        $hasCompleted = Booking::where('user_id', $user->id)
            ->where('arena_id', $arena->id)
            ->where('status', 'completed')
            ->exists();

        if (!$hasCompleted) {
            return back()->with('review_error', 'Bạn cần hoàn thành ít nhất một buổi chơi tại sân này để đánh giá.');
        }

        // Mỗi user chỉ đánh giá 1 lần / sân
        if (Review::where('user_id', $user->id)->where('arena_id', $arena->id)->exists()) {
            return back()->with('review_error', 'Bạn đã đánh giá sân này rồi.');
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required'  => 'Vui lòng chọn số sao.',
            'comment.required' => 'Vui lòng nhập nhận xét.',
            'comment.min'      => 'Nhận xét cần ít nhất 10 ký tự.',
        ]);

        Review::create([
            'user_id'  => $user->id,
            'arena_id' => $arena->id,
            'rating'   => $data['rating'],
            'comment'  => $data['comment'],
            'status'   => 'pending', // Mặc định review chờ duyệt
        ]);

        return back()->with('review_success', 'Cảm ơn bạn đã đánh giá! Review của bạn đang chờ duyệt.');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();

        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $review->delete();

        return back()->with('review_success', 'Đã xóa đánh giá.');
    }
}
