<?php

namespace App\Http\Controllers\HR\Announcement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Announcement;
use Exception;

//
class AnnouncementController extends Controller
{
    // create a single announcement controller method
    public function createSingleAnnouncement(Request $request): jsonResponse
    {
        try {
            $createdAnnouncement = Announcement::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ]);

            return $this->response($createdAnnouncement->toArray());
        } catch (Exception $err) {
            return response()->json(['error' => 'An error occurred during creating announcement. Please try again later.'], 500);
        }
    }

    // get all announcement data controller method
    public function getAllAnnouncement(Request $request): jsonResponse
    {
        if ($request->query('query') === 'all') {
            try {
                $allAnnouncement = Announcement::orderBy('id', 'desc')->where('status', 'true')->get();
                return $this->response($allAnnouncement->toArray());
            } catch (Exception $err) {
                return response()->json(['error' => 'An error occurred during getting announcement. Please try again later.'], 500);
            }
        } else if ($request->query('status') === 'true') {
            try {
                $pagination = getPagination($request->query());
                $allAnnouncement = Announcement::where('status', 'true')
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $allAnnouncementCount = Announcement::where('status', "true")
                    ->count();

                return $this->response([
                    'getAllAnnouncement' => $allAnnouncement->toArray(),
                    'totalAnnouncement' => $allAnnouncementCount,
                ]);
            } catch (Exception $err) {
                return response()->json(['error' => 'An error occurred during getting announcement. Please try again later.'], 500);
            }
        } else if ($request->query()) {
            $pagination = getPagination($request->query());
            try {
                $allAnnouncement = Announcement::where('status', $request->query('status'))
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $allAnnouncementCount = Announcement::where('status', $request->query('status'))
                    ->count();

                return $this->response([
                    'getAllAnnouncement' => $allAnnouncement->toArray(),
                    'totalAnnouncement' => $allAnnouncementCount,
                ]);
            } catch (Exception $err) {
                return response()->json(['error' => 'An error occurred during getting announcement. Please try again later.'], 500);
            }
        } else {
            return response()->json(['message' => 'Invalid Query'], 400);
        }
    }

    // get single announcement data controller method
    public function getSingleAnnouncement(Request $request, $id): jsonResponse
    {
        try {
            $singleAnnouncement = Announcement::findOrFail($id);
            return $this->response($singleAnnouncement->toArray());
        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

    // update announcement data controller method
    public function updateSingleAnnouncement(Request $request, $id): jsonResponse
    {
        try {
            $updatedAnnouncement = Announcement::where('id', $id)->update($request->all());

            if (!$updatedAnnouncement) {
                return response()->json(['error' => 'Failed to update announcement'], 404);
            }
            return response()->json(['message' => 'Announcement Updated Successfully'], 200);
        } catch (Exception $err) {
            return $this->badRequest($err->getMessage());
        }
    }

    // delete an announcement data controller method
    public function deleteAnnouncement($id): JsonResponse
    {
        try {
            $announcement = Announcement::findOrFail($id); // Ensures the announcement exists, or throws an exception
            $announcement->delete();
    
            return response()->json(['message' => 'Announcement deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Announcement not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
