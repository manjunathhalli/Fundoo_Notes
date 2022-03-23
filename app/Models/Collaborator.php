<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Collaborator extends Model
{
    use HasFactory;
    protected $table="collaborators";
    protected $fillable = [
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(Notes::class);
    }

    public function getAllNotes($currentUser){
        $collaborator = Collaborator::leftJoin('notes', 'notes.id', '=', 'collaborators.id')
        //->leftJoin('label_notes', 'label_notes.note_id', '=', '.id')
       // ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
       ->select('note_id', 'email')->where([['user_id', '=', $currentUser->id],])->get();
            return $collaborator ;
    }
}
