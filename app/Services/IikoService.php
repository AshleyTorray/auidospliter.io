<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IikoService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api-ru.iiko.services/api/1'; // Adjust if needed
        $this->apiKey = env('IIKO_API_KEY');
    }

    public function getOrders()
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json'
        ])->get("{$this->baseUrl}/orders/logs");
        if ($response->successful()) {
            return $response->json();    
        }
        else{
            Log::error("Failed to retrieve logs. Status code: " . $response->status());
        }
        
    }
    
    // Add more methods for different endpoints as needed...
}
