<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function words()
{
    return $this->belongsToMany(Word::class, 'words_categories', 'category_id', 'word_id');
}
}
