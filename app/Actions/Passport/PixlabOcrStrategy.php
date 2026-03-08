<?php

namespace App\Actions\Passport;

use App\Libs\Pixlab;

class PixlabOcrStrategy implements OcrStrategy
{
    private Pixlab $pixlab;

    public function __construct()
    {
        // Assuming the API key is set somewhere, maybe in config
        $this->pixlab = new Pixlab(config('services.pixlab.key'));
    }

    public function extractText($image)
    {
        $imageUrl = url('storage/'.basename($image));

        if (! $this->pixlab->get('ocr', ['img' => $imageUrl])) {
            throw new \Exception('Pixlab OCR failed: '.$this->pixlab->get_error_message());
        }
        dd($this->pixlab->json);

        return $this->pixlab->json;
    }
}
