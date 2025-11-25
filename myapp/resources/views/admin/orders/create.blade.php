@extends('admin.layouts.admin')

@section('title', isset($order) ? 'Sửa đơn hàng' : 'Tạo đơn hàng')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">{{ isset($order) ? 'Sửa đơn hàng #' . $order->id : 'Tạo đơn hàng mới' }}</h4>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Bảng Điều Khiển</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item active">{{ isset($order) ? 'Sửa' : 'Tạo mới' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($order) ? route('admin.orders.update', $order) : route('admin.orders.store') }}" method="POST">
        @csrf
        @if(isset($order)) @method('PUT') @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin chung</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Khách hàng</label>
                        <select name="customer_id" class="form-select" {{ isset($order) ? 'disabled' : '' }} required>
                            <option value="">Chọn khách hàng</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    {{ (old('customer_id', $order->customer_id ?? null) == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thu ngân (nhân viên)</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Chọn thu ngân</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ (old('employee_id', $order->employee_id ?? null) == $employee->id) ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ (old('status', $order->status ?? null) == 'pending') ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="confirmed" {{ (old('status', $order->status ?? null) == 'confirmed') ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="shipping" {{ (old('status', $order->status ?? null) == 'shipping') ? 'selected' : '' }}>Đang giao</option>
                            <option value="completed" {{ (old('status', $order->status ?? null) == 'completed') ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ (old('status', $order->status ?? null) == 'cancelled') ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $order->note ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">Chi tiết sản phẩm</h5>
                    </div>
                    <div class="col-md-4">
                        <select id="supplierFilter" class="form-select form-select-sm" onchange="filterVariantsBySupplier()">
                            <option value="">-- Lọc theo nhà cung cấp --</option>
                            @php
                                $suppliers = \App\Models\Supplier::all();
                            @endphp
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-sm btn-success" onclick="addOrderItem()">Thêm sản phẩm</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="orderItems">
                    @if(isset($order))
                        @foreach($order->orderItems as $index => $item)
                            <div class="row order-item mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Sản phẩm</label>
                                    <select name="items[{{ $index }}][product_variant_id]" class="form-select product-select" required
                                        onchange="updatePrice(this)">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($productVariants as $variant)
                                            <option value="{{ $variant->id }}" 
                                                data-price="{{ $variant->price }}"
                                                {{ $item->product_variant_id == $variant->id ? 'selected' : '' }}>
                                                {{ $variant->product->name }} - {{ $variant->attribute }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Số lượng</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity"
                                        value="{{ $item->quantity }}" min="1" required onchange="updateSubtotal(this)">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Đơn giá</label>
                                    <input type="number" name="items[{{ $index }}][price]" class="form-control price"
                                        value="{{ $item->price }}" min="0" required onchange="updateSubtotal(this)">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Thành tiền</label>
                                    <input type="text" class="form-control subtotal" value="{{ $item->subtotal }}" readonly>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger d-block" onclick="removeOrderItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="row mt-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><strong>Tổng cộng</strong></label>
                            <div class="input-group">
                                <input type="text" id="totalAmountDisplay" class="form-control" readonly style="font-size: 1.2em; font-weight: bold;">
                                <span class="input-group-text">đ</span>
                            </div>
                            <input type="hidden" id="totalAmount" name="total_price">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">{{ isset($order) ? 'Cập nhật' : 'Tạo đơn hàng' }}</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ isset($order) ? count($order->orderItems) : 0 }};

// Data for all product variants with supplier info
const allVariants = {
    @foreach($productVariants as $variant)
        {{ $variant->id }}: {
            id: {{ $variant->id }},
            name: '{{ $variant->product->name }} - {{ $variant->attribute }}',
            price: {{ $variant->price }},
            supplierId: {{ $variant->supplier_id ?? 'null' }}
        },
    @endforeach
};

function filterVariantsBySupplier() {
    const supplierId = document.getElementById('supplierFilter').value;
    const selects = document.querySelectorAll('.product-select');
    
    selects.forEach(select => {
        const selectedId = select.value;
        const options = select.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const variantId = parseInt(option.value);
                const variant = allVariants[variantId];
                
                if (supplierId === '') {
                    // Show all
                    option.style.display = 'block';
                } else if (variant && variant.supplierId == supplierId) {
                    // Show only from this supplier
                    option.style.display = 'block';
                } else {
                    // Hide others
                    option.style.display = 'none';
                }
            }
        });
    });
}

function addOrderItem() {
    let optionsHtml = '<option value="">Chọn sản phẩm</option>';
    const supplierId = document.getElementById('supplierFilter').value;
    
    for (let variantId in allVariants) {
        const variant = allVariants[variantId];
        if (supplierId === '' || variant.supplierId == supplierId) {
            optionsHtml += `<option value="${variant.id}" data-price="${variant.price}">${variant.name}</option>`;
        }
    }
    
    const template = `
        <div class="row order-item mb-3">
            <div class="col-md-5">
                <label class="form-label">Sản phẩm</label>
                <select name="items[${itemIndex}][product_variant_id]" class="form-select product-select" required onchange="updatePrice(this)">
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Số lượng</label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" value="1" min="1" required onchange="updateSubtotal(this)">
            </div>
            <div class="col-md-2">
                <label class="form-label">Đơn giá</label>
                <input type="number" name="items[${itemIndex}][price]" class="form-control price" value="0" min="0" required onchange="updateSubtotal(this)">
            </div>
            <div class="col-md-2">
                <label class="form-label">Thành tiền</label>
                <input type="text" class="form-control subtotal" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger d-block" onclick="removeOrderItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('orderItems').insertAdjacentHTML('beforeend', template);
    itemIndex++;
    updateTotal();
}

function removeOrderItem(button) {
    button.closest('.order-item').remove();
    updateTotal();
}

function updatePrice(select) {
    const price = select.options[select.selectedIndex].dataset.price;
    const row = select.closest('.order-item');
    const priceInput = row.querySelector('.price');
    priceInput.value = price;
    updateSubtotal(priceInput);
}

function updateSubtotal(input) {
    const row = input.closest('.order-item');
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const subtotal = quantity * price;
    row.querySelector('.subtotal').value = subtotal.toFixed(0);
    updateTotal();
}

function updateTotal() {
    const subtotals = [...document.querySelectorAll('.subtotal')]
        .map(input => parseFloat(input.value) || 0);
    const total = subtotals.reduce((sum, value) => sum + value, 0);
    document.getElementById('totalAmountDisplay').value = numberFormat(total);
    document.getElementById('totalAmount').value = total;
}

function numberFormat(number) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(number));
}

// Initialize first item if creating new order
@if(!isset($order))
document.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});
@endif

// Initialize total on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});
</script>
@endpush
@endsection