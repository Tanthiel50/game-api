<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categorie;

class Word extends Model
{
    use HasFactory;
    protected $fillable = ['term', 'definition', 'image', 'user_id'] ;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($word) {
            $word->categories()->detach();
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Categorie::class, 'words_categories', 'word_id', 'category_id');
    }
}


