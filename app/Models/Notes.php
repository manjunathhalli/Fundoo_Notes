<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notes extends Model
{
    use HasFactory;
    protected $table = 'notes';
    protected $fillable = [
        'title',
        'descrption',
        'label_notes_id'
    ];

    public function noteId($id)
    {
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

    public function getAllNotes($user)
    {
        $notes = Notes::leftJoin('collaborators', 'collaborators.note_id', '=', 'notes.id')
            ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour', 'lables.labelname', 'collaborators.email as Collaborator',)
            ->where([['notes.user_id', '=', $user->id],])->orWhere('collaborators.email', '=', $user->email)->paginate(5);
        return $notes;
    }

    public function getAllpin($currentUser)
    {
        $usernotes = Notes::leftJoin('collaborators', 'collaborators.note_id', '=', 'notes.id')
            ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour', 'collaborators.email as Collaborator', 'lables.labelname')
            ->where([['notes.user_id', '=', $currentUser->id], ['pin', '=', 1]])->orWhere('collaborators.email', '=', $currentUser->email)->paginate(5);
        return $usernotes;
    }

    public function getAllArchive($currentUser)
    {
        $usernotes = Notes::leftJoin('collaborators', 'collaborators.note_id', '=', 'notes.id')
            ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour', 'collaborators.email as Collaborator', 'lables.labelname')
            ->where([['notes.user_id', '=', $currentUser->id], ['archive', '=', 1]])->orWhere('collaborators.email', '=', $currentUser->email)->paginate(5);
        return $usernotes;
    }

    public function searchNotes($currentUser, $searchKey)
    {
        $usernotes = Notes::leftJoin('collaborators', 'collaborators.note_id', '=', 'notes.id')->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour', 'collaborators.email as Collaborator', 'lables.labelname')
            ->where('notes.user_id', '=', $currentUser->id)->Where('notes.title', 'like', '%' . $searchKey . '%')
            ->orWhere('notes.user_id', '=', $currentUser->id)->Where('notes.description', 'like', '%' . $searchKey . '%')
            ->orWhere('notes.user_id', '=', $currentUser->id)->Where('lables.labelname', 'like', '%' . $searchKey . '%')
            ->orWhere('collaborators.email', '=', $currentUser->email)->Where('notes.title', 'like', '%' . $searchKey . '%')
            ->orWhere('collaborators.email', '=', $currentUser->email)->Where('notes.description', 'like', '%' . $searchKey . '%')
            ->orWhere('collaborators.email', '=', $currentUser->email)->Where('lables.labelname', 'like', '%' . $searchKey . '%')
            ->paginate(5);
        return  $usernotes;
    }
}
