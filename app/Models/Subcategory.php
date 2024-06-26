<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['name', 'category_id, is_check'];
    protected $table = 'subcategories';

    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
