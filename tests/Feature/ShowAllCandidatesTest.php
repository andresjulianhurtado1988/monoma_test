<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\JwtAuth;
use App\Models\Candidate;

class ShowAllCandidatesTest extends TestCase
{
    /**
     * A basic feature test example2.
     *
     * @return void
     */
    
     public function test_example()
    {
        $response = $this->get('/');

        $candidates = Candidate::where('owner', 1)->get();
           
        $resp = response()->json([
            'code' => 200,
            'status' => 'success',
            'candidates' => $candidates
        ]);

        $response->assertStatus(200);
    }
}
