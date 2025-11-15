<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'brand',
        'supplier_id',
        'attribute',
        'description', 
        'price',
        'image',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'product_variant_id');
    }
}