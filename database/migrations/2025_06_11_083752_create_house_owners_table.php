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
        Schema::create('house_owners', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name', 120); // OwnerName: varchar(120)
            $table->string('email_address', 100)->unique(); // EmailAddress: varchar(100)
            $table->string('password', 100); // Password: varchar(100)
            $table->string('mobile_number', 11)->nullable(); // MobileNumber: char(11)
            $table->string('address', 100); // Address: varchar(100)
            $table->rememberToken(); // For authentication
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_owners');
    }
};
