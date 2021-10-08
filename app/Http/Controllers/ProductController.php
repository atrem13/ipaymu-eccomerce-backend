<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as Image;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'List Data Product',
            'data'    => $products  
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'   => 'required',
            'description' => 'required',
            'purchase_price' => 'required',
            'sell_price' => 'required',
            'myimg' => 'nullable|mimes:jpeg,png|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();
        try{
            if ($request->hasFile('myimg')) {
                $path = public_path('upload/product/');
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }

                if ($request->file('myimg')->isValid()) {
                    $image = $request->file('myimg');
                    $image_name = time().'.'.$image->extension();
                    $img = Image::make($image->path());
                    $img->resize(500, 500, function ($const) {
                        $const->aspectRatio();
                    })->save($path.''.$image_name);

                    $request['img'] = $image_name;
                }
            }

            $product = Product::create($request->all());
            $digit = generateDigit($product->id);
            $code = 'Pro' . $digit;
            $product->update(['code' => $code]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data #'.$product->name.' Added Successfully.',
                'data'    => $product  
            ], 201);

        }catch(Exception $ex){
            DB::rollback();return response()->json([
                'success' => false,
                'message' => 'Product Failed to Save',
            ], 409);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Product',
            'data'    => $product 
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'description' => 'required',
            'purchase_price' => 'required',
            'sell_price' => 'required',
            'myimg' => 'nullable|mimes:jpeg,png|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();
        try{
            if ($request->hasFile('myimg')) {
                $path = public_path('upload/product/');
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }

                if ($request->file('myimg')->isValid()) {
                    $image = $request->file('myimg');
                    $image_name = time().'.'.$image->extension();
                    $img = Image::make($image->path());
                    $img->resize(500, 500, function ($const) {
                        $const->aspectRatio();
                    })->save($path.''.$image_name);

                    if($product->img != ''  && $product->img != null){
                        $file_old = $path.$product->img;
                        if(file_exists($file_old)){
                            unlink($file_old);
                        }
                    }

                    $request['img'] = $image_name;
                }
            }

            $product = Product::update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Updated Successfully.',
                'data'    => $product  
            ], 201);

        }catch(Exception $ex){
            DB::rollback();return response()->json([
                'success' => false,
                'message' => 'Product Failed to Update',
            ], 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            $path = public_path('upload/product/');
            if($product->img != ''  && $product->img != null){
                $file_old = $path.$product->img;
                unlink($file_old);
            }

            $product->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Post Deleted',
            ], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Post Not Found',
            ], 404);
        }
    }
}
