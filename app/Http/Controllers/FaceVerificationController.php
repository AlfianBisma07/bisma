<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaceVerificationController extends Controller
{
    public function index()
    {
        return view('face_verification');
    }

    public function verify(Request $request)
{
    try {
        $response = Http::attach(
            'image', $request->file('image')->get(), 'image'
        )->post('http://localhost:5000/verify');

        $responseData = $response->json();
        return response()->json($responseData);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
