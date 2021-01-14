<?php

namespace App\Http\Controllers;

use App\City;
use App\Country;
use App\Province;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    /**
     * Countries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $countries = Country::query()->orderBy('name')->search($request->search)->paginate(10);

        return response()->json(['success' => true, 'data' => $countries]);
    }

    /**
     * Provinces
     *
     * @param Request $request
     * @param int $countryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function provinces(Request $request, $countryId)
    {
        $provinces = Province::query()
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->search($request->search);

        if (! $request->has('paginate') || $request->paginate === 'true') {
            $provinces = $provinces->paginate();
        } else {
            $provinces = $provinces->get();
        }

        return response()->json(['success' => true, 'data' => $provinces]);
    }

    /**
     * Cities
     *
     * @param Request $request
     * @param int $provinceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities(Request $request, $provinceId)
    {
        $cities = City::query()
            ->where('province_id', $provinceId)
            ->orderBy('name')
            ->search($request->search)
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $cities]);
    }
}
