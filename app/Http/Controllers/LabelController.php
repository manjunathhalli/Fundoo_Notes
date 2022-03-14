<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'labelname' => 'required|string|between:2,15',
            'note_id' => 'required'
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            $labelName = Label::where('labelname', $request->labelname)->first();
            if ($labelName) {
                Log::alert('Label Created : ', ['email' => $request->email]);
                return response()->json(['message' => 'Label Name already exists'], 401);
            }

            $label = new Label;
            $label->labelname = $request->get('labelname');
            $label->note_id = $request->note_id;
            Cache::remember('lables', 3600, function () {
                return DB::table('lables')->get();
            });

            if ($user->labels()->save($label)) {
                return response()->json(['message' => 'Label added Sucessfully'], 201);
            }
            return response()->json(['message' => 'Could not add label'], 405);
        }
        return response()->json(['message' => 'Invalid authorization token'], 404);
    }

    public function addLabelByNoteId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'note_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user) {

            $id = $request->input('id');
            $request->input('note_id');

            $label = $user->labels()->find($id);
            if (!$label) {
                return response()->json(['message' => 'label not found'], 404);
            }
            $label->note_id = $request->get('note_id');

            if ($user->labels()->save($label)) {
                return response()->json(['message' => 'Label Added to Note Sucessfully'], 201);
            }
            return response()->json(['message' => 'Label Did Not added to Note'], 403);
        }
        return response()->json(['message' => 'Invalid authorization token'], 404);
    }

    public function displayLabel()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $lables = Label::where('user_id', $user->id)->get();
        if ($lables == '') {
            return response()->json(['message' => 'Label not found'], 404);
        }
        return response()->json([
            'label' => $lables
        ]);
    }

    public function updateLabelById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'labelname' => 'required|string|between:2,20',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $id = $request->input('id');
        $user = JWTAuth::parseToken()->authenticate();
        $label = $user->labels()->find($id);
        Cache::remember('lables', 3600, function () {
            return DB::table('lables')->get();
        });
        if (!$label) {
            Log::error('label Not Found', ['id' => $request->id]);
            return response()->json(['message' => 'label not Found'], 404);
        }
        $label->fill($request->all());
        if ($label->save()) {
            Log::info('Label updated', ['user_id' => $user, 'label_id' => $request->id]);
            return response()->json(['message' => 'Label updated Sucessfully'], 201);
        }
        if (!($label->save())) {
            return response()->json("Invalid Authorization token ", 404);
        }
        return $label;
    }

    public function deleteLabelById(Request $request)
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

        if ($label->delete()) {
            Log::info('Label deleted', ['user_id' => $user, 'label_id' => $request->id]);
            return response()->json(['message' => 'Label deleted Sucessfully'], 201);
        }
        if (!($label->delete())) {
            return response()->json(['message' => 'Invalid Authorization token'], 404);
        }
    }
}
