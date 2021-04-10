<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $following = $request->user()->following()->get();

        return response()->json(['data' => $following], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['status'] = 'PENDING';

        $validator = Validator::make($request->all(), [
            'to_id' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $friend = $request->user()->following()->create($request->all());
        if (!$friend) {
            return response()->json(['message' => 'Following failed.'], 500);
        }
        $friend['message'] = 'Following success.';

        return response()->json($friend, 200);
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
        $request['status'] = 'ACCEPT';

        $validator = Validator::make($request->all(), [
            'status' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $friend = Friend::find($id);
        $success = $friend->update($request->only('status'));
        if (!$success) {
            return response()->json(['message' => 'Accept follow request failed.'], 500);
        }

        return response()->json($friend, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $friend = Friend::find($id);
        if (!$friend->delete()) {
            return response()->json(['message' => 'Unfollow / delete follow request failed.'], 500);
        }

        return response()->json(['message' => 'Unfollow / delete follow request success.'], 200);
    }
}
