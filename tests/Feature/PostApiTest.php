<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Website;
use App\Jobs\SendBulkPostEmail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_post()
    {
        Queue::fake();

        // create a website
        $website = new Website();
        $website->name = 'website1';
        $website->save();

        $response = $this->postJson('/api/posts', [
            'website_id' => $website->id, 
            'title' => 'test post1',
            'description' => 'test post description'
        ]);
        
        $responseContent = json_decode($response->getContent());
       
        Queue::assertPushed(SendBulkPostEmail::class, function ($job) use ($responseContent) {
            return $job->post->id === $responseContent->post->id;
        });

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'post'
            ]);
    }

    public function test_post_api_fails_on_invalid_website_id() 
    {
        Queue::fake();

        $nonExistingWebsiteId = 3333;

        $response = $this->postJson('/api/posts', [
            'website_id' => $nonExistingWebsiteId, 
            'title' => 'test post1',
            'description' => 'test post description'
        ]);
        
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }
}