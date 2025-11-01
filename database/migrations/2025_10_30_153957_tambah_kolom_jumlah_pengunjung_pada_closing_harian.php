<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahKolomJumlahPengunjungPadaClosingHarian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('closing_harians', function (Blueprint $table) {
            $table->integer('jumlah_pengunjung')->after('total_lebih_kurang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('closing_harians', function (Blueprint $table) {
            $table->dropColumn('jumlah_pengunjung');
        });
    }
}
