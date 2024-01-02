<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Import the Http facade
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonController extends Controller
{
    public function addDnsRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'name' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $type = $request->input('type');
        $name = $request->input('name');
        $content = $request->input('content');

        $cloudflareApiToken = env('CLOUDFLARE_API_TOKEN');
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $cfEmail = env('CF_EMAIL');

        $scriptUrl = 'http://localhost:5000/save-python-endpoint';

        try {
            $response = Http::post($scriptUrl, [
                'type' => $type,
                'name' => $name,
                'content' => $content,
                'api_token' => $cloudflareApiToken,
                'zone_id' => $zoneId,
                'email' => $cfEmail,
            ]);

            return response()->json(['success' => true, 'output' => $response->json()], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function runPython()
    {
        $cloudflareApiToken = env('CLOUDFLARE_API_TOKEN');
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $cfEmail = env('CF_EMAIL');

        $response = Http::withHeaders([
            'X-Auth-Email' => $cfEmail,
            'X-Auth-Key' => $cloudflareApiToken,
            'Content-Type' => 'application/json',
        ])->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records");

        // Check if the request was successful
        if ($response->successful()) {
            $dnsRecords = $response->json()['result'];
            return view('python-script.index', ['dnsRecords' => $dnsRecords]);
        } else {
            $error = 'Error fetching DNS records from Cloudflare API: ' . $response->status() . ' ' . $response->body();
            return view('python-script.index', ['error' => $error]);
        }
    }

    public function getDnsRecordDetailsById($id)
    {
        $cloudflareApiToken = env('CLOUDFLARE_API_TOKEN');
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $cfEmail = env('CF_EMAIL');

        $response = Http::withHeaders([
            'X-Auth-Email' => $cfEmail,
            'X-Auth-Key' => $cloudflareApiToken,
            'Content-Type' => 'application/json',
        ])->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$id}");

        // Check if the request was successful
        if ($response->successful()) {
            $dnsRecordDetails = $response->json()['result'];
            return response()->json($dnsRecordDetails);
        } else {
            return response()->json(['error' => 'Error fetching DNS record details from Cloudflare API'], $response->status());
        }
    }

    public function updateDnsRecord(Request $request)
    {
        $cloudflareApiToken = env('CLOUDFLARE_API_TOKEN');
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $cfEmail = env('CF_EMAIL');

        $recordId = $request->input('edit-record-id');

        $response = Http::withHeaders([
            'X-Auth-Email' => $cfEmail,
            'X-Auth-Key' => $cloudflareApiToken,
            'Content-Type' => 'application/json',
        ])->put("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$recordId}", [
            // Add other fields to update as needed
            'type' => $request->input('edit-type'),
            'name' => $request->input('edit-name'),
            'content' => $request->input('edit-content'),
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'DNS record updated successfully']);
        } else {
            return response()->json(['error' => 'Error updating DNS record'], $response->status());
        }
    }
}