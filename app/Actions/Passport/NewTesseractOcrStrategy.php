<?php

namespace App\Actions\Passport;

use thiagoalessio\TesseractOCR\TesseractOCR;

class NewTesseractOcrStrategy implements OcrStrategy
{
    public function extractText($image)
    {
        $path = storage_path('app/public/'.$image);

        $mrzImage = storage_path('app/tmp/mrz.png');

        // Ensure the tmp directory exists
        $tmpDir = dirname($mrzImage);
        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Detect MRZ using OpenCV
        $scriptPath = base_path('scripts/mrz_detector.py');
        $output = [];
        $return_var = 0;
        exec("python3 $scriptPath $path $mrzImage 2>&1", $output, $return_var);

        if ($return_var !== 0) {
            throw new \Exception("MRZ detection script failed with code $return_var: ".implode("\n", $output));
        }

        // Check if the MRZ image was created
        if (! file_exists($mrzImage)) {
            throw new \Exception("Failed to create MRZ image from $path. Script output: ".implode("\n", $output));
        }

        // OCR optimized for MRZ
        $text = (new TesseractOCR($mrzImage))
            ->lang('eng')
            ->psm(6)
            ->allowlist('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789<')
            ->run();

        $lines = array_values(array_filter(explode("\n", $text)));

        if (count($lines) < 2) {
            return null;
        }

        return $this->parseMRZ($lines[0], $lines[1]);
    }

    private function parseMRZ($line1, $line2)
    {

        $surname = trim(str_replace('<', ' ', substr($line1, 5, strpos($line1, '<<') - 5)));
        $given = trim(str_replace('<', ' ', substr($line1, strpos($line1, '<<') + 2)));

        return [

            'passport_number' => substr($line2, 0, 9),

            'nationality' => substr($line2, 10, 3),

            'birth_date' => $this->date(substr($line2, 13, 6)),

            'sex' => substr($line2, 20, 1),

            'expiry_date' => $this->date(substr($line2, 21, 6)),

            'surname' => $surname,

            'given_names' => $given,
        ];
    }

    private function date($d)
    {
        try {
            return \Carbon\Carbon::createFromFormat('ymd', $d)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
