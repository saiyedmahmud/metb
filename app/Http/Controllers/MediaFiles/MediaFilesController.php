<?php

namespace App\Http\Controllers\MediaFiles;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Images;
use App\Models\MediaFiles;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaFilesController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,svg,webp,pdf|max:2048',
                'files' => 'max:10',
            ]);

            if (!$request->hasFile('files')) {
                return $this->badRequest('No files found.');
            }

            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) {
                    return $this->badRequest('Invalid file.');
                }

                $fileName = $file->getClientOriginalName();
                $directory = 'uploads/' . Carbon::now()->format('Y/m/d');
                $filePath = $file->store($directory);

                if (!$filePath) {
                    return $this->badRequest('Failed to store one or more files.');
                }

                MediaFiles::create([
                    'fileName' => $fileName,
                    'filePath' => $filePath,
                    'fileType' => $file->getClientMimeType(),
                    'fileSize' => $file->getSize(),
                ]);
            }

            return $this->success('Media upload successful.');
        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

    public function show(Request $request): JsonResponse
    {
        try {
            if ($request->query('query') === 'all') {
                $media = MediaFiles::orderBy('id', 'desc')->get();
                return $this->response($media->toArray());
            } else if ($request->query('query') === 'search') {
                $request->validate([
                    'key' => 'required|string',
                ]);

                $media = MediaFiles::where('fileName', 'like', '%' . $request->query('key') . '%')->orderBy('id', 'desc')
                    ->get();
                $totalImageCount = MediaFiles::where('fileName', 'like', '%' . $request->query('key') . '%')->count();

                return $this->response([
                    'getAllImage' => $media->toArray(),
                    'totalImage' => $totalImageCount,
                ]);
            } else if ($request->query()) {
                $pagination = getPagination($request->query());
                $getAllMedia = MediaFiles::when($request->query('fileType'), function ($query) use ($request) {
                    return $query->where('fileType', $request->query('fileType'));
                })
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $totalImageCount = MediaFiles::when($request->query('fileType'), function ($query) use ($request) {
                    return $query->where('fileType', $request->query('fileType'));
                })->count();

                return $this->response([
                    'getAllImage' => $getAllMedia->toArray(),
                    'totalImage' => $totalImageCount,
                ]);
            }
            return $this->badRequest('invalid query');
        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('images');
            if ($ids === null) {
                return $this->badRequest('No file selected');
            }
            foreach ($ids as $id) {
                $media = MediaFiles::where('id', $id)->first();
                if ($media === null) {
                    return $this->badRequest('file not found');
                }

                $product = Product::where('productThumbnailImage', $media->id)->get();
                $gallery = Images::where('imageName', $media->id)->get();
                $settings = AppSetting::where('logo', $media->id)->get();

                if (!$product->isEmpty()) {
                    return $this->badRequest('This image is used in product thumbnail');
                }

                if (!$gallery->isEmpty()) {
                    return $this->badRequest('This image is used in gallery');
                }

                if (!$settings->isEmpty()) {
                    return $this->badRequest('This image is used in settings');
                }

                Storage::delete($media->filePath);
                MediaFiles::where('id', $id)->delete();
            }

            return $this->success('file deleted successfully');

        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

    //view single file from storage by id
    public function view($id): StreamedResponse|JsonResponse
    {
        try {
            $media = MediaFiles::where('id', $id)->first();
            if (!$media) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $filePath = $media->filePath;
            if (!Storage::exists($filePath)) {
                return response()->json(['error' => 'File does not exist.'], 404);
            }
            $file = Storage::get($filePath);
            $mimeType = Storage::mimeType($filePath);

            return response()->stream(function () use ($file) {
                ob_end_clean();
                echo $file;
            }, 200, ['Content-Type' => $mimeType]);
        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

}
