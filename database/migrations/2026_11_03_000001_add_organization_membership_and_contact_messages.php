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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique();
        });

        foreach (DB::table('workspaces')->whereNull('public_id')->get(['id']) as $workspace) {
            DB::table('workspaces')
                ->where('id', $workspace->id)
                ->update(['public_id' => (string) Str::uuid()]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('role_id')
                ->constrained('workspaces')->nullOnDelete();
        });

        foreach (DB::table('workspaces')->whereNotNull('user_id')->get(['id', 'user_id']) as $workspace) {
            DB::table('users')->where('id', $workspace->user_id)
                ->whereNull('workspace_id')
                ->update(['workspace_id' => $workspace->id]);
        }

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workspace_id');
        });

        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });
    }
};
