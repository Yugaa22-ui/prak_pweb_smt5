<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Helpers\ApiFormatter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index()
    {
        $city = CityModel::orderBy('city_id', 'ASC')->get();
        return response()->json(ApiFormatter::createJson(200, "Get All City Success", $city));
    }

    // Get City by Province ID
    public function getByProvince($provinceId)
    {
        $city = CityModel::where('province_id', $provinceId)->get();

        if ($city->isEmpty()) {
            return response()->json(ApiFormatter::createJson(404, "City Not Found"));
        }

        return response()->json(ApiFormatter::createJson(200, "Get City by Province Success", $city));
    }

    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'province_id' => 'required|numeric',
                'code' => 'required|max:10',
                'name' => 'required'
            ], [
                'province_id.required' => 'Province ID is required',
                'code.required' => 'City Code is required',
                'name.required' => 'City Name is required'
            ]);

            if ($validator->fails()) {
                return response()->json(ApiFormatter::createJson(400, "Bad Request", $validator->errors()->all()));
            }

            $city = [
                'province_id' => $params['province_id'],
                'city_code' => $params['code'],
                'city_name' => $params['name']
            ];

            $data = CityModel::create($city);
            return response()->json(ApiFormatter::createJson(200, "Create City Success", $data));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function detail($id)
    {
        try {
            $city = CityModel::find($id);

            if (is_null($city)) {
                return response()->json(ApiFormatter::createJson(404, "City Not Found"));
            }

            return response()->json(ApiFormatter::createJson(200, "Get City Detail Success", $city));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $city = CityModel::find($id);

            if (is_null($city)) {
                return response()->json(ApiFormatter::createJson(404, "City Not Found"));
            }

            $params = $request->all();

            $validator = Validator::make($params, [
                'province_id' => 'required|numeric',
                'code' => 'required|max:10',
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(ApiFormatter::createJson(400, "Bad Request", $validator->errors()->all()));
            }

            $city->update([
                'province_id' => $params['province_id'],
                'city_code' => $params['code'],
                'city_name' => $params['name']
            ]);

            return response()->json(ApiFormatter::createJson(200, "Update City Success", $city->fresh()));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $city = CityModel::find($id);

            if (is_null($city)) {
                return response()->json(ApiFormatter::createJson(404, "City Not Found"));
            }

            $city->delete();

            return response()->json(ApiFormatter::createJson(200, "Delete City Success"));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, "Internal Server Error", $e->getMessage()));
        }
    }
}
