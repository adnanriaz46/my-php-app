<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronProcessLog extends Model
{
	protected $fillable = [
		'command',
		'context',
		'started_at',
		'ended_at',
		'status',
		'processed_count',
		'success_count',
		'failure_count',
		'response',
		'error_message',
	];

	protected $casts = [
		        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
		'context' => 'array',
		'response' => 'array',
		'processed_count' => 'integer',
		'success_count' => 'integer',
		'failure_count' => 'integer',
	];
}


