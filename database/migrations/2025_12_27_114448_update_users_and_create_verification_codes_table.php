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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('profile_picture_url')->nullable()->after('google_id');
            $table->boolean('email_verified')->default(false)->after('profile_picture_url');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            $table->string('password')->nullable()->change();
        });

        Schema::create('email_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('code');
            $table->timestamp('expiry_time');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verification_codes');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'profile_picture_url', 'email_verified', 'last_login_at']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
