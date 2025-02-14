<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['locale_id', 'key', 'content', 'tags'];

    public function locale() {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
