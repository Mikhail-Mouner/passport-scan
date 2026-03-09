<?php

namespace App\Http\Controllers;

use App\Actions\Passport\IDAnalyzerOcrStrategy;
use App\Actions\Passport\PixlabOcrStrategy;
use App\Actions\Passport\ProgressPassport;
use App\Actions\Passport\TesseractOcrStrategy;
use App\Actions\Passport\MindeeOcrStrategy;
use App\Actions\Passport\GoogleVisionOcrStrategy;
use App\Actions\Passport\NewTesseractOcrStrategy;
use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function process(Request $request)
    {
        $imageName = $request->input('image');

        $ocrStrategy = new IDAnalyzerOcrStrategy;
        // $ocrStrategy = new PixlabOcrStrategy;
        // $ocrStrategy = new TesseractOcrStrategy;
        // $ocrStrategy = new MindeeOcrStrategy;
        // $ocrStrategy = new GoogleVisionOcrStrategy;
        // $ocrStrategy = new PixlabOcrStrategy;
        // $ocrStrategy = new NewTesseractOcrStrategy;


        $progressPassport = new ProgressPassport($ocrStrategy);

        $result = $progressPassport->processImage($imageName);

        return response()->json([
            'message' => 'Image processed successfully',
            'image' => $imageName,
            'text' => $result,
        ]);
    }
}
