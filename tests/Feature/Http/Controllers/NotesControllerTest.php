<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotesControllerTest extends TestCase
{
    /**
     * @test 
     * for successfull notecreation
     */
    public function test_SuccessfullCreateNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMTU3OCwiZXhwIjoxNjQ3NzA1MTc4LCJuYmYiOjE2NDc3MDE1NzgsImp0aSI6IkE2djdGTlZCSXk2R0YwRTUiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.suQXmdfedUFYt21wbRfoL9NZHzQ5NeFG7qW3QqCz-HE'
        ])->json(
            'POST',
            '/api/auth/createNotes',
            [
                "title" => "testing title",
                "description" => "testing description",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'notes created successfully']);
    }

    /**
     * @test 
     * for Successfull Note update
     */

    public function test_SuccessfullUpdateNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMjg0OCwiZXhwIjoxNjQ3NzA2NDQ4LCJuYmYiOjE2NDc3MDI4NDgsImp0aSI6Ilpoa0hTdk5Fc3h6SVV4V1QiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.1Kc_GnSsVaie0wM4V4WQpgcHRjHvDObpwx1KQN6HRLg'
        ])->json(
            'POST',
            '/api/auth/updateNoteById',
            [
                "id" => "2",
                "title" => "updated title",
                "description" => "updated description",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note updated Successfully']);
    }

    /**
     * @test 
     * for Successfull Deletion of Node
     */
    public function test_SuccessfullDeleteNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMjg0OCwiZXhwIjoxNjQ3NzA2NDQ4LCJuYmYiOjE2NDc3MDI4NDgsImp0aSI6Ilpoa0hTdk5Fc3h6SVV4V1QiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.1Kc_GnSsVaie0wM4V4WQpgcHRjHvDObpwx1KQN6HRLg'
        ])->json(
            'POST',
            '/api/auth/deleteNoteById',
            [
                "id" => "3"
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Note deleted Successfully']);
    }

    /**
     * @test 
     * for Unsuccessfull Note Updatation
     */

    public function test_FailUpdateNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMjg0OCwiZXhwIjoxNjQ3NzA2NDQ4LCJuYmYiOjE2NDc3MDI4NDgsImp0aSI6Ilpoa0hTdk5Fc3h6SVV4V1QiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.1Kc_GnSsVaie0wM4V4WQpgcHRjHvDObpwx1KQN6HRLg'
        ])->json(
            'POST',
            '/api/auth/updateNoteById',
            [
                "id" => "11",
                "title" => "title Test",
                "description" => "description Test",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }

    /**
     * @test 
     * for Successfull Deletion of Node
     */
    public function test_UnSuccessfullDeleteNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMjg0OCwiZXhwIjoxNjQ3NzA2NDQ4LCJuYmYiOjE2NDc3MDI4NDgsImp0aSI6Ilpoa0hTdk5Fc3h6SVV4V1QiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.1Kc_GnSsVaie0wM4V4WQpgcHRjHvDObpwx1KQN6HRLg'
        ])->json(
            'POST',
            '/api/auth/deleteNoteById',
            [
                "id" => "27"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }

    /**
     * @test 
     * for Successfull pinned the Node
     */
    public function test_Successfull_Pinned_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMjg0OCwiZXhwIjoxNjQ3NzA2NDQ4LCJuYmYiOjE2NDc3MDI4NDgsImp0aSI6Ilpoa0hTdk5Fc3h6SVV4V1QiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.1Kc_GnSsVaie0wM4V4WQpgcHRjHvDObpwx1KQN6HRLg'
        ])->json(
            'POST',
            '/api/auth/pinNoteById',
            [
                "id" => "2"
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note pinned Successfully']);
    }

    /**
     * @test 
     * for UnSuccessfull pinned the Node
     */
    public function test_UnSuccessfull_Pinned_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMzY3NCwiZXhwIjoxNjQ3NzA3Mjc0LCJuYmYiOjE2NDc3MDM2NzQsImp0aSI6InBtenFzdUpMM1lmd3BvUjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ._KU54vnCME6ArD2J7c8J3rPL4a74TOZry5tuViPwYqg'
        ])->json(
            'POST',
            '/api/auth/pinNoteById',
            [
                "id" => "19"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }

    /**
     * @test 
     * for Successfull Archive the Node
     */
    public function test_Successfull_Archived_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMzY3NCwiZXhwIjoxNjQ3NzA3Mjc0LCJuYmYiOjE2NDc3MDM2NzQsImp0aSI6InBtenFzdUpMM1lmd3BvUjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ._KU54vnCME6ArD2J7c8J3rPL4a74TOZry5tuViPwYqg'
        ])->json(
            'POST',
            '/api/auth/archiveNoteById',
            [
                "id" => "2"
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Note archived Successfully']);
    }

    /**
     * @test 
     * for Successfull Archive the Node
     */
    public function test_UnSuccessfull_Archived_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMzY3NCwiZXhwIjoxNjQ3NzA3Mjc0LCJuYmYiOjE2NDc3MDM2NzQsImp0aSI6InBtenFzdUpMM1lmd3BvUjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ._KU54vnCME6ArD2J7c8J3rPL4a74TOZry5tuViPwYqg'
        ])->json(
            'POST',
            '/api/auth/archiveNoteById',
            [
                "id" => "19"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }

    /**
     * @test 
     * for Successfull coloring the Node
     */
    public function test_Successfull_Color_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMzY3NCwiZXhwIjoxNjQ3NzA3Mjc0LCJuYmYiOjE2NDc3MDM2NzQsImp0aSI6InBtenFzdUpMM1lmd3BvUjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ._KU54vnCME6ArD2J7c8J3rPL4a74TOZry5tuViPwYqg'
        ])->json(
            'POST',
            '/api/auth/colourNoteById',
            [
                "id" => "2",
                "colour" => "green"
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Note coloured Sucessfully']);
    }

    /**
     * @test 
     * for UnSuccessfull coloring the Node
     */
    public function test_UnSuccessfull_Color_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwMzY3NCwiZXhwIjoxNjQ3NzA3Mjc0LCJuYmYiOjE2NDc3MDM2NzQsImp0aSI6InBtenFzdUpMM1lmd3BvUjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ._KU54vnCME6ArD2J7c8J3rPL4a74TOZry5tuViPwYqg'
        ])->json(
            'POST',
            '/api/auth/colourNoteById',
            [
                "id" => "19",
                "colour" => "green"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }
}
