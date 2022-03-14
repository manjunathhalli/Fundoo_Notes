<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotesController extends Controller
{
    public function createNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'description' => 'required|string|between:3,800',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $note = new Notes;
        $note->title = $request->input('title');
        $note->description = $request->input('description');
        $note->user_id = Auth::user()->id;
        $note->save();

        Cache::remember('notes', 3600, function () {
            return DB::table('notes')->get();
        });
        Log::info('notes created', ['user_id' => $note->user_id]);
        return response()->json([
            'status' => 201,
            'message' => 'notes created successfully'
        ]);
    }

    public function displayNotes()
    {
        $user = JWTAuth::parseToken()->authenticate();
        Cache::remember('notes', 3600, function () {
            return DB::table('notes')->get();
        });
        if ($user) {
            $user = Notes::where('user_id', '=', $user->id)->get();
        }
        if ($user == '[]') {
            return response()->json(['message' => 'Notes not found'], 404);
        }

        return response()->json([
            'message' => 'All Notes are Fetched Successfully',
            'Notes' => $user
        ], 200);
    }

    public function updateNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'required|string|between:2,30',
            'description' => 'required|string|between:3,1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = $request->input('id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($id);
        Cache::remember('notes', 3600, function () {
            return DB::table('notes')->get();
        });

        if (!$note) {
            Log::error('Notes Not Found', ['id' => $request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }
        $note->fill($request->all());

        if ($note->save()) {
            Log::info('notes update', ['user_id' => $currentUser, 'note_id' => $request->id]);
            return response()->json(['Message' => 'Note Updated Successfully'], 201);
        }
        if (!($note->save())) {
            return response()->json(['message' => 'Invalid Authorization token'], 404);
        }
    }

    public function deleteNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $id = $request->input('id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($id);
        Cache::remember('notes', 3600, function () {
            return DB::table('notes')->get();
        });

        if (!$note) {
            Log::error('Notes Not Found', ['id' => $request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }

        if ($note->delete()) {
            Log::info('notes deleted', ['user_id' => $currentUser, 'note_id' => $request->id]);
            return response()->json(['message' => 'Note deleted Successfully'], 201);
        }
        if (!($note->delete())) {
            return response()->json(['message' => 'Invalid Authorization token'], 404);
        }
    }
}
