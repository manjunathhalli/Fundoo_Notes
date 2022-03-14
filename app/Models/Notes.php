<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;
    protected $table = 'notes';
    protected $fillable = [
        'title',
        'descrption'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function label()
    {
        return $this->belongsTo(Label::class);
    }
}
