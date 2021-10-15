<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\WebsiteUser;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    private function subscriptionExists($userId, $websiteId) {
        return WebsiteUser::where('user_id', $userId)
        ->where('website_id', $websiteId)
        ->first();
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'website_id' => 'required|numeric|exists:websites,id',
        ]);

        if ($this->subscriptionExists($request->user_id, $request->website_id)) {
            return response()->json([
                'message' => 'Subscription already exists'
            ], 422);
        } 

        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $user = User::find($request->user_id);
            $user->websites()->attach($request->website_id);

            return response()->json([
                'message' => 'Website subscription successfull',
                'user' => $user
            ]);
        } catch(\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Website subscription failed',
            ], 400);
        }
    }
}