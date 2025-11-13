<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportNoteItem extends Model
{
    use HasFactory;

    protected $table = 'import_note_items';

    protected $fillable = [
        'import_note_id',
        'product_variant_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function importNote()
    {
        return $this->belongsTo(ImportNote::class, 'import_note_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
