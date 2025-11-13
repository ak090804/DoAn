@extends('admin.layouts.admin')

@section('title', 'Phiếu nhập')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Phiếu nhập</h4>
        <a href="{{ route('admin.import-notes.create') }}" class="btn btn-success">Tạo phiếu nhập</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nhân viên</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                        <tr>
                            <td>{{ $note->id }}</td>
                            <td>{{ $note->employee->name ?? 'N/A' }}</td>
                            <td>{{ number_format($note->total_price, 0, ',', '.') }}đ</td>
                            <td>{{ $note->status_label }}</td>
                            <td>{{ $note->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.import-notes.show', $note) }}" class="btn btn-info btn-sm">Chi tiết</a>
                                @php
                                    $canEdit = (isset($currentRole) && $currentRole === 'admin') || (isset($currentEmployee) && $note->employee_id == $currentEmployee->id);
                                @endphp
                                @if($canEdit)
                                    <a href="{{ route('admin.import-notes.edit', $note) }}" class="btn btn-warning btn-sm">Sửa</a>
                                @endif
                                @if($note->status === 'pending' && (!isset($currentRole) || $currentRole !== 'inventory'))
                                        <button class="btn btn-success btn-sm approve-btn" data-id="{{ $note->id }}" data-url="{{ route('admin.import-notes.approve', $note->id) }}" onclick="approveNote(event)">Duyệt</button>
                                @endif
                                @if($canEdit)
                                <form action="{{ route('admin.import-notes.destroy', $note) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Không có phiếu nhập</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">{{ $notes->links() }}</div>
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

    // Fallback to a normal form submit to avoid fetch/cors/session issues
    try {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = url;
        f.style.display = 'none';
        // CSRF
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken || '';
        f.appendChild(tokenInput);
        // method override to PATCH
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
