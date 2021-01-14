<?php

namespace App\Http\Controllers;

use App\Customer;
use App\PhoneBrand;
use App\PhoneType;
use App\Province;
use App\Service\AlertService;
use App\Store;
use App\Wholesaler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->orderBy('id', 'desc')
            ->assigned()
            ->search($request->search)
            ->with(['country'])
            ->paginate()
        ;

        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $phoneTypes = PhoneType::all();
        $phoneBrands = PhoneBrand::all();

        return view('customer.form', compact('phoneTypes', 'phoneBrands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $data = $request->all();

        $customer = new Customer($data);
        $customer->save();

        if ($data['store']['qty'] > 0) {
            $customer->setStore($data['store']);
        }

        if ($data['wholesaler']['qty'] > 0) {
            $customer->setWholesaler($data['wholesaler']);
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('customer.edit', $customer->uuid)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::query()
            ->uuid($id)
            ->with([
                'store.cities.province.country',
                'wholesaler.provinces.country',
                'wholesaler.phoneTypes',
                'wholesaler.phoneBrands',
                'country'
            ])
            ->firstOrFail();
        $phoneTypes = PhoneType::all();
        $phoneBrands = PhoneBrand::all();

        return view('customer.form', compact('customer', 'phoneTypes', 'phoneBrands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $data = $request->all();

        $customer = Customer::query()->uuid($id)->firstOrFail();
        $customer->fill($data);
        $customer->save();

        if ($data['store']['qty'] > 0) {
            $customer->setStore($data['store']);
        } else {
            Store::query()->where('customer_id', $customer->id)->delete();
        }

        if ($data['wholesaler']['qty'] > 0) {
            $customer->setWholesaler($data['wholesaler']);
        } else {
            Wholesaler::query()->where('customer_id', $customer->id)->delete();
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('customer.edit', $customer->uuid)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
