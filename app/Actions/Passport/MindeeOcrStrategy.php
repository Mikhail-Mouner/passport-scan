<?php


namespace App\Actions\Passport;

use Mindee\ClientV2;
use Mindee\Input\PathInput;
use Mindee\Input\InferenceParameters;
use Mindee\Product\Extraction\Extraction;

class MindeeOcrStrategy implements OcrStrategy
{
    /**
     * Extracts structured data from a passport image using Mindee SDK.
     *
     * @param string $image The filename stored in storage/app/public/
     * @return array
     */
    public function extractText($image)
    {
        $apiKey = env('MINDEE_API_KEY');
        $mindeeClient = new ClientV2($apiKey);
        // 2. Load the file from disk
        $filePath = storage_path('app/public/' . $image);
        $inputSource = new PathInput($filePath);

        // 3. Set parameters using your specific Model ID from the image
        $modelId = "7a596709-ed80-4599-8459-71278947819e";
        $inferenceParams = new InferenceParameters(
            modelId: $modelId,
            rag: null,
            rawText: null,
            polygon: null,
            confidence: null
        );

        try {
            // 4. Process using polling (as shown in your image)
            // This waits for the asynchronous processing to finish
            $response = $mindeeClient->enqueueAndGetInference(
                $inputSource,
                $inferenceParams
            );

            // 5. Access the dynamic fields from your custom schema
            $fields = $response->inference->result->fields;

            $surname = $fields->getSimpleField('surnames')->value ?? 'Not Found';
            $givenNames = $fields->getSimpleField('given_names')->value ?? 'Not Found';
            $passportNumber = $fields->getSimpleField('passport_number')->value ?? 'Not Found';
            $dateOfBirth = $fields->getSimpleField('date_of_birth')->value ?? 'Not Found';
            $placeOfBirth = $fields->getSimpleField('place_of_birth')->value ?? 'Not Found';
            $issuing_country = $fields->getSimpleField('issuing_country')->value ?? 'Not Found';
            $nationality = $fields->getSimpleField('nationality')->value ?? 'Not Found';
            $date_of_expiry = $fields->getSimpleField('date_of_expiry')->value ?? 'Not Found';
            return [
                'status' => 'success',
                'data' => [
                    'surname' => $surname,
                    'given_names' => $givenNames,
                    'passport_number' => $passportNumber,
                    'date_of_birth' => $dateOfBirth,
                    'place_of_birth' => $placeOfBirth,
                    'issuing_country' => $issuing_country,
                    'nationality' => $nationality,
                    'date_of_expiry' => $date_of_expiry,
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
