<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Pembayaran;
use App\Klaim;
use App\Transaksi;
use App\Asuransi;
use App\Tindakan;
use App\ObatTebusItem;
use App\PemakaianKamarRawatInap;

class PembayaranController extends Controller
{
    private function getPembayaran($id = null)
    {
        if (isset($id)) {
            return Pembayaran::with(['transaksi.pasien', 'tindakan.daftarTindakan', 'klaim'])->findOrFail($id);
        } else {
            return Pembayaran::with('transaksi.pasien')->get();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'allPembayaran' => $this->getPembayaran()
        ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->input('pembayaran');
        $pembayaran = new Pembayaran;
        $pembayaran->id_transaksi = $payload['id_transaksi'];
        $pembayaran->harga_bayar = $payload['harga_bayar'];
        $pembayaran->metode_bayar = $payload['metode_bayar'];
        $pembayaran->save();

        if (isset($payload['tindakan']) && count($payload['tindakan']) > 0) {
            $arrTindakan = $payload['tindakan'];
            foreach ($arrTindakan as $value) {
                $tindakan = Tindakan::findOrFail($value);
                $tindakan->id_pembayaran = $pembayaran->id;
                $tindakan->save();
            }
        }

        if (isset($payload['obatTebus']) && count($payload['obatTebus']) > 0) {
            $arrObatTebus = $payload['obatTebus'];
            foreach ($arrObatTebus as $value) {
                $obatTebus = ObatTebusItem::findOrFail($value);
                $obatTebus->id_pembayaran = $pembayaran->id;
                $obatTebus->save();
            }
        }

        if (isset($payload['kamarRawatInap']) && count($payload['kamarRawatInap']) > 0) {
            $arrKamarRawatInap = $payload['kamarRawatInap'];
            foreach ($arrKamarRawatInap as $value) {
                $kamarRawatInap = PemakaianKamarRawatInap::findOrFail($value);
                $kamarRawatInap->id_pembayaran = $pembayaran->id;
                $kamarRawatInap->save();
            }
        }

        try {
            if ($pembayaran->metode_bayar != 'tunai' && $pembayaran->metode_bayar != 'bpjs') {
                $transaksi = Transaksi::findOrFail($pembayaran->id_transaksi);
                $asuransi = DB::table('asuransi')->select('id')->where([
                    ['nama_asuransi', '=', $pembayaran->metode_bayar],
                    ['id_pasien', '=', $transaksi->id_pasien]
                ])->first();

                $klaim = new Klaim;
                $klaim->id_pembayaran = $pembayaran->id;
                $klaim->id_asuransi = $asuransi->id;
                $klaim->status = 'processed';
                $klaim->save();
            }
        }
        catch(\Exception $e) {
            Pembayaran::destroy($pembayaran->id);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'pembayaran' => $pembayaran
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
        return response()->json([
            'pembayaran' => $this->getPembayaran($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $payload = $request->input('pembayaran');
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->harga_bayar = $payload['harga_bayar'];
        $pembayaran->metode_bayar = $payload['metode_bayar'];
        $pembayaran->save();

        return response()->json([
            'pembayaran' => $pembayaran
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
        Pembayaran::destroy($id);
    }
}
