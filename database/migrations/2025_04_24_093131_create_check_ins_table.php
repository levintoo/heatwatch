<?php

use App\Models\Site;
use App\Models\Worker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Worker::class);
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->foreignIdFor(Site::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
