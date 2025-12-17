<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistrictModel;
use App\Helpers\ApiFormatter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    public function index()
    {
        $district = DistrictModel::orderBy('district_id', 'ASC')->get();
        return response()->json(ApiFormatter::createJson(200, "Get All District Success", $district));
    }

    // Get District by City ID
    public function getByCity($cityId)
    {
        $district = DistrictModel::where('city_id', $cityId)->get();

        if ($district->isEmpty()) {
            return response()->json(ApiFormatter::createJson(404, "District Not Found"));
        }

        return response()->json(ApiFormatter::createJson(200, "Get District by City Success", $district));
    }

    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'city_id' => 'required|numeric',
                'code' => 'required|max:10',
                'name' => 'required'
            ], [
                'city_id.required' => 'City ID is required',
                'code.required' => 'District Code is required',
                'name.required' => 'District Name is required'
            ]);

            if ($validator->fails()) {
                return response()->json(ApiFormatter::createJson(400, "Bad Request", $validator->errors()->all()));
            }

            $district = [
                'city_id' => $params['city_id'],
                'district_code' => $params['code'],
                'district_name' => $params['name']
            ];

            $data = DistrictModel::create($district);
            return response()->json(ApiFormatter::createJson(200, "Create District Success", $data));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function detail($id)
    {
        try {
            $district = DistrictModel::find($id);

            if (is_null($district)) {
                return response()->json(ApiFormatter::createJson(404, "District Not Found"));
            }

            return response()->json(ApiFormatter::createJson(200, "Get District Detail Success", $district));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $district = DistrictModel::find($id);

            if (is_null($district)) {
                return response()->json(ApiFormatter::createJson(404, "District Not Found"));
            }

            $params = $request->all();

            $validator = Validator::make($params, [
                'city_id' => 'required|numeric',
                'code' => 'required|max:10',
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(ApiFormatter::createJson(400, "Bad Request", $validator->errors()->all()));
            }

            $district->update([
                'city_id' => $params['city_id'],
                'district_code' => $params['code'],
                'district_name' => $params['name']
            ]);

            return response()->json(ApiFormatter::createJson(200, "Update District Success", $district->fresh()));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $district = DistrictModel::find($id);

            if (is_null($district)) {
                return response()->json(ApiFormatter::createJson(404, "District Not Found"));
            }

            $district->delete();

            return response()->json(ApiFormatter::createJson(200, "Delete District Success"));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }
}
