<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Events\PostCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'website_id' => 'required|numeric|exists:websites,id',
            'description' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $post = new Post;
            $post->website_id = $request->website_id;
            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();
    
            PostCreated::dispatch($post);
            
            return response()->json([
                'message' => 'Successfully created post',
                'post' => $post
            ]);
        } catch(\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed creating post',
            ], 400);
        }
    }
}