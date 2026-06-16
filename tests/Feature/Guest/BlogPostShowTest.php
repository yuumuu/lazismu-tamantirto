<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;

test('blog post show increments view count', function () {
    $category = BlogCategory::factory()->create(['name' => 'Test Category']);
    $post = BlogPost::factory()->create([
        'category_id' => $category->id,
        'author_id' => User::factory()->create()->id,
        'view_count' => 0,
    ]);

    $initialViews = $post->view_count;

    $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->get("/berita/{$post->slug}");

    $post->refresh();

    expect($post->view_count)->toBe($initialViews + 1);
});
