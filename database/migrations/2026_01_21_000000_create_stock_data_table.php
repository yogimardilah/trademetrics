<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_data', function (Blueprint $table) {
            $table->id();
            $table->string('kode_saham')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('sebelumnya', 15, 2)->nullable();
            $table->decimal('open_price', 15, 2)->nullable();
            $table->date('tanggal_perdagangan_terakhir')->nullable();
            $table->decimal('first_trade', 15, 2)->nullable();
            $table->decimal('tertinggi', 15, 2)->nullable();
            $table->decimal('terendah', 15, 2)->nullable();
            $table->decimal('penutupan', 15, 2)->nullable();
            $table->decimal('selisih', 15, 2)->nullable();
            $table->bigInteger('volume')->nullable();
            $table->bigInteger('nilai')->nullable();
            $table->integer('frekuensi')->nullable();
            $table->decimal('index_individual', 20, 6)->nullable();
            $table->decimal('offer', 15, 2)->nullable();
            $table->bigInteger('offer_volume')->nullable();
            $table->decimal('bid', 15, 2)->nullable();
            $table->bigInteger('bid_volume')->nullable();
            $table->bigInteger('listed_shares')->nullable();
            $table->bigInteger('tradable_shares')->nullable();
            $table->bigInteger('weight_for_index')->nullable();
            $table->bigInteger('foreign_sell')->nullable();
            $table->bigInteger('foreign_buy')->nullable();
            $table->bigInteger('non_regular_volume')->nullable();
            $table->bigInteger('non_regular_value')->nullable();
            $table->integer('non_regular_frequency')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_data');
    }
};
