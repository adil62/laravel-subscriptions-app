<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Jobs\SendBulkPostEmail;
use Illuminate\Console\Command;

/**
 * When executed :
 * takes and sends a random post to every user.
 */
class SendRandomPostEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:random-post-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'takes and sends a random post to every user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Post::inRandomOrder()->limit(1)->first();

        SendBulkPostEmail::dispatch($post);

        return Command::SUCCESS;
    }
}
