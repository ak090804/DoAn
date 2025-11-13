@extends('admin.layouts.admin')

@section('title', isset($note) ? 'Sửa phiếu nhập' : 'Tạo phiếu nhập')

@section('content')
<div class="container mt-4">
    <h4>{{ isset($note) ? 'Sửa phiếu nhập #' . $note->id : 'Tạo phiếu nhập mới' }}</h4>

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form id="import-note-form" action="{{ isset($note) ? route('admin.import-notes.update', $note) : route('admin.import-notes.store') }}" method="POST">
        @csrf
        @if(isset($note)) @method('PUT') @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Kiểm kho</label>
                @if(isset($currentUser) && $currentUser->role === 'inventory' && isset($currentEmployee))
                    <input type="hidden" name="employee_id" value="{{ $currentEmployee->id }}">
                    <input type="text" class="form-control" readonly value="{{ $currentEmployee->name }}">
                @else
                    <select name="employee_id" class="form-select" required>
                        <option value="">Chọn kiểm kho</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ (old('employee_id', $note->employee_id ?? '') == $emp->id) ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label>Trạng thái</label>
                @if(isset($currentUser) && $currentUser->role === 'inventory')
                    <input type="hidden" name="status" value="pending">
                    <input type="text" class="form-control" readonly value="Chờ">
                @else
                    <select name="status" class="form-select">
                        <option value="pending" {{ (old('status', $note->status ?? $defaultStatus ?? '') == 'pending') ? 'selected' : '' }}>Chờ</option>
                        <option value="approved" {{ (old('status', $note->status ?? $defaultStatus ?? '') == 'approved') ? 'selected' : '' }}>Đã duyệt</option>
                    </select>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5>Chi tiết sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:40px"></th>
                                <th>Sản phẩm</th>
                                <th style="width:120px">Đơn giá</th>
                                <th style="width:120px">Số lượng</th>
                                <th style="width:120px">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productVariants as $pv)
                                @php
                                    $existing = null;
                                    if(isset($note)) {
                                        $existing = $note->items->firstWhere('product_variant_id', $pv->id);
                                    }
                                @endphp
                                <tr data-pv-id="{{ $pv->id }}">
                                    <td class="align-middle text-center">
                                        <input type="checkbox" class="form-check-input include-checkbox" onchange="toggleQty(this)" {{ $existing ? 'checked' : '' }}>
                                    </td>
                                    <td class="align-middle">{{ $pv->product->name ?? 'N/A' }} @if($pv->brand) - {{ $pv->brand }}@endif @if($pv->attribute) ({{ $pv->attribute }})@endif</td>
                                    <td class="align-middle"><input type="text" class="form-control form-control-sm unit-price" value="{{ number_format($pv->price,2,'.','') }}" readonly></td>
                                    <td class="align-middle"><input type="number" class="form-control form-control-sm qty" min="1" value="{{ $existing->quantity ?? 1 }}"></td>
                                    <td class="align-middle"><input type="text" class="form-control form-control-sm subtotal" value="{{ isset($existing) ? number_format($existing->subtotal,2,'.','') : '0.00' }}" readonly></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tổng:</strong></td>
                                <td><input type="text" id="total" class="form-control" readonly value="0.00"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="selected-items"></div>
            </div>
        </div>

        {{-- note removed as requested --}}

        <div class="text-end">
            <button type="button" id="submit-btn" class="btn btn-primary">{{ isset($note) ? 'Cập nhật' : 'Tạo' }}</button>
            <a href="{{ route('admin.import-notes.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

@section('scripts')
    <script>
function recalcRow($tr){
    const price = parseFloat($tr.querySelector('.unit-price').value) || 0;
    const qty = parseFloat($tr.querySelector('.qty').value) || 0;
    const sub = (price * qty);
    $tr.querySelector('.subtotal').value = sub.toFixed(2);
}

function updateTotal(){
    const subs = [...document.querySelectorAll('.subtotal')].map(i=>parseFloat(i.value)||0);
    const s = subs.reduce((a,b)=>a+b,0);
    document.getElementById('total').value = s.toFixed(2);
}

function toggleQty(el){
    try{
        const tr = el.closest('tr');
        const qty = tr.querySelector('.qty');
        if(el.checked){
            qty.disabled = false;
            if(!qty.value || qty.value <= 0) qty.value = 1;
            try { qty.focus(); } catch(e) {}
        } else {
            qty.disabled = true;
        }
        recalcRow(tr);
        updateTotal();
    } catch(err){ console && console.error && console.error(err); }
}

    document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('import-note-form');
    if (!form) {
        console.error('Form #import-note-form not found!');
        return;
    }

    console.log('Form initialized:', form);
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    
    // enable/disable qty when checkbox toggled
    document.querySelectorAll('.include-checkbox').forEach(function(ch){
        ch.addEventListener('change', function(){
            const tr = this.closest('tr');
            const qty = tr.querySelector('.qty');
            if(this.checked){
                qty.disabled = false;
                if(!qty.value || qty.value <= 0) qty.value = 1;
                try { qty.focus(); } catch(e) {}
            } else {
                qty.disabled = true;
            }
            recalcRow(tr); updateTotal();
        });
    });

    // qty change: recalc and auto-check row when edited
    document.querySelectorAll('.qty').forEach(function(q){ q.addEventListener('input', function(){ const tr = this.closest('tr'); recalcRow(tr); updateTotal(); const ch = tr.querySelector('.include-checkbox'); if(ch && !ch.checked) ch.checked = true; }); });

    // initial recalc (for edit page existing items)
    document.querySelectorAll('tbody tr').forEach(function(tr){ recalcRow(tr); });
    updateTotal();

    // build items[] and submit explicitly to avoid timing/validation issues
    const submitBtn = document.getElementById('submit-btn');
    if(submitBtn){
        submitBtn.addEventListener('click', function(e){
            e.preventDefault();
            
            console.log('Submit button clicked');
            console.log('Form:', form);
            
            // HTML5 validation: if form invalid, show messages and stop
            if(!form.checkValidity()){
                form.reportValidity();
                return;
            }

            // remove previous selected inputs
            document.getElementById('selected-items').innerHTML = '';
            let idx = 0;
            document.querySelectorAll('tbody tr').forEach(function(tr){
                const checked = tr.querySelector('.include-checkbox').checked;
                if(!checked) return;
                const pvId = tr.getAttribute('data-pv-id');
                const qty = tr.querySelector('.qty').value || 0;
                const price = tr.querySelector('.unit-price').value || 0;

                const container = document.createElement('div');
                container.innerHTML = `
                    <input type="hidden" name="items[${idx}][product_variant_id]" value="${pvId}">
                    <input type="hidden" name="items[${idx}][quantity]" value="${qty}">
                    <input type="hidden" name="items[${idx}][price]" value="${price}">
                `;
                document.getElementById('selected-items').appendChild(container);
                idx++;
            });

            if(document.getElementById('selected-items').children.length === 0){
                alert('Vui lòng chọn ít nhất một sản phẩm để nhập.');
                return;
            }

            // submit via fetch so we can detect redirects (e.g. to admin login)
            const fd = new FormData(form);
            // append selected items (already added into #selected-items as inputs)
            
            console.log('FormData entries:', Array.from(fd.entries()));
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            fetch(form.action, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                redirect: 'manual',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(resp => {
                console.log('Response status:', resp.status);
                console.log('Response headers:', Array.from(resp.headers.entries()));
                
                // If server returned a redirect (302), handle location
                if(resp.status === 302 || resp.status === 301){
                    const loc = resp.headers.get('Location') || resp.headers.get('location');
                    console.log('Redirect location:', loc);
                    if(loc){
                        if(loc.includes('/admin/login')){
                            alert('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.');
                            window.location = loc; // go to login
                            return;
                        }
                        // otherwise navigate to the returned location (e.g., show page)
                        window.location = loc;
                        return;
                    }
                }

                // If OK, try to parse JSON for redirect/message
                if(resp.ok){
                    const ct = resp.headers.get('content-type') || '';
                    if(ct.indexOf('application/json') !== -1){
                        return resp.json().then(js => {
                            console.log('JSON response:', js);
                            if(js.redirect){ window.location = js.redirect; return; }
                            if(js.success){ window.location = '{{ route("admin.import-notes.index") }}'; return; }
                            throw new Error(js.message || 'Lỗi khi tạo phiếu.');
                        });
                    } else {
                        // fallback: navigate to index
                        window.location = '{{ route("admin.import-notes.index") }}';
                        return;
                    }
                }

                return resp.text().then(t => { 
                    console.error('Error response:', t);
                    throw new Error(t || 'Lỗi khi tạo phiếu.'); 
                });
            }).catch(err => {
                console.error('Fetch error:', err);
                alert('Không thể tạo phiếu nhập: ' + (err.message||err));
            });
        });
    }
});
    </script>
@endsection

@endsection
