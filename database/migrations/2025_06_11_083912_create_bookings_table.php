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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_number')->unique();
            $table->string('tenant_email', 100);
            $table->foreign('tenant_email')->references('email_address')->on('tenants')->onDelete('cascade');

            $table->unsignedBigInteger('house_id'); // HouseId: int -> FK to houses table
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
            $table->string('from_date', 20); // FromDate: varchar(20) - Should ideally be 'date' type
            $table->string('to_date', 20); // ToDate: varchar(20) - Should ideally be 'date' type
            $table->string('duration', 20)->nullable(); // Duration: varchar(20)
            $table->string('message', 255)->nullable(); // Message: varchar(255)
            $table->integer('status')->default(0);// Status: int (e.g., 0=pending, 1=confirmed)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
