<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptsOrganization extends Model
{
    use HasFactory;

    protected $table = 'receipts_organization';
    protected $fillable = [
        'receipts_id',
        'name',
        'city',
        'street',
        'entrance'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipts_id');
    }
}
