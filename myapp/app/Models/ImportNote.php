<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportNote extends Model
{
    use HasFactory;

    protected $table = 'import_notes';

    protected $fillable = [
        'employee_id',
        'total_price',
        'status',
        'note',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(ImportNoteItem::class, 'import_note_id');
    }

    public function getStatusLabelAttribute()
    {
        $map = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
        ];

        return $map[$this->status] ?? $this->status;
    }
}
