<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Groups extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'admin_id',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function groupMemberships()
    {
        return $this->hasMany(GroupMemberships::class, 'group_id');
    }
}
