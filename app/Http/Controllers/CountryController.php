<?php

namespace App\Http\Controllers;

use App\Country;
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
}
