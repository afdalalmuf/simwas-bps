<?php

namespace App\Http\Controllers;

use App\Models\SpjVerification;
use App\Http\Requests\StoreSpjVerificationRequest;
use App\Http\Requests\UpdateSpjVerificationRequest;

class SpjVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSpjVerificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSpjVerificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SpjVerification  $spjVerification
     * @return \Illuminate\Http\Response
     */
    public function show(SpjVerification $spjVerification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SpjVerification  $spjVerification
     * @return \Illuminate\Http\Response
     */
    public function edit(SpjVerification $spjVerification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSpjVerificationRequest  $request
     * @param  \App\Models\SpjVerification  $spjVerification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSpjVerificationRequest $request, SpjVerification $spjVerification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SpjVerification  $spjVerification
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpjVerification $spjVerification)
    {
        //
    }

    public function getVerificationsBySpjDiklatId($spjDiklatId)
    {
        $verifications = SpjVerification::where('spj_diklat_id', $spjDiklatId)->get();
        return response()->json($verifications);
    }
}
