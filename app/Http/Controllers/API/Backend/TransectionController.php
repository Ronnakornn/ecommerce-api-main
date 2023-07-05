<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Requests\StoreTransectionRequest;
use App\Http\Requests\UpdateTransectionRequest;
use App\Models\Transection;
use App\Http\Controllers\API\ApiBaseController;
use Validator;
use App\Http\Resources\TransectionResource;

class TransectionController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transections = Transection::all();

        return $this->sendResponse(TransectionResource::collection($transections), 'Transection retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransectionRequest $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $transection = Transection::create($input);

        return $this->sendResponse(new TransectionResource($transection), 'Transection created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transection = Transection::find($id);

        if (is_null($Transection)) {
            return $this->sendError('Transection not found.');
        }

        return $this->sendResponse(new TransectionResource($transection), 'Transection retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transection $transection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransectionRequest $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $transection = Transection::find($id);

        if (is_null($Transection)) {
            return $this->sendError('Transection not found.');
        }

        $transection->name = $input['name'];
        $transection->detail = $input['detail'];
        $transection->save();

        return $this->sendResponse(new TransectionResource($Transection), 'Transection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transection = Transection::find($id);

        if (is_null($Transection)) {
            return $this->sendError('Transection not found.');
        }

        $transection->delete();

        return $this->sendResponse([], 'Transection deleted successfully.');
    }
}
