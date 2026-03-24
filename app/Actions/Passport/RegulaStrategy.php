<?php

namespace App\Actions\Passport;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class RegulaStrategy implements OcrStrategy
{
    private $configUrl;
    private $configKey;

    public function __construct()
    {
        $this->configUrl = config('services.regula.url') . '/api/process?logRequest=false';
        $this->configKey = config('services.regula.key');
    }

    public function extractText($image)
    {
        if (is_string($image)) {
            $imagePath = storage_path('app/public/' . $image);
        } elseif ($image instanceof UploadedFile) {
            $imagePath = $image->getRealPath();
        } else {
            throw new \Exception('Invalid image input type');
        }

        if (! $image || ! file_exists($imagePath) || ! is_file($imagePath)) {
            throw new \Exception("Image file not found or invalid: $imagePath");
        }

        $imageBase64 = base64_encode(file_get_contents($imagePath));


        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($this->configUrl, [
            "processParam" => [
                "scenario" => "FullAuth",
                "authParams" => [
                    "checkLiveness" => false
                ],
                "alreadyCropped" => false
            ],
            "List" => [
                [
                    "ImageData" => ['image' => $imageBase64],
                    "light" => 6,
                    "page_idx" => 0
                ]
            ]
        ]);

        return ['raw_response' => $this->parseRegulaResponse($response->json())];
    }

    public function parseRegulaResponse($response)
    {
        // $fields = collect(data_get($response, "ContainerList.List.$index.Text.fieldList", []));

        $list = collect(data_get($response, 'ContainerList.List', []));

        // 🔍 نجيب العنصر اللي فيه Text
        $textContainer = $list->first(function ($item) {
            return isset($item['Text']);
        });

        if (!$textContainer) {
            throw new \Exception('No text container found in the response');
        }

        $fields = collect(data_get($textContainer, 'Text.fieldList', []));

        return [
            'full_name' => optional($fields->firstWhere('fieldName', 'Surname And Given Names'))['value'] ?? null,
            'surname' => optional($fields->firstWhere('fieldName', 'Surname'))['value'] ?? null,
            'first_name' => optional($fields->firstWhere('fieldName', 'Given Names'))['value'] ?? null,
            'birth_date' => optional($fields->firstWhere('fieldName', 'Date of Birth'))['value'] ?? null,
            'age' => optional($fields->firstWhere('fieldName', 'Age'))['value'] ?? null,
            'passport_number' => optional($fields->firstWhere('fieldName', 'Document Number'))['value'] ?? null,
            'status' => optional($fields->firstWhere('fieldName', 'Document Status'))['value'] ?? null,
            'nationality_code' => optional($fields->firstWhere('fieldName', 'Nationality Code'))['value'] ?? null,
            'nationality' => optional($fields->firstWhere('fieldName', 'Nationality'))['value'] ?? null,
            'place_of_birth' => optional($fields->firstWhere('fieldName', 'Place of Birth'))['value'] ?? null,
            'issue_date' => optional($fields->firstWhere('fieldName', 'Date of Issue'))['value'] ?? null,
            'expiry_date' => optional($fields->firstWhere('fieldName', 'Date of Expiry'))['value'] ?? null,
            'sex' => optional($fields->firstWhere('fieldName', 'Sex'))['value'] ?? null,
        ];
    }
}
