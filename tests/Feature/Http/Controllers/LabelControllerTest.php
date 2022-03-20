<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_IfGiven_LabelName_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1Nzc4NywiZXhwIjoxNjQ3NzYxMzg3LCJuYmYiOjE2NDc3NTc3ODcsImp0aSI6Ijc2SnRPZDBUQnpuZFNJR0giLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kysqsZ81kph5pwPhsyUCHO25PQXLcJvjb8HeqSaYxjw'
        ])->json('POST', '/api/auth/createLabel', 
        [
            "labelname" => "new label",
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Label added Sucessfully']);
    }

     //create label Error
     public function test_IfGiven_LabelName_ShouldValidate_AndReturnErrorsStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1Nzc4NywiZXhwIjoxNjQ3NzYxMzg3LCJuYmYiOjE2NDc3NTc3ODcsImp0aSI6Ijc2SnRPZDBUQnpuZFNJR0giLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kysqsZ81kph5pwPhsyUCHO25PQXLcJvjb8HeqSaYxjw'
         ])->json('POST', '/api/auth/createLabel', 
             [
                 "labelname" => "label first one",
             ]
         );
 
         $response->assertStatus(401)->assertJson(['message' => 'Label Name already exists']);
     }
   

            //read all labels success
    public function test_IfGiven_AuthorisedToken_AndReturnAllLabels_SuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1Nzc4NywiZXhwIjoxNjQ3NzYxMzg3LCJuYmYiOjE2NDc3NTc3ODcsImp0aSI6Ijc2SnRPZDBUQnpuZFNJR0giLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kysqsZ81kph5pwPhsyUCHO25PQXLcJvjb8HeqSaYxjw'
        ])->json('GET', '/api/auth/readAllLabel');

        $response->assertStatus(201)->assertJson(['message' => 'Labels Fetched  Successfully']);
    }

    //read all labels error
    public function test_IfGiven_WrongAuthorisedToken_AndReturnAllLabels_ErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer ezJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1Nzc4NywiZXhwIjoxNjQ3NzYxMzg3LCJuYmYiOjE2NDc3NTc3ODcsImp0aSI6Ijc2SnRPZDBUQnpuZFNJR0giLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kysqsZ81kph5pwPhsyUCHO25PQXLcJvjb8HeqSaYxjw'
        ])->json('GET', '/api/auth/readAllLabel');

        $response->assertStatus(404)->assertJson(['message' => 'Invalid authorization token']);
    }


        //update label
    public function test_IfGiven_Label_idAnd_LabelNameAndToken_ShouldValidate_AndReturnUpdateSuccessStatus()
        {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
            ])->json('POST', '/api/auth/updateLabel',
            [
                "id" => 1,
                "labelname" => "Label update",
            ]);
    
            $response->assertStatus(201)->assertJson(['message' => 'Label updated Sucessfully']);
        }

        //label update error
        public function test_IfGiven_WrongLabel_idAnd_LabelNameAndToken_ShouldValidate_AndReturnUpdateErrorStatus()
        {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
            ])->json('POST', '/api/auth/updateLabel',
            [
                "id" => 37,
                "labelname" => "Label update",
            ]);
    
            $response->assertStatus(404)->assertJson(['message' => 'Label not Found']);
        }

         //label delete success
    public function test_IfGiven_Label_idAnd_ShouldValidate_AndReturnDeleteSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0Nzc1ODcyNSwiZXhwIjoxNjQ3NzYyMzI1LCJuYmYiOjE2NDc3NTg3MjUsImp0aSI6IjF2TGxEc011MHZPbkNGWEgiLCJzdWIiOjgsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rDEGt_MfsZHPUt2UAEcKEYAVmc6uZpx1B43mhQ8jM3c'
        ])->json('POST', '/api/auth/deleteLabel',
        [
            "id" => 2,
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Label successfully deleted']);
    }

    //delete error
    public function test_IfGiven_WrongLabel_idAnd_ShouldValidate_AndReturnDeleteErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer J0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('POST', '/api/auth/deleteLabel',
        [
            "id" => 20,
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Invalid authorization token']);
    }
}
