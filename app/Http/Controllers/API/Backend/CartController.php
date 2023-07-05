<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\CartResource;
use Validator;

class CartController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::all();

        return $this->sendResponse(CartResource::collection($carts), 'Cart retrieved successfully.');
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
    public function store(StoreCartRequest $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $cart = Cart::create($input);

        return $this->sendResponse(new CartResource($cart), 'Cart created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->sendError('Cart not found.');
        }

        return $this->sendResponse(new CartResource($cart), 'Cart retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->sendError('Cart not found.');
        }

        $cart->name = $input['name'];
        $cart->detail = $input['detail'];
        $cart->save();

        return $this->sendResponse(new CartResource($cart), 'Cart updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->sendError('Cart not found.');
        }

        $cart->delete();

        return $this->sendResponse([], 'Cart deleted successfully.');
    }
}
