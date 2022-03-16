<?php

namespace App\Http\Controllers;

use App\Exceptions\FundoNoteException;
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
            'title' => 'required|string|between:2,50',
            'description' => 'required|string|between:3,1000',
            // 'pin' => 'nullable|int|between:0,1',
            // 'archive' => 'nullable',
            // 'colour' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if(($request->has('pin'))==null)
        {
            $pin = 0;
        }
        
        else{
            $pin = $request->input('pin');

        }
        if(($request->has('archive'))==null)
        {
            $archive = 0;
        }
        else{
            $archive = $request->input('archive');
        }
        if(($request->has('colour'))==null)
        {
            $colour = 'rgb(255,255,255)';
        }
        else{
            $colour = $request->input('colour');
    
        }
        if(($request->has('label'))==null)
        {
            $label = 0;
        }
        else{
            $label = $request->input('label');

        }

        try {
            $note = new Notes;
            $note->title = $request->input('title');
            $note->description = $request->input('description');
            $note->pin =$pin;     
            $note->archive =$archive;
            $note->colour = $colour;
         //   $note->label = $label;
            $note->user_id = Auth::user()->id;
            $note->save();
            if (!$note) {
                throw new FundoNoteException("Invalid Authorization token ", 404);
            }
            $value = Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });
        } catch (FundoNoteException $e) {
            Log::error('Invalid User');
            return response()->json([
                'status' => $e->statusCode(),
                'message' => $e->message()
            ]);
        }
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

    public function displayNoteById(Request $request)
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
        return response()->json([
            'Note' => $note
        ]);
    }

    public function updateNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'string|between:2,30',
            'description' => 'string|between:3,1000',
            'pin' => 'int|between:0,1',
            'archive' => 'int|between:0,1',
            'colour' => 'string|max:20'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $note = $currentUser->notes()->find($id);

            if (!$note) {
                Log::error('Notes Not Found', ['id' => $request->id]);
                return response()->json(['message' => 'Notes not Found'], 404);
            }
            // return response()->json($request->all());
            $note->fill($request->all());

            if ($note->save()) {
                Log::info('notes updated', ['user_id' => $currentUser, 'note_id' => $request->id]);
                return response()->json(['message' => 'Note updated Successfully'], 201);
            }
            if (!($note->save())) {
                throw new FundoNoteException("Invalid Authorization token ", 404);
            }
        } catch (FundoNoteException $e) {
            return response()->json(['message' => $e->message(), 'status' => $e->statusCode()]);
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

    public function pinNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $noteObject = new Notes();
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $noteObject->noteId($request->id);

        if (!$note) {
            Log::error('Notes Not Found', ['user' => $currentUser, 'id' => $request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }

        if ($note->pin == 0) {
            if ($note->archive == 1) {
                $note->archive = 0;
                $note->save();
            }
            $note->pin = 1;
            $note->save();

            Log::channel('customLog')->info('notes Pinned', ['user_id' => $currentUser, 'note_id' => $request->id]);
            return response()->json([
                'status' => 201,
                'message' => 'Note Pinned Sucessfully'
            ], 201);
        }
    }

    public function archiveNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $noteObject = new Notes();
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $noteObject->noteId($request->id);

        if (!$note) {
            Log::error('Notes Not Found', ['user' => $currentUser, 'id' => $request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }

        if ($note->archive == 0) {
            if ($note->pin == 1) {
                $note->pin = 0;
                $note->save();
            }
            $note->archive = 1;
            $note->save();

            Log::info('notes Archived', ['user_id' => $currentUser, 'note_id' => $request->id]);
            return response()->json([
                'status' => 201,
                'message' => 'Note Archived Sucessfully'
            ], 201);
        }
    }

    public function colourNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'colour' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $noteObject = new Notes();
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $noteObject->noteId($request->id);


        if (!$note) {
            Log::error('Notes Not Found', ['user' => $currentUser, 'id' => $request->id]);
            return response()->json([
                'status' => 404,
                'message' => 'Notes not Found'
            ], 404);
        }

        $colours  =  array(
            'green' => 'rgb(0,255,0)',
            'red' => 'rgb(255,0,0)',
            'blue' => 'rgb(0,0,255)',
            'yellow' => 'rgb(255,255,0)',
            'grey' => 'rgb(128,128,128)',
            'purple' => 'rgb(128,0,128)',
            'brown' => 'rgb(165,42,42)',
            'orange' => 'rgb(255,165,0)',
            'pink' => 'rgb(255,192,203)',
            'black' => 'rgb(0,0,0)',
            'silver' => 'rgb(192,192,192)',
            'teal' => 'rgb(0,128,128)',
            'white' => 'rgb(255,255,255)',
        );

        $colour_name = strtolower($request->colour);

        if (isset($colours[$colour_name])) {
            $note->colour = $colours[$colour_name];
            $note->save();

            Log::info('notes coloured', ['user_id' => $currentUser, 'note_id' => $request->id]);
            return response()->json([
                'status' => 201,
                'message' => 'Note coloured Sucessfully'
            ], 201);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Colour Not Specified in the List'
            ], 400);
        }
    }
}
