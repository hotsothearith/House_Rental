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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
             // Change to house_owner_id referencing house_owners table
            $table->unsignedBigInteger('house_owner_id'); // Foreign key to house_owners table
            $table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('cascade');
            $table->string('address', 100); // Address: varchar(100)
            $table->string('house_city', 20); // House_City: varchar(20)
            $table->string('house_district', 20); // House_District: varchar(20)
            $table->string('house_state', 20); // House_State: varchar(20)
            $table->longText('descriptions')->nullable(); // Descriptionn: longtext
            $table->integer('price'); // Price: int
            $table->string('house_type', 20); // House_Type: varchar(20)
            $table->integer('rooms'); // Rooms: int
            $table->string('furnitures', 30)->nullable(); // Furnitures: varchar(30)
            $table->string('variation', 30)->nullable(); // Variation: varchar(30)
            $table->string('image', 120)->nullable(); // Image: varchar(120) - for a single main image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
