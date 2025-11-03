<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/test-upload', function() {
    return view('test-upload');
});

Route::post('/test-upload', function(Request $request) {
    Log::info('=== TEST UPLOAD RECEIVED ===');
    Log::info('Has file "test_image":', ['has' => $request->hasFile('test_image')]);
    Log::info('All files:', ['files' => $request->allFiles()]);
    Log::info('All input:', ['input' => $request->all()]);
    
    if ($request->hasFile('test_image')) {
        $file = $request->file('test_image');
        Log::info('File details:', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);
        
        $path = $file->store('test-uploads', 'public');
        Log::info('File saved:', ['path' => $path]);
        
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully!',
            'path' => $path
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'No file received'
    ]);
});
