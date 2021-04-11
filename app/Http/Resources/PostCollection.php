<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($request->from == 'friends') {
            $posts = $request->user()->following()->accepted()->with(['followingPosts'])->get();
            $posts = $posts->pluck('followingPosts');
            $posts = $posts->flatten();
            $posts = $posts->sortByDesc('created_at');
        } else {
            $posts = $this->collection;
        }

        foreach ($posts as $post) {
            $post->image = asset($post->image);
        }

        $posts = $posts->forPage($request->page, $request->limit);
        $posts = $posts->all();

        return $posts;
    }
}
