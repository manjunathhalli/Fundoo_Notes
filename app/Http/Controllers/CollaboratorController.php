<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Mailer\SendEmailRequest;
use App\Notifications\EmailToCollab;
use App\Models\Collaborator;
use App\Models\Notes;
use App\Models\User;

class CollaboratorController extends Controller
{

    /**
     * This function takes User access token and checks if it is
     * authorised or not if so and takes note_id, email if those 
     * parameters are valid it will successfully creates a 
     * collaborator.
     * 
     * @return JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/addCollaboratorByNoteId",
     *   summary="Add Collaborator to specific Note ",
     *   description=" Add Colaborator to specific Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={ "note_id","email"},
     *               @OA\Property(property="note_id", type="integer"),
     *               @OA\Property(property="email", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Collaborator created Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

    public function addCollaboratorByNoteId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'email' => 'required|string|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($request->input('note_id'));
        $user = User::where('email', $request->email)->first();
        if ($currentUser) {
            if ($note) {
                if ($user) {

                    $collabUser = Collaborator::select('id')->where([
                        ['note_id', '=', $request->input('note_id')],
                        ['email', '=', $request->input('email')]
                    ])->get();

                    if ($collabUser != '[]') {
                        return response()->json(['message' => 'Collaborater Already Created'], 404);
                    }

                    $collab = new Collaborator;
                    $collab->note_id = $request->get('note_id');
                    $collab->email = $request->get('email');
                    $collaborator = Notes::select('id', 'title', 'description', 'pin',)->where([['id', '=', $request->note_id]])->get();
                    if ($currentUser->collaborators()->save($collab)) {
                        // $sendEmail = new SendEmailRequest();
                        // $sendEmail->sendEmailToCollab($request->email, $collaborator, $currentUser->email);

                        $delay = now()->addSeconds(60);
                        $user->notify((new EmailToCollab($request->email, $collaborator))->delay($delay));
                        return response()->json(['message' => 'Collaborator created Sucessfully'], 201);
                    }
                    return response()->json(['message' => 'Could not add collab'], 404);
                }
                return response()->json(['message' => 'User Not Registered'], 404);
            }
            return response()->json(['message' => 'Notes not found'], 404);
        }
        return response()->json(['message' => 'Invalid authorization token'], 404);
    }


    /**
     * This function takes User access token of collaborator and
     * checks if it is authorised or not if so and takes note details
     * as parametres if those are valid updates the notes successfully. 
     * 
     * @return JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/updateNoteByCollaborator",
     *   summary="Edit the note through Collaborator ",
     *   description=" Edit the note through Collaborator",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title" , "note_id" , "description"},
     *               @OA\Property(property="note_id", type="integer"),
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Note updated Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */

    public function updateNoteByCollaborator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'title' => 'string|between:2,30',
            'description' => 'string|between:3,1000',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = $request->input('note_id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        if ($currentUser) {
            $collabUser = Collaborator::where('email', $currentUser->email)->first();
            if ($collabUser) {
                $id = $request->input('note_id');
                $email = $currentUser->email;

                $collab = Collaborator::select('id')->where([
                    ['note_id', '=', $id],
                    ['email', '=', $email]
                ])->get();

                if ($collab == '[]') {
                    return response()->json(['message' => 'note_id is not correct'], 404);
                }

                $user = Notes::where('id', $request->note_id)
                    ->update(['title' => $request->title, 'description' => $request->description]);

                if ($user) {
                    return response()->json([
                        'status' => 201,
                        'message' => 'Note updated Sucessfully'
                    ], 201);
                }
                return response()->json(['message' => 'Note could not updated'], 201);
            }
            return response()->json(['message' => 'Collaborator Email not registered'], 404);
        }
        return response()->json(['message' => 'Invalid authorization token'], 404);
    }

    public function displayNoteByCollaborator()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Invalid authorization token'
            ], 404);
        }
        $collaborators = Collaborator::where('user_id', JWTAuth::user()->id)->get();

        if (!$collaborators) {
            return response()->json([
                'status' => 404,
                'message' => 'collabortors not found'
            ], 401);
        }

        return response()->json([
            'status' => 201,
            'message' => 'collabortors Fetched  Successfully',
            'collabortors' => $collaborators
        ], 201);
    }


    /**
     * This function takes User access token and checks if it is 
     * authorised or not if so and takes note_id and collabarator email
     * as parametres if those are valid deletes the notes successfully. 
     * 
     * @return JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/removeCollaborator",
     *   summary="Remove Collaborator from specific Note ",
     *   description=" Remove Collaborator from specific Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"note_id","email"},
     *               @OA\Property(property="note_id", type="integer"),
     *              @OA\Property(property="email", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Collaborator deleted Sucessfully"),
     *   @OA\Response(response=404, description="Collaborater Not created"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function removeCollaborator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'email' => 'required|string|email|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = $request->input('note_id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        if ($currentUser) {
            $id = $request->input('note_id');
            $email =  $request->input('email');

            $collaborator = Collaborator::select('id')->where([
                ['note_id', '=', $id],
                ['email', '=', $email]
            ])->get();

            if ($collaborator == '[]') {
                return response()->json(['message' => 'Collaborater Not created'], 404);
            }

            $collabDelete = Collaborator::where('note_id', '=', $id)->where('email', '=', $email)->delete();
            if ($collabDelete) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Collaborator deleted Sucessfully'
                ], 201);
            }
            return response()->json(['message' => 'Collaborator could not deleted'], 404);
        }
    }


    /**
     * This function takes User access token and checks if it is
     *  authorised or not if so it returns all the collabarators
     *  he has created.
     * 
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/api/auth/getAllCollaborators",
     *   summary="Display all Colaborators",
     *   description=" Display all Colaborators ",
     *   @OA\RequestBody(
     *         
     *    ),
     *   @OA\Response(response=201, description="Fetched Collaborators Successfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function getAllCollaborators()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();

        if ($currentUser) {
            $collaborator = Collaborator::select('note_id', 'email')->where([['user_id', '=', $currentUser->id],])->get();

            // $collaborator = new Collaborator();
            // $collaborator->getAllNotes($currentUser);

            if ($collaborator == '[]') {
                return response()->json(['message' => 'Collaborators not found'], 404);
            }
            return response()->json([
                'message' => 'Fetched Collaborators Successfully',
                'Collaborator' => $collaborator
            ], 201);
        }
        return response()->json(['message' => 'Invalid authorization token'], 403);
    }
}
