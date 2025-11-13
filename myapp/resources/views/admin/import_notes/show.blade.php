@extends('admin.layouts.admin')

@section('title', 'Chi tiết phiếu nhập')

@section('content')
<div class="container mt-4">
    <h4>Phiếu nhập #{{ $note->id }}</h4>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Thu ngân:</strong> {{ $note->employee->name ?? 'N/A' }}</p>
            <p><strong>Trạng thái:</strong> {{ $note->status_label }}</p>
            <p><strong>Ghi chú:</strong> {{ $note->note ?? '-' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $note->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Chi tiết sản phẩm</div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead>
                <tbody>
                    @foreach($note->items as $it)
                        @php $pv = $it->product; @endphp
                        <tr>
                            <td>{{ $pv->product->name ?? 'N/A' }} @if($pv->brand) - {{ $pv->brand }}@endif @if($pv->attribute) ({{ $pv->attribute }})@endif</td>
                            <td>{{ number_format($it->price,0,',','.') }}đ</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ number_format($it->subtotal,0,',','.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end"><strong>Tổng:</strong></td><td><strong>{{ number_format($note->total_price,0,',','.') }}đ</strong></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.import-notes.index') }}" class="btn btn-secondary">Quay lại</a>
    @php
        $canEdit = (isset($currentRole) && $currentRole === 'admin') || (isset($currentEmployee) && $note->employee_id == $currentEmployee->id);
    @endphp
    @if($canEdit)
        <a href="{{ route('admin.import-notes.edit', $note) }}" class="btn btn-warning">Sửa</a>
    @endif
    @if($note->status === 'pending' && (!isset($currentRole) || $currentRole !== 'inventory'))
            <button class="btn btn-success approve-btn" data-id="{{ $note->id }}" data-url="{{ route('admin.import-notes.approve', $note->id) }}" onclick="approveNote(event)">Duyệt</button>
    @endif
    <form action="{{ route('admin.import-notes.destroy', $note) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        @if($canEdit)
            <button class="btn btn-danger">Xóa</button>
        @endif
    </form>
</div>
@endsection
@section('scripts')
<script>
function approveNote(event) {
    if (!confirm('Duyệt phiếu nhập này?')) return;
    
    const btn = event.target;
    const noteId = btn.getAttribute('data-id');
    const url = btn.getAttribute('data-url');
    
    console.log('Approving note:', noteId, 'URL:', url);
    
    // Use CSRF token from meta and send it in header
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : null;

    // Submit a normal form so browser sends cookies and session properly
    try {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = url;
        f.style.display = 'none';
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken || '';
        f.appendChild(tokenInput);
        const m = document.createElement('input');
        m.type = 'hidden';
        m.name = '_method';
        m.value = 'PATCH';
        f.appendChild(m);
        document.body.appendChild(f);
        f.submit();
    } catch (err) {
        console.error('Form submit fallback failed', err);
        alert('Không thể duyệt phiếu: ' + (err.message || err));
    }
}
</script>
@endsection
