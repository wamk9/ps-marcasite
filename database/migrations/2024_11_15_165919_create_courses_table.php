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
        Schema::create('courses', function (Blueprint $table) {
            $table->ulid('id');
            $table->string('name');
            $table->string('category_id', 26);
            $table->decimal('value', 10, 2);
            $table->boolean('active');
            $table->integer('vacations')->unsigned();
            $table->date('registration_at');
            $table->date('registration_until');
            $table->text('description')->nullable();

            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
