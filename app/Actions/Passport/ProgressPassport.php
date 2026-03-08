<?php

namespace App\Actions\Passport;

class ProgressPassport
{
    private OcrStrategy $ocrStrategy;

    public function __construct(OcrStrategy $ocrStrategy)
    {
        $this->ocrStrategy = $ocrStrategy;
    }

    public function processImage($image)
    {
        return $this->ocrStrategy->extractText($image);
    }
}
