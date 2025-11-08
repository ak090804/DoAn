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
}