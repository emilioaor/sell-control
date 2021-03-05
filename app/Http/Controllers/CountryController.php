<?php

namespace App\Http\Controllers;

use App\City;
use App\Country;
use App\Province;
use App\Service\AlertService;
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
        $countries = Country::query()->orderBy('name')->search($request->search)->paginate();

        return view('country.index', compact('countries'));
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('country.form');
    }

    public function store(Request $request)
    {
        $country = new Country($request->all());
        $country->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('country.index')]);
    }

    /**
     * Edit
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $country = Country::query()->findOrFail($id);

        return view('country.form', compact('country'));
    }

    /**
     * Update
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $country = Country::query()->findOrFail($id);
        $country->fill($request->all());
        $country->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('country.edit', [$id])]);
    }

    /**
     * Check if country exists
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(Request $request)
    {
        $country = Country::query()->where('iso', $request->iso)->first();

        return response()->json(['success' => true, 'data' => $country]);
    }

    /**
     * Countries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countries(Request $request)
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
