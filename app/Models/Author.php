<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $table = 'authors';

    protected $fillable = [
        'firstname',
        'surname',
        'patronymic',
    ];

    public function magazines(): BelongsToMany
    {
        return $this->belongsToMany(Magazine::class, 'magazines_authors', 'author_id', 'magazine_id')->withTimestamps();
    }
}
