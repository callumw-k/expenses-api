<?php

namespace App\Services;

use App\DTOs\ReceiptDataDto;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ImageParsingService
{

    static public function ReceiptFromImage(string $imagePath): ?ReceiptDataDto
    {
        $storage_path = Storage::path($imagePath);
        $text = self::HandleOcr($storage_path);
        if (strlen($text) === 0) {
            return null;
        }
        Log::info("Text from OCR received: " . $text);
        return self::parseReceiptStringWithClaude($text);
    }

    static function HandleOcr(string $path): string
    {
        try {
            return (new TesseractOCR($path))->run();
        } catch (TesseractOcrException $e) {
            Log::error($e->getMessage());
            return '';
        }
    }

    static function parseReceiptStringWithClaude(string $messageText): ?ReceiptDataDto
    {
        $claudeEnv = config('app.claude_key');

        Log::debug('Claude env ' . $claudeEnv);

        try {

            $response = Http::withHeaders([
                'x-api-key' => $claudeEnv,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-5-sonnet-20240620',
                'max_tokens' => 1024,
                'system' => "You purpose is to parse and return the total amount, tax and store or merchant name from receipts. You will return a JSON blob with the following keys. Total amount has a key of total_amount, merchant or store name has a key of merchant_name, and tax has a key of tax_amount. If any one of these values do not exist in the text, return null as it's value. You only communicate via the specified json structure.",
                'messages' => [
                    ['role' => 'user', 'content' => $messageText]
                ],
            ]);

            $data = $response->json();
            $content = $data['content'][0]['text'] ?? null;
            if (!$content) {
                Log::debug('Content not found in the response');
                return null;
            }

            $parsedContent = ReceiptDataDto::fromJson($content);

            if (!$parsedContent) {
                Log::debug('Failed to parse content into ReceiptData');
                return null;
            }

            Log::debug('Parsed content: ' . print_r($parsedContent, true));

            return $parsedContent;
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
