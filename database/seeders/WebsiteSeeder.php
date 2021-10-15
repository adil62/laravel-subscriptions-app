<?php

use App\Models\Website;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Website::create([
      'name' => Str::random(10)
    ]);
  }
}
