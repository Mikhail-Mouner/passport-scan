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
        $imagePath = storage_path('app/public/'.$image);

        if (! $image || ! file_exists($imagePath) || ! is_file($imagePath)) {
            throw new \Exception("Image file not found or invalid: $imagePath");
        }

        if (! $this->pixlab->post('docscan', [  // Use POST for file upload
            'img' => $imagePath,  // Passport input image (now a local file)
            'type' => 'passport', // Type of document we are going to scan
        ], $imagePath)) {  // Pass the file path for upload
            echo $this->pixlab->get_error_message()."\n";
            exit;
        }

        return $this->pixlab->get_decoded_json();
    }
}
