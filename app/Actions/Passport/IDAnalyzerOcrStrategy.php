<?php

namespace App\Actions\Passport;

use IDAnalyzer\CoreAPI;
use Illuminate\Http\UploadedFile;

class IDAnalyzerOcrStrategy implements OcrStrategy
{
    private CoreAPI $coreApi;

    public function __construct()
    {
        $this->coreApi = new CoreAPI(
            config('services.idanalyzer.key'),
            config('services.idanalyzer.region', 'US')
        );

        // Configure for document scanning
        $this->coreApi->enableAuthentication(true, 'quick');
        $this->coreApi->setParameter('vault_save', false); // Don't save to vault unless needed
    }

    public function extractText($image)
    {
        if (is_string($image)) {
            $imagePath = storage_path('app/public/'.$image);
        } elseif ($image instanceof UploadedFile) {
            $imagePath = $image->getPathname();
        } else {
            throw new \Exception('Invalid image input type');
        }

        if (! $image || ! file_exists($imagePath) || ! is_file($imagePath)) {
            throw new \Exception("Image file not found or invalid: $imagePath");
        }

        try {
            // Scan the document
            $result = $this->coreApi->scan($imagePath);

            if (! isset($result['result'])) {
                throw new \Exception('Failed to extract data from document');
            }

            // Return structured data
            return [
                'success' => true,
                'authentication' => $result['authentication'] ?? null,
                'face' => $result['face'] ?? null,
                'raw_response' => $result,
            ];

        } catch (\Exception $e) {
            throw new \Exception('ID Analyzer API error: '.$e->getMessage());
        }
    }
}
