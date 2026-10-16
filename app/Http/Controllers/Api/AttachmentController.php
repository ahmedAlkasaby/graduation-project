<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\AttachmentCollection;

class AttachmentController extends ApiHomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $attachments = Attachment::where('user_id',$this->authUserID())->paginate($this->limit);
            return $this->collectionResponse(new AttachmentCollection($attachments));
        } catch (Exception $e) {
            return $this->failedResponse($e);
        }
    }


    public function store(Request $request)
    {
        $rules = [
            'name'                 => 'required|string',
            'attachmentable_id'    => 'required',
            'attachmentable_type'  => 'required',
            'type'                 => 'nullable',
            'key'                  => 'required',
            'value'                => 'required',
        ];

        $validateRequest = $this->validateRequest($request, $rules);
        if(isset($validateRequest)){
            return $validateRequest;
        }
        $input = $request->all();
        foreach ($input as $key => $value) {
            $input[$key] = stripslashes(trim(filter_var($value, FILTER_SANITIZE_STRING)));
        }
        $input['user_id'] =$this->authUserID();
        $attachment = Attachment::create($input);
        $data = ['attachment' => new AttachmentResource($attachment)];
        return $this->successResponse($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attachment = Attachment::where('id',$id)->where('user_id',$this->authUserID())->first();
        $data = ['attachment' => new AttachmentResource($attachment)];
        return $this->successResponse($data);
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
        $attachment = Attachment::find($id);
        if (!empty($attachment) && $attachment->user_id == $this->authUserID()) {
            $rules = [
                'name'                 => 'required|string',
                'type'                 => 'nullable',
                'key'                  => 'required',
                'value'                => 'required',
            ];


        $validateRequest = $this->validateRequest($request, $rules);
        if(isset($validateRequest)){
            return $validateRequest;
        }
        $input = $request->all();
        foreach ($input as $key => $value) {
            $input[$key] = stripslashes(trim(filter_var($value, FILTER_SANITIZE_STRING)));
        }
        $input = Arr::except($input, array('user_id','attachmentable_id','attachmentable_type'));
        $attachment->update($input);
        $data = ['attachment' => new AttachmentResource($attachment)];
        return $this->successResponse($data);
        }else{
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
        $attachment = Attachment::find($id);
        if (!empty($attachment) && $attachment->user_id == $this->authUserID()) {
            $attachment->delete();
            return $this->successResponse();
        }else{
            return $this->errorResponse();
        }
    }
}
