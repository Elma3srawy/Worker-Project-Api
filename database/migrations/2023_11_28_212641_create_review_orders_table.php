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
        Schema::create('review_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")->constrained("order_posts")->cascadeOnUpdate();
            $table->string("comments")->nullable();
            $table->unsignedTinyInteger("rate");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_orders');
    }
};
