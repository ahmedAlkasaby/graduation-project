<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends ApiHomeController
{
     public function index(Request $request)
    {
        try {
            $readAll = (bool) $request->input('read_all', 0);
            $readOnly = (bool) $request->input('read_only', 0);


            $notifications = DatabaseNotification::latest()
                ->where('notifiable_id', $this->authUserID())
                ->where('notifiable_type', 'users')
                ->when($request->filled('read'), fn ($query) => $request->boolean('read')
                    ? $query->whereNotNull('read_at')
                    : $query->whereNull('read_at'))
                ->paginate($this->limit);

            if ($readAll) {
                $this->authUser()->unreadNotifications->markAsRead();

                if ($readOnly) {
                    return $this->successResponse();
                }
            }

            return $this->collectionResponse(
                new NotificationCollection($notifications)
            );

        } catch (Exception $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return $this->successResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = DatabaseNotification::find($id);
        if (! empty($notification)) {
            $this->authUser()->notifications->where('id', $id)->markAsRead();
            $notification = DatabaseNotification::where('id', $id)->where('notifiable_id', $this->authUserID())->where('notifiable_type', 'users')->first();
            $data = ['notification' => new NotificationResource($notification)];
            return $this->successResponse($data);
        } else {
            return $this->errorResponse();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $notification = DatabaseNotification::find($id);
        if (! empty($notification)) {
            return $this->successResponse();
        } else {
            return $this->errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = DatabaseNotification::find($id);
        if (! empty($notification) && $notification->notifiable_id == $this->authUserID() && $notification->notifiable_type == 'users') {
            $notification->delete();

            return $this->successResponse();
        } else {
            return $this->errorResponse();
        }
    }
}
