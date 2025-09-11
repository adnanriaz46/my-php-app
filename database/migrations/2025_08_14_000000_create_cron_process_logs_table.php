<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('cron_process_logs', function (Blueprint $table) {
			$table->id();
			$table->string('command');
			$table->json('context')->nullable();
			$table->timestamp('started_at')->nullable();
			$table->timestamp('ended_at')->nullable();
			$table->string('status')->default('running'); // running, success, failed
			$table->unsignedInteger('processed_count')->default(0);
			$table->unsignedInteger('success_count')->default(0);
			$table->unsignedInteger('failure_count')->default(0);
			$table->json('response')->nullable();
			$table->text('error_message')->nullable();
			$table->timestamps();
			$table->index(['command', 'status']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('cron_process_logs');
	}
};


