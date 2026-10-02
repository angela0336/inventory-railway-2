<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->enum('type', ['percent', 'fixed', 'free_item']);
            $t->unsignedInteger('value');
            $t->unsignedInteger('points_cost')->nullable();
            $t->unsignedInteger('min_purchase')->default(0);
            $t->unsignedSmallInteger('valid_days')->default(30);
            $t->string('auto_trigger')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};