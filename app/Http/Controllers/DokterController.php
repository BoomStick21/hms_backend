<?php

namespace App\Http\Controllers;

use App\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return Dokter::with('tenagaMedis')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $dokter = new dokter;
      $dokter->no_pegawai = $request->input('no_pegawai');
      $dokter->spesialis = $request->input('spesialis');
      $dokter->save();

      return response($dokter, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $no_pegawai
     * @return \Illuminate\Http\Response
     */
    public function show($no_pegawai)
    {
      return Dokter::with('tenagaMedis')->findOrFail($no_pegawai);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $spesialis
     * @return \Illuminate\Http\Response
     */
    public function getAllDokterOfSpesialis($spesialis)
    {
      return Dokter::with('tenagaMedis')
        ->where('spesialis', '=', $spesialis)
        ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $no_pegawai
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $no_pegawai)
    {
      $dokter = Dokter::findOrFail($no_pegawai);
      $dokter->no_pegawai = $request->input('no_pegawai');
      $dokter->spesialis = $request->input('spesialis');
      $dokter->save();

      return response($dokter, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $no_pegawai
     * @return \Illuminate\Http\Response
     */
    public function destroy($no_pegawai)
    {
      Dokter::destroy($no_pegawai);
      return response('', 204);
    }

    /**
     * Sends id transaksi to assigned dokter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function periksa(Request $request)
    {
      $no_pegawai = $request->input('no_pegawai');
      $nama_poli = $request->input('nama_poli');
      $id_transaksi = $request->input('id_transaksi');
      $response = json_encode([
        'no_pegawai' => $no_pegawai,
        'nama_poli' => $nama_poli,
        'id_transaksi' => $id_transaksi
      ]);
      Redis::publish('periksa', json_encode([
        'no_pegawai' => $no_pegawai,
        'nama_poli' => $nama_poli,
        'id_transaksi' => $id_transaksi
      ]));
      return response($response, 200);
    }
}
