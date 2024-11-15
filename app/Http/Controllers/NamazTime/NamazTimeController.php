<?php

namespace App\Http\Controllers\NamazTime;

use App\Models\NamazTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NamazTimeController extends Controller
{
    public function createNamazTime(Request $request)
    {
        try {
            $namazTime = NamazTime::where('name', $request->name)
                ->first();
            if ($namazTime) {
                return response()->json(['message' => 'Namaz Time already exists'], 400);
            }

            $namazTime = new NamazTime();
            $namazTime->name = $request->name;
            $namazTime->time = $request->time;
            $namazTime->save();

            $converted = arrayKeysToCamelCase($namazTime);
            return response()->json($converted, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAllNamazTimes(Request $request)
    {
        try {
            if ($request->query('query') === 'all') {
                    $namazTimes = NamazTime::all();
                    $converted = arrayKeysToCamelCase($namazTimes);
                    return response()->json($converted, 200);
            } else if ($request->query('query') === 'search') {
                $pagination = getPagination($request->query());
                $namazTimes = NamazTime::where('name', 'like', '%' . $request->query('key'). '%')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $total = NamazTime::where('name', 'like', '%' . $request->query('key'). '%')
                    ->count();

                $result = [
                    'getAllNamazTime' => arrayKeysToCamelCase($namazTimes),
                    'totalNamazTime' => $total
                ];
                return response()->json($result, 200);
            } else if ($request->query()) {
                $pagination = getPagination($request->query());
                $namazTimes = NamazTime::when($request->query('name'), function ($query) use ($request) {
                    $query->whereIn('name', explode(',', $request->query('name')));
                })->when($request->query('status'), function ($query) use ($request) {
                    $query->whereIn('status', explode(',', $request->query('status')));
                })
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $total = NamazTime::when($request->query('name'), function ($query) use ($request) {
                    $query->whereIn('name', explode(',', $request->query('name')));
                })->when($request->query('status'), function ($query) use ($request) {
                    $query->whereIn('status', explode(',', $request->query('status')));
                })
                    ->count();

                $result = [
                    'getAllNamazTime' => arrayKeysToCamelCase($namazTimes),
                    'totalNamazTime' => $total
                ];

                return response()->json($result, 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getSingleNamazTime($id)
    {
        try {
            $namazTime = NamazTime::find($id);
            if (!$namazTime) {
                return response()->json(['message' => 'Namaz Time not found'], 404);
            }

            $converted = arrayKeysToCamelCase($namazTime->toArray());
            return response()->json($converted, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateNamazTime(Request $request, $id)
    {
        try {
            $namazTime = NamazTime::find($id);
            if (!$namazTime) {
                return response()->json(['message' => 'Namaz Time not found'], 404);
            }

            $namazTime->name = $request->name ?? $namazTime->name;
            $namazTime->time = $request->time ?? $namazTime->time;
            $namazTime->save();

            $converted = arrayKeysToCamelCase($namazTime->toArray());
            return response()->json($converted, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteNamazTime(Request $request, $id)
    {
        try {
            $namazTime = NamazTime::find($id);
            if (!$namazTime) {
                return response()->json(['message' => 'Namaz Time not found'], 404);
            }

            $namazTime->status = $request->status;
            $namazTime->save();
            return response()->json(['message' => 'Namaz Time deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


                    
}
