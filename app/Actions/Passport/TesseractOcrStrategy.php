<?php

namespace App\Actions\Passport;

use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractOcrStrategy implements OcrStrategy
{
    public function extractText($image)
    {
        $path = storage_path('app/public/'.$image);

        return (new TesseractOCR($path))->run();
    }
}
