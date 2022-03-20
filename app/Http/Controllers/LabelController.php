<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Notes;
use App\Models\LabelNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'labelname' => 'required|string|between:2,15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user) {
            $labelName = Label::where('labelname', $request->labelname)->first();
            if ($labelName) {
                return response()->json([
                    'message' => 'Label Name already exists'
                ], 401);
            }

            $label = new Label();
            $label->labelname = $request->get('labelname');

            if ($user->labels()->save($label)) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Label added Sucessfully',
                ], 201);
            }
        }

        return response()->json([
            'status' => 401,
            'message' => 'Invalid authorization token'
        ], 401);
    }

    public function readAllLabel()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Invalid authorization token'
            ], 404);
        }
        $label = Label::where('user_id', Auth::user()->id)->get();

        if (!$label) {
            return response()->json([
                'status' => 404,
                'message' => 'Notes not found'
            ], 401);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Labels Fetched  Successfully',
            'Label' => $label
        ], 201);
    }

    public function updateLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'labelname' => 'required|string|between:2,15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid authorization token'
            ], 401);
        }

        $notes = Label::where('id', $request->id)->first();
        if (!$notes) {
            return response()->json([
                'status' => 404,
                'message' => 'Label not Found'
            ], 404);
        }

        $notes->update([
            'id' => $request->id,
            'labelname' => $request->labelname,
        ]);

        Cache::forget('labels');
        Cache::forget('notes');
        return response()->json([
            'status' => 200,
            'message' => "Label updated Sucessfully"
        ], 200);
    }

    public function deleteLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid authorization token'
            ], 401);
        }

        $labels = Label::where('id', $request->id)->first();
        if (!($labels->user_id == $user->id) || !$labels) {
            return response()->json([
                'status' => 404,
                'message' => 'Label not found'
            ], 404);
        }

        $labels->delete($labels->id);
        Cache::forget('labels');
        Cache::forget('notes');
        return response()->json([
            'status' => 201,
            'message' => 'Label successfully deleted'
        ], 201);
    }

    function displyLabelById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $id = $request->input('id');
        $user = JWTAuth::parseToken()->authenticate();
        $label = $user->labels()->find($id);

        if (!$label) {
            Log::error('Label Not Found', ['label_id' => $request->id]);
            return response()->json(['message' => 'Label not Found'], 404);
        }
        Cache::remember('lables', 3600, function () {
            return DB::table('lables')->get();
        });
        if ($label == '') {
            return response()->json(['message' => 'Label not found'], 404);
        }
        return response()->json([
            'label' => $label
        ]);
    }

    public function addNoteLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required',
            'note_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user) {
            $labelnote = LabelNotes::where('note_id', $request->note_id)->where('label_id', $request->label_id)->first();
            if ($labelnote) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Note Already have a label'
                ], 409);
            }

            $labelnotes = new LabelNotes();
            $labelnotes->label_id = $request->label_id;
            $labelnotes->note_id = $request->note_id;
            if ($user->label_notes()->save($labelnotes)) {
                Cache::forget('notes');
                return response()->json([
                    'status' => 201,
                    'message' => 'Label note added Sucessfully',
                ], 201);
            }
        }

        return response()->json([
            'status' => 401,
            'message' => 'Invalid authorization token'
        ], 401);
    }

    public function deleteNoteLabel(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'label_id' => 'required',
            'note_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user) {
            $labelnote = LabelNotes::where('label_id', $req->label_id)->where('note_id', $req->note_id)->first();
            if (!$labelnote) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Note not found with this label'
                ], 404);
            }

            $labelnote->delete($labelnote->id);
            Cache::forget('notes');
            return response()->json([
                'status' => 201,
                'message' => 'Label successfully deleted'
            ], 200);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Invalid authorization token'
        ], 401);
    }

    public function displayNoteLabel()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Invalid authorization token'
            ], 404);
        }

        $labelnotes = LabelNotes::leftJoin('notes', 'notes.id', '=', 'label_notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('label_notes.id', 'lables.labelname', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour')
            ->where('label_notes.user_id', Auth::user()->id)->get();

        if (!$labelnotes) {
            return response()->json([
                'status' => 404,
                'message' => 'Notes not found'
            ], 401);
        }
        return response()->json([
            'status' => 201,
            'message' => 'Labelnotes Fetched  Successfully',
            'Labelnotes' => $labelnotes,
        ], 201);
    }
}
