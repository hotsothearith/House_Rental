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
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_no');
            $table->foreign('booking_no')->references('id')->on('bookings')->onDelete('cascade');

            $table->unsignedBigInteger('house_id');
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');

            // Change to house_owner_id
            $table->unsignedBigInteger('house_owner_id'); // AgentID: int -> FK to house_owners table
            $table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('cascade');

            $table->string('user_email', 100); // UserEmail: varchar(100) - For tenant
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('administrators')->onDelete('set null');

            
            $table->longText('remember')->nullable(); // Remember: longtext
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
