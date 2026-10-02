<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $t) {
            $t->id();
            $t->string('member_code')->nullable()->unique();
            $t->string('name');
            $t->string('phone')->unique();
            $t->string('email')->nullable();
            $t->date('birth_date')->nullable();
            $t->string('tier')->default('silver');
            $t->unsignedInteger('points_balance')->default(0);
            $t->unsignedInteger('lifetime_points')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};