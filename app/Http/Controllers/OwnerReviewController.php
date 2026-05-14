<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerReviewController extends Controller
{
    /**
     * Danh sách review trên các sân của owner
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return redirect()->route('profile')->with('error', 'Bạn không có quyền truy cập.');
        }

        $arenaIds = $user->arenas()->pluck('id');

        $filterStatus = $request->get('status', 'approved'); // approved, all
        $filterArena = $request->get('arena_id', null);

        $query = Review::with(['user', 'arena'])
            ->whereIn('arena_id', $arenaIds);

        // Chỉ hiển thị approved hoặc tất cả tùy filter
        if ($filterStatus === 'approved') {
            $query->approved();
        }

        // Lọc theo sân nếu có
        if ($filterArena) {
            $query->where('arena_id', $filterArena);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);
        $arenas = $user->arenas()->get();

        return view('owner.reviews.index', compact('reviews', 'arenas', 'filterStatus', 'filterArena'));
    }

    /**
     * Báo cáo review không phù hợp (owner)
     */
    public function report(Review $review, Request $request)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra review có thuộc sân của owner hay không
        if (!$user->arenas()->where('arena_id', $review->arena_id)->exists()) {
            abort(403, 'Review này không thuộc sân của bạn.');
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        // Có thể lưu vào bảng report hoặc cập nhật cột trong review
        // Tạm thời chỉ cập nhật status thành rejected nếu report
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Đã báo cáo review. Admin sẽ xem xét sớm.');
    }
}
