<?php

namespace App\Http\Controllers\Settings\TermsAndCondition;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\TermsAndCondition;

class TermsAndConditionController extends Controller
{


    public function createSingletermsAndCondition(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            $validate = validator($request->all(), [
                "title" => "required|string",
                "subject" => "required|string",
            ]);
            if($validate->fails()){
                return response()->json(["error" => $validate->errors()->first()], 400);
            }
            $termsAndCondition = TermsAndCondition::create([
                "title" => $request->input("title"),
                "subject" => $request->input("subject"),
            ]);

             return $this->response($termsAndCondition->toArray(),201);
        }
        catch(Exception $e){
            return response()->json(["error" => $e->getMessage(), 500]);
        }
    }

    public function getAlltermsAndCondition(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            if($request->query('query')=== 'all'){
                $termsAndCondition = TermsAndCondition::where('status', 'true')
                ->orderBy('id', 'desc')
                ->get();
                 return $this->response($termsAndCondition->toArray());
            }
            elseif($request->query()){
                $pagination = getPagination($request->query());
                $termsAndCondition = TermsAndCondition::where('status', $request->query('status'))
                ->skip($pagination['skip'])
                ->take($pagination['limit'])
                ->get();

                 return $this->response($termsAndCondition->toArray());
            }
            else{
                return response()->json(["error" => "Invalid query parameter"], 400);
            }

        }
        catch(Exception $e){
            return response()->json(["error" => $e->getMessage(), 500]);
        }
    }

    public function getSingletermsAndCondition(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try{
            $termsAndCondition = TermsAndCondition::findOrFail($id);
            if($termsAndCondition){
                 return $this->response($termsAndCondition->toArray());
            }
            else{
                return response()->json(["error" => "terms and condition not found", 404]);
            }
        }
        catch(Exception $e){
            return response()->json(["error" => $e->getMessage(), 500]);
        }
    }

    public function updateSingletermsAndCondition(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try{
            $validate = validator($request->all(), [
                "title" => "required|string",
                "subject" => "required|string",
            ]);
            if($validate->fails()){
                return response()->json(["error" => $validate->errors()->first()], 400);
            }
            $termsAndCondition = TermsAndCondition::where('id', $id)->get();
            if(!$termsAndCondition){
                return response()->json(["error" => "terms and condition not found", 404]);
            }
            else{
                TermsAndCondition::where('id', $id)->update([
                    "title" => $request->input("title") ?? $termsAndCondition->title,
                    "subject" => $request->input("subject") ?? $termsAndCondition->subject,
                ]);
                return response()->json(["message" => "terms and condition updated successfully"], 200);
            }
        }
        catch(Exception $e){
            return response()->json(["error" => $e->getMessage(), 500]);
        }
    }

    public function deleteSingletermsAndCondition(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try{
            $termsAndCondition = TermsAndCondition::where('id', $id)->get();
            if(!$termsAndCondition){
                return response()->json(["error" => "terms and condition not found", 404]);
            }
            $termsAndCondition = TermsAndCondition::where('id', $id)->update([
                "status" => $request->status,
            ]);
            return response()->json(["message" => "terms and condition deleted successfully"], 200);
        }
        catch(Exception $e){
            return response()->json(["error" => $e->getMessage(), 500]);
        }
    }

}
