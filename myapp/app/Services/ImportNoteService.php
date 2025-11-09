<?php

namespace App\Services;

use App\Models\ImportNote;
use App\Models\ImportNoteItem;
use Illuminate\Support\Facades\DB;

class ImportNoteService
{
    public function getAllPaginated($perPage = 15, $filters = [])
    {
        $query = ImportNote::with(['employee', 'items.product']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function find($id)
    {
        return ImportNote::with(['employee', 'items.product'])->findOrFail($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $note = ImportNote::create([
                'employee_id' => $data['employee_id'] ?? null,
                'total_price' => 0,
                'status' => $data['status'] ?? 'pending',
                'note' => $data['note'] ?? null,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                ImportNoteItem::create([
                    'import_note_id' => $note->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $note->update(['total_price' => $total]);
            DB::commit();
            return $note;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $note = ImportNote::findOrFail($id);
            $note->update([
                'employee_id' => $data['employee_id'] ?? $note->employee_id,
                'status' => $data['status'] ?? $note->status,
                'note' => $data['note'] ?? $note->note,
            ]);

            if (isset($data['items'])) {
                $note->items()->delete();
                $total = 0;
                foreach ($data['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                    ImportNoteItem::create([
                        'import_note_id' => $note->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                    ]);
                }
                $note->update(['total_price' => $total]);
            }

            DB::commit();
            return $note;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $note = ImportNote::findOrFail($id);
            $note->items()->delete();
            $note->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
