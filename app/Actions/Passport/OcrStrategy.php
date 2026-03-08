<?php

namespace App\Actions\Passport;

interface OcrStrategy
{
    public function extractText($image);
}
