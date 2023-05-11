<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\JwtAuth;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;

class ShowCandidateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $candidates = DB::table('candidate AS c')
        ->select('c.name', 'c.source', 'u.username')
        ->leftjoin('users AS u', 'u.id','=','c.owner')
        ->where('c.id', 1)
        ->first();
 
        if (is_object($candidates)) {
 
         $data = array('status' => 'success',
         'code' => 200,
         'candidates' =>  $candidates
             );
             }else {
                 $data = array('status' => 'error',
                 'code' => 400,
                 'message' =>  'El candidato no existe'
             );
     }
 

        $response->assertStatus(200);
    }
}
