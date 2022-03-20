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

    public function noteId($id) {
        return Notes::where('id', $id)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function label()
    {
        return $this->belongsTo(Label::class);
    }
    public function labelnote()
    {
        return $this->belongsTo(LabelNotes::class);
    }
}
