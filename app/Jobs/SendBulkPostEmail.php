<?php

namespace App\Jobs;

use App\Models\User;
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
        $users = User::all();

        $users->each(function ($user) {
            Mail::send(
                'mail.post_created',
                ['post' => $this->post],
                function ($message) use ($user) {
                    $message
                        ->to($user->email, $user->name)
                        ->subject('New Post');
                }
            );
        });
    }
}
