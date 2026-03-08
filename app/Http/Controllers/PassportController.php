<?php

namespace App\Http\Controllers;

use App\Actions\Passport\PixlabOcrStrategy;
use App\Actions\Passport\ProgressPassport;
use App\Actions\Passport\TesseractOcrStrategy;
use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function process(Request $request)
    {
        $imageName = $request->input('image');


        $ocrStrategy = new PixlabOcrStrategy;
        // $ocrStrategy = new TesseractOcrStrategy;
        $progressPassport = new ProgressPassport($ocrStrategy);

        $text = $progressPassport->processImage($imageName);

        return response()->json([
            'message' => 'Image processed successfully',
            'image' => $imageName,
            'text' => $text,
        ]);
    }
}
