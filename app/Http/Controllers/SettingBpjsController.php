<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingBpjs;

class SettingBpjsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SettingBpjs::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->input('setting_bpjs');
        $setting = new SettingBpjs;
        $setting->tarif_rs = $payload['tarif_rs'];
        $setting->kd_tarif_rs = $payload['kd_tarif_rs'];
        $setting->coder_nik = $payload['coder_nik'];
        $setting->add_payment_pct = $payload['add_payment_pct'];
        $setting->save();

        return response()->json([
            'setting_bpjs' => $setting->toJson()
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return SettingBpjs::findOrFail($id);
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
        $payload = $request->input('setting_bpjs');
        $setting = SettingBpjs::findOrFail($id);
        $setting->tarif_rs = $payload['tarif_rs'];
        $setting->kd_tarif_rs = $payload['kd_tarif_rs'];
        $setting->coder_nik = $payload['coder_nik'];
        $setting->add_payment_pct = $payload['add_payment_pct'];
        $setting->save();

        return response()->json([
            'setting_bpjs' => $setting->toJson()
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return SettingBpjs::destroy($id);
    }
}
