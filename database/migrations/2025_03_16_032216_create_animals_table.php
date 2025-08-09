<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enclosure_id')->references('id')->on('enclosures')->Delete('cascade');
            $table->timestamps();
            $table->string('name');
            $table->string('species');
            $table->boolean('is_predator');
            $table->timestamp('born_at');
            $table->string('kep')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
