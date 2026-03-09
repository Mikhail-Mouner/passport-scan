<?php

namespace App\Actions\Passport;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class GoogleVisionOcrStrategy implements OcrStrategy
{
    /**
     * Extracts text from an image using Google Cloud Vision.
     */
    public function extractText(string $image): array
    {
        // 1. Initialize the client
        // It automatically looks for the path in GOOGLE_APPLICATION_CREDENTIALS
        $imageAnnotator = new ImageAnnotatorClient();

        // 2. Load the image content
        $filePath = storage_path('app/public/' . $image);
        $fileContent = file_get_contents($filePath);

        try {
            // 3. Perform text detection
            $response = $imageAnnotator->textDetection($fileContent);
            $texts = $response->getTextAnnotations();

            if (count($texts) > 0) {
                // The first element [0] contains the entire block of text
                $fullText = $texts[0]->getDescription();

                return [
                    'status' => 'success',
                    'raw_text' => $fullText,
                    // You would need a parser here to find "Surname", etc.
                    'data' => $this->parsePassportData($fullText)
                ];
            }

            return ['status' => 'error', 'message' => 'No text found'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        } finally {
            $imageAnnotator->close();
        }
    }

    private function parsePassportData(string $text): array
    {
        // Simple example: Use Regex to find patterns in the raw text
        return [
            'detected_lines' => explode("\n", $text)
        ];
    }
}
