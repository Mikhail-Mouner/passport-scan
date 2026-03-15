<?php

namespace App\Http\Controllers;

use App\Actions\Passport\GoogleVisionOcrStrategy;
use App\Actions\Passport\IDAnalyzerOcrStrategy;
use App\Actions\Passport\MindeeOcrStrategy;
use App\Actions\Passport\NewTesseractOcrStrategy;
use App\Actions\Passport\PixlabOcrStrategy;
use App\Actions\Passport\ProgressPassport;
use App\Actions\Passport\TesseractOcrStrategy;
use App\Models\Post;
use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function process(Request $request)
    {
        $id = $request->input('id');
        $post = Post::findOrFail($id);
        $imageName = $post->image;

        $currentStrategy = 'IDAnalyzerOcrStrategy';

        $ocrStrategy = match ($currentStrategy) {
            'IDAnalyzerOcrStrategy' => new IDAnalyzerOcrStrategy,
            'PixlabOcrStrategy' => new PixlabOcrStrategy,
            'TesseractOcrStrategy' => new TesseractOcrStrategy,
            'MindeeOcrStrategy' => new MindeeOcrStrategy,
            'GoogleVisionOcrStrategy' => new GoogleVisionOcrStrategy,
            'NewTesseractOcrStrategy' => new NewTesseractOcrStrategy,
            default => throw new \Exception('Invalid OCR strategy selected'),
        };

        $progressPassport = new ProgressPassport($ocrStrategy);

        $result = $progressPassport->processImage($imageName);

        $post->update([
            'data' => $result && ! empty($result['raw_response']) ? $result['raw_response'] : null,
        ]);

        return response()->json([
            'message' => 'Image processed successfully',
            'image' => $imageName,
            'text' => $result,
        ]);
    }
}
