<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Models\UserSentPost;
use Illuminate\Bus\Queueable;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendBulkPostEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $post = Post::with('website', 'website.users')->where('id', $this->post->id)->first();
        $userSentPosts = [];    

        $post?->website?->users?->each(function ($user) use($post, &$userSentPosts) {
            $postPreviouslySent = UserSentPost::where('post_id', $post->id)->where('user_id', $user->id)->first();

            if ($postPreviouslySent) {
                return false;
            }

            Mail::send(
                'mail.post',
                ['post' => $post],
                function ($message) use ($user) {
                    $message
                        ->to($user->email, $user->name);
                }
            );

            array_push($userSentPosts, [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'created_at' => now()
            ]);
        });

        if (count($userSentPosts) > 0) {
            UserSentPost::insert($userSentPosts);
        }
    }
}