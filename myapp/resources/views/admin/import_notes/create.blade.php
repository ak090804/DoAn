@extends('admin.layouts.admin')

@section('title', isset($note) ? 'Sửa phiếu nhập' : 'Tạo phiếu nhập')

@section('content')
<div class="container mt-4">
    <h4>{{ isset($note) ? 'Sửa phiếu nhập #' . $note->id : 'Tạo phiếu nhập mới' }}</h4>

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ isset($note) ? route('admin.import-notes.update', $note) : route('admin.import-notes.store') }}" method="POST">
        @csrf
        @if(isset($note)) @method('PUT') @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Thu ngân</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Chọn thu ngân</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (old('employee_id', $note->employee_id ?? '') == $emp->id) ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ (old('status', $note->status ?? '') == 'pending') ? 'selected' : '' }}>Chờ</option>
                    <option value="approved" {{ (old('status', $note->status ?? '') == 'approved') ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="cancelled" {{ (old('status', $note->status ?? '') == 'cancelled') ? 'selected' : '' }}>Hủy</option>
                </select>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5>Chi tiết sản phẩm</h5>
                <button type="button" class="btn btn-sm btn-success" onclick="addItem()">Thêm</button>
            </div>
            <div class="card-body">
                <div id="items">
                    @if(isset($note))
                        @foreach($note->items as $i => $it)
                            <div class="row item-row mb-3">
                                <div class="col-md-5">
                                    <select name="items[{{ $i }}][product_id]" class="form-select product-select" onchange="setPrice(this)">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ $it->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2"><input type="number" name="items[{{ $i }}][quantity]" class="form-control qty" value="{{ $it->quantity }}" min="1" onchange="recalc(this)"></div>
                                <div class="col-md-2"><input type="number" name="items[{{ $i }}][price]" class="form-control price" value="{{ $it->price }}" min="0" onchange="recalc(this)"></div>
                                <div class="col-md-2"><input type="text" class="form-control subtotal" value="{{ $it->subtotal }}" readonly></div>
                                <div class="col-md-1"><button type="button" class="btn btn-danger" onclick="removeItem(this)">x</button></div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="row mt-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <label>Tổng</label>
                        <input type="text" id="total" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label>Ghi chú</label>
            <textarea name="note" class="form-control">{{ old('note', $note->note ?? '') }}</textarea>
        </div>

        <div class="text-end">
            <button class="btn btn-primary">{{ isset($note) ? 'Cập nhật' : 'Tạo' }}</button>
            <a href="{{ route('admin.import-notes.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let idx = {{ isset($note) ? count($note->items) : 0 }};
function addItem(){
    const html = `
    <div class="row item-row mb-3">
        <div class="col-md-5">
            <select name="items[${idx}][product_id]" class="form-select product-select" onchange="setPrice(this)">
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="number" name="items[${idx}][quantity]" class="form-control qty" value="1" min="1" onchange="recalc(this)"></div>
        <div class="col-md-2"><input type="number" name="items[${idx}][price]" class="form-control price" value="0" min="0" onchange="recalc(this)"></div>
        <div class="col-md-2"><input type="text" class="form-control subtotal" readonly></div>
        <div class="col-md-1"><button type="button" class="btn btn-danger" onclick="removeItem(this)">x</button></div>
    </div>`;
    document.getElementById('items').insertAdjacentHTML('beforeend', html);
    idx++; updateTotal();
}
function removeItem(btn){ btn.closest('.item-row').remove(); updateTotal(); }
function setPrice(sel){ const price = sel.options[sel.selectedIndex].dataset.price || 0; const row = sel.closest('.item-row'); row.querySelector('.price').value = price; recalc(row.querySelector('.price')); }
function recalc(el){ const row = el.closest('.item-row'); const q = parseFloat(row.querySelector('.qty').value)||0; const p = parseFloat(row.querySelector('.price').value)||0; row.querySelector('.subtotal').value = (q*p).toFixed(2); updateTotal(); }
function updateTotal(){ const subs = [...document.querySelectorAll('.subtotal')].map(i=>parseFloat(i.value)||0); const s = subs.reduce((a,b)=>a+b,0); document.getElementById('total').value = s.toFixed(2); }
document.addEventListener('DOMContentLoaded', updateTotal);
</script>
@endpush

@endsection
