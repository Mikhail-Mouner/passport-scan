<?php

namespace App\Actions\Passport;

use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractOcrStrategy implements OcrStrategy
{
    public function extractText($image)
    {
        // 1. Build the full path to the image in your storage
        $path = storage_path('app/public/' . $image);

        // 2. Initialize the library
        $tesseract = new TesseractOCR($path);

        // 3. Point directly to the .exe file you just downloaded
        // (Assuming you used the default installation path)
        $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
        try {
            // 4. Run the OCR
            return $tesseract->run();
        } catch (\Exception $e) {
            return "OCR Error: " . $e->getMessage();
        }
    }
}
