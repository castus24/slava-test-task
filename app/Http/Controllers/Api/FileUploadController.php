<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Jobs\ProcessExcelJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FileUploadController extends Controller
{
    public function upload(FileUploadRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = 'uploads/' . $originalName;

        if (Storage::exists($path)) {
            $filePath = $path;
        } else {
            $filePath = $file->storeAs('uploads', $originalName);
        }

        ProcessExcelJob::dispatch($filePath);

        return response()->json([
            'message' => 'File uploaded. Worker started'
        ], ResponseAlias::HTTP_OK);
    }
}
