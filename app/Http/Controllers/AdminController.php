<?php

namespace App\Http\Controllers;

use App\Models\OwnerRequest;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $requests = OwnerRequest::with('user')->latest()->get();

        return view('admin.owner-requests.index', compact('requests'));
    }

    public function approve(OwnerRequest $ownerRequest)
    {
        if ($ownerRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý trước đó.');
        }

        $ownerRoleId = Role::where('name', 'owner')->value('id');

        DB::transaction(function () use ($ownerRequest, $ownerRoleId) {
            $ownerRequest->user->update([
                'role_id' => $ownerRoleId,
                'status' => 'active',
            ]);

            $ownerRequest->update(['status' => 'approved']);
        });

        return back()->with('success', 'Yêu cầu đã được duyệt.');
    }

    public function reject(OwnerRequest $ownerRequest)
    {
        if ($ownerRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý trước đó.');
        }

        DB::transaction(function () use ($ownerRequest) {
            $ownerRequest->user->update(['status' => 'active']);
            $ownerRequest->update(['status' => 'rejected']);
        });

        return back()->with('success', 'Yêu cầu đã bị từ chối.');
    }
}
