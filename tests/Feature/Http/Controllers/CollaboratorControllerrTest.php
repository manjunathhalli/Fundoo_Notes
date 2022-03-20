<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollaboratorControllerrTest extends TestCase
{
    /**
     * @test 
     * for successfull add Collaborator
     * to given noteid
     */
    public function test_SuccessfullAddCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/addCollaboratorByNoteId',
            [
                "note_id" => "33",
                "email" => "manjii850@gmail.com",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Collaborator created Sucessfully']);
    }

    /**
     * @test 
     * for Unsuccessfull add Collaborator
     * to given noteid
     */
    public function test_UnSuccessfullAddCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/addCollaboratorByNoteId',
            [
                "note_id" => "33",
                "email" => "narayana@gmail.com",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'User Not Registered']);
    }

    /**
     * @test 
     * for successfull Remove Collaborator
     * to given noteid
     */
    public function test_Successfull_Remove_Collaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/removeCollaborator',
            [
                "note_id" => "33",
                "email" => "manjii850@gmail.com",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Collaborator deleted Sucessfully']);
    }

    /**
     * @test 
     * for Unsuccessfull Remove Collaborator
     * to given noteid
     */
    public function test_UnSuccessfull_Remove_Collaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/removeCollaborator',
            [
                "note_id" => "33",
                "email" => "manjii850@gmail.com",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Collaborator could not deleted']);
    }

    /**
     * @test 
     * for successfull Update Note 
     * By Collaborator
     * to given noteid
     */
    public function test_SuccessfullUpdate_Note_ByCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/updateNoteByCollaborator',
            [
                "note_id" => "33",
                "title" => "jeevan",
                "description" => "chethan",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Note updated Sucessfully']);
    }
    /**
     * @test 
     * for Unsuccessfull Update Note 
     * By Collaborator
     * to given noteid
     */
    public function test_UnSuccessfullUpdate_Note_ByCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json(
            'POST',
            '/api/auth/updateNoteByCollaborator',
            [
                "note_id" => "33",
                "title" => "jeevan",
                "description" => "chethan",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Collaborator Email not registered']);
    }
}
