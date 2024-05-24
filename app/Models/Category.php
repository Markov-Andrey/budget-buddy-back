<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $table = 'categories';

    use HasFactory;

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}
