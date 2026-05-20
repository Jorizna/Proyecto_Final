<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reposte extends Model
{
    protected $table = 'repostes';

    protected $fillable = ['user_id', 'publicacion_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }
}
