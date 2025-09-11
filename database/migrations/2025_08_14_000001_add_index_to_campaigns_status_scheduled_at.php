<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('campaigns', function (Blueprint $table) {
			$table->index(['status', 'scheduled_at'], 'campaigns_status_scheduled_at_idx');
		});
	}

	public function down(): void
	{
		Schema::table('campaigns', function (Blueprint $table) {
			$table->dropIndex('campaigns_status_scheduled_at_idx');
		});
	}
};


