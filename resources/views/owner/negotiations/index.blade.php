@extends('layouts.app')

@section('title', 'Thương Lượng Giá - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 px-3 mb-3">Thương Lượng</span>
                <h1 class="display-6 mb-3">Đề Xuất Giá Từ Khách Hàng</h1>
                <p class="text-muted fs-6">Xem xét và phản hồi các đề xuất giá từ khách hàng.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Khách Hàng</th>
                            <th>Sân</th>
                            <th>Giá Gốc</th>
                            <th>Giá Đề Xuất</th>
                            <th>Lời Nhắn</th>
                            <th>Trạng Thái</th>
                            <th>Thời Gian</th>
                            <th class="text-end pe-4">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($negotiations as $negotiation)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $negotiation->user->name }}</div>
                                    <div class="small text-muted">{{ $negotiation->user->email }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $negotiation->arena->name }}</div>
                                    <div class="small text-muted">{{ $negotiation->arena->type }}</div>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ number_format($negotiation->arena->price) }}đ/giờ</span>
                                </td>
                                <td>
                                    @php
                                        $diff = $negotiation->proposed_price - $negotiation->arena->price;
                                        $pct  = $negotiation->arena->price > 0
                                            ? round(abs($diff) / $negotiation->arena->price * 100)
                                            : 0;
                                    @endphp
                                    <div class="fw-bold {{ $diff < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($negotiation->proposed_price) }}đ/giờ
                                    </div>
                                    <div class="small {{ $diff < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $diff < 0 ? '-' : '+' }}{{ $pct }}%
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $negotiation->message ?? '—' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusCfg = match($negotiation->status) {
                                            'pending'  => ['bg-warning text-dark', 'Chờ xử lý'],
                                            'accepted' => ['bg-success text-white', 'Đã chấp nhận'],
                                            'rejected' => ['bg-danger text-white', 'Đã từ chối'],
                                            default    => ['bg-secondary text-white', $negotiation->status],
                                        };
                                    @endphp
                                    <span class="badge {{ $statusCfg[0] }} rounded-pill px-3 py-2 small">
                                        {{ $statusCfg[1] }}
                                    </span>
                                    @if($negotiation->owner_note)
                                        <div class="small text-muted mt-1">{{ Str::limit($negotiation->owner_note, 40) }}</div>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $negotiation->created_at->diffForHumans() }}</td>
                                <td class="text-end pe-4">
                                    @if($negotiation->status === 'pending')
                                        <div class="d-flex justify-content-end gap-2">
                                            {{-- Accept --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-success rounded-pill px-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#acceptModal"
                                                    data-neg-id="{{ $negotiation->id }}"
                                                    data-customer="{{ $negotiation->user->name }}"
                                                    data-price="{{ number_format($negotiation->proposed_price) }}">
                                                <i class="fas fa-check me-1"></i> Chấp nhận
                                            </button>
                                            {{-- Reject --}}
                                            <form action="{{ route('owner.negotiations.reject', $negotiation) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                        onclick="return confirm('Từ chối đề xuất của {{ $negotiation->user->name }}?')">
                                                    <i class="fas fa-times me-1"></i> Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Đã xử lý</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-handshake fa-2x text-muted mb-3 d-block opacity-50"></i>
                                    <span class="text-muted">Chưa có đề xuất giá nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($negotiations->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $negotiations->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Modal xác nhận chấp nhận --}}
<div class="modal fade" id="acceptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fas fa-check-circle text-success me-2"></i>Chấp nhận đề xuất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="acceptForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body pt-2">
                    <p class="text-muted mb-3">
                        Khách: <strong id="acceptCustomer"></strong> —
                        Giá đề xuất: <strong id="acceptPrice"></strong>đ/giờ
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú phản hồi <span class="text-muted">(tùy chọn)</span></label>
                        <textarea name="owner_note" class="form-control" rows="2"
                                  placeholder="Thông tin thêm cho khách hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-check me-1"></i> Xác nhận chấp nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: .72rem;
    letter-spacing: .05em;
    color: #64748b;
    padding-top: 1.1rem;
    padding-bottom: 1.1rem;
    border-bottom: 2px solid #f1f5f9;
}
.table tbody td {
    padding-top: .9rem;
    padding-bottom: .9rem;
    border-bottom: 1px solid #f1f5f9;
}
.table-hover tbody tr:hover { background: #f8fafc; }
</style>

@push('scripts')
<script>
document.getElementById('acceptModal').addEventListener('show.bs.modal', function (event) {
    const btn   = event.relatedTarget;
    const negId = btn.dataset.negId;
    document.getElementById('acceptCustomer').textContent = btn.dataset.customer;
    document.getElementById('acceptPrice').textContent    = btn.dataset.price;
    document.getElementById('acceptForm').action = '/owner/negotiations/' + negId + '/accept';
});
</script>
@endpush
@endsection
