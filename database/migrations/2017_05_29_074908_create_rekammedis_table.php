<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekammedisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pasien')->unsigned();
            $table->dateTime('tanggal_waktu');
            $table->foreign('id_pasien')->references('id')->on('pasien')->onDelete('cascade');
            $table->integer('id_dokter')->unsigned();
            $table->jsonb('hasil_pemeriksaan');
            $table->jsonb('diagnosis');
            $table->jsonb('anamnesis');
            $table->jsonb('catatan_tindakan');
            $table->jsonb('rencana_penatalaksanaan');
            $table->jsonb('pelayanan_lain');
            $table->binary('dokumen_penunjang');
            $table->timestamps();

            $table->unique(['id_pasien','tanggal_waktu']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropForeign('rekam_medis_id_pasien_foreign');
        });
        Schema::dropIfExists('rekam_medis');
    }
}
