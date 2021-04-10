<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PostCollection(Post::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:png,jpg,jpeg'],
            'caption' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $file_name = date('YmdHis') . '.' . $ext;
        $upload = $file->storeAs('public/posts/' . $request->user()->id, $file_name);
        if (!$upload) {
            return response()->json(['message' => 'Image not uploaded.'], 500);
        }
        $request['image'] = 'storage/posts/' . $request->user()->id . '/' . $file_name;

        $post = $request->user()->posts()->create($request->except('file'));
        if (!$post) {
            return response()->json(['message' => 'Upload failed.'], 500);
        }

        $post['message'] = 'Upload success.';

        return response()->json($post, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
