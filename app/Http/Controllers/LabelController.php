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
    /**
     * This function takes the User access token and labelname
     * creates a label for that respective user.
     */
    /**
     * @OA\Post(
     *   path="/api/auth/createLabel",
     *   summary="Create Label",
     *   description=" Create Label ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"labelname"},
     *               @OA\Property(property="labelname", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Label added Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   @OA\Response(response=401, description="Label Name already exists"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
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
            'status' => 404,
            'message' => 'Invalid authorization token'
        ], 404);
    }


    /**
     * This function takes the User access token and label id and 
     * displays that respective label id.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/api/auth/readAllLabel",
     *   summary="Display Label",
     *   description=" Display Label ",
     *   @OA\RequestBody(
     *         
     *    ),
     *   @OA\Response(response=404, description="invalid authorization token"),
     *   @OA\Response(response=201, description="Labels Fetched Successfully"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
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

    /**
     * This function takes the User access token and label id and 
     * updates the label for the respective id.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/updateLabel",
     *   summary="Update Label",
     *   description=" Update label ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id" , "labelname"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="labelname", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Label updated Sucessfully"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   @OA\Response(response=404, description="Label not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

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


    /**
     * This function takes the User access token and label id and 
     * and deleted that particular label id.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/deleteLabel",
     *   summary="Delete Label",
     *   description=" Delete label ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Label Sucessfully deleted"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   @OA\Response(response=404, description="label not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

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

    /**
     * This function takes the User access token and note id and 
     * creates a label for that respective note is and user.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/addNoteLabel",
     *   summary="Add Label By Note Id",
     *   description=" Add Label By Note Id ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_id" , "note_id"},
     *               @OA\Property(property="label_id", type="integer"),
     *               @OA\Property(property="note_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Label notes added Successfully"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   @OA\Response(response=409, description="Note Already have a label"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

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


    /**
     *   @OA\POST(
     *   path="/api/auth/deleteNoteLabel",
     *   summary="delete note label",
     *   description="delete note label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_id","note_id"},
     *               @OA\Property(property="label_id", type="integer"),
     *               @OA\Property(property="note_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Label successfully deleted"),
     *   @OA\Response(response=404, description="Note not found with this label"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * function to delete the label from the note
     *
     * @var req Request
     */
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

    /**
     * This function takes the User access token and label id and note id and 
     * displays that respective label id.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/api/auth/displayNoteLabel",
     *   summary="Display Label note",
     *   description=" Display LabelNote ",
     *   @OA\RequestBody(
     *         
     *    ),
     *   @OA\Response(response=404, description="notes not Found"),
     *   @OA\Response(response=200, description="labelsNote are Fetched Successfully"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

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
