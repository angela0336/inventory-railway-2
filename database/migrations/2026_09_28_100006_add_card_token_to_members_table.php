<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('members', 'card_token')) {
            return;
        }

        Schema::table('members', function (Blueprint $t) {
            $t->string('card_token', 64)->nullable()->after('member_code');
        });

        // Isi token untuk member yang sudah ada (misalnya Budi)
        DB::table('members')->whereNull('card_token')->orderBy('id')->each(function ($m) {
            DB::table('members')->where('id', $m->id)->update(['card_token' => Str::random(40)]);
        });

        Schema::table('members', function (Blueprint $t) {
            $t->unique('card_token');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('members', 'card_token')) {
            return;
        }

        Schema::table('members', function (Blueprint $t) {
            $t->dropUnique(['card_token']);
            $t->dropColumn('card_token');
        });
    }
};