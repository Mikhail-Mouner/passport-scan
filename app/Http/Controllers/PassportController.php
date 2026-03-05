<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PassportController extends Controller
{

    public function process(Request $request)
    {
        $imageName = $request->input('image');

        // Add your processing logic here
        // For now, just return the path

        $path = storage_path('app/public/' . $imageName);
        $text = (new TesseractOCR($path))
            ->run();

        return response()->json([
            'message' => 'Image processed successfully',
            'image' => $imageName,
            'text' => $text,
        ]);
    }
}
