<?php

namespace App\Http\Controllers;

use App\Traits\Cruds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{

    use Cruds;
    /**
     * Strip image
     * delete metadata
     * create image file using imagick
     * resize image
     */
    public function upload(Request $request){
        Log::info("try to upload");
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            LOG::info("Validator failed");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        Log::info("Valid file");
        $image = $request->file('image');
        $filename = uniqid() . '.' . $request->file('image')->extension();
        $image->storeAs("/uploads", $filename);

        return response()->json($this->saveImagen($filename, $request->user()->id ));
    }

    /**
     * Proteger contra traverse directory
     */
    public function show($filename){
        $path = storage_path("app/uploads/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            return response()->json(null, 404);
        }
    }

    public function delete($filename){
        if (Storage::disk("uploads")->exists($filename)) {
            Storage::disk("uploads")->delete($filename);
            return response()->json(null, 200);
        } else {
            return response()->json(null, 404);
        }
    }
}
