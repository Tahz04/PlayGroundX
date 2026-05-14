<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Danh sách review chờ duyệt (admin)
     */
    public function index(Request $request)
    {
        $filterStatus = $request->get('status', 'all'); // all, pending, approved, rejected

        $query = Review::with(['user', 'arena']);

        if ($filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reviews.index', compact('reviews', 'filterStatus'));
    }

    /**
     * Duyệt review (approve)
     */
    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt đánh giá thành công!');
    }

    /**
     * Từ chối review (reject)
     */
    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Đã từ chối đánh giá!');
    }

    /**
     * Xóa review
     */
    public function destroy(Review $review)
    {
        $arenaName = $review->arena->name ?? 'Sân bóng';
        $review->delete();

        return back()->with('success', "Đã xóa đánh giá sân '{$arenaName}' thành công!");
    }
}
