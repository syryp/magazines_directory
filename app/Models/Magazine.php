<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Magazine extends Model
{
    use HasFactory;

    protected $table = 'magazines';

    protected $fillable = [
        'name',
        'short_description',
        'image',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'datetime',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'magazines_authors', 'magazine_id', 'author_id')->withTimestamps();
    }

    public function getImageUrl(): ?string
    {
        return $this->image !== null ? Storage::disk(config('app.product_image_storage'))->url($this->image) : null;
    }
}
