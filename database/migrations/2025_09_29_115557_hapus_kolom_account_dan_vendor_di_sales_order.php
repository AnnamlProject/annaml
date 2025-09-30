<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HapusKolomAccountDanVendorDiSalesOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sales_orders', function (Blueprint $table) {
            // Baru hapus kolomnya
            $table->dropColumn('account_id');
            $table->dropColumn('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Kembalikan kolom kalau rollback
            $table->foreignId('payment_method_account_id')
                ->nullable()
                ->constrained('payment_method_details');
        });
    }
}
