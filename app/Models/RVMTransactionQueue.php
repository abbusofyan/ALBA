<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RVMTransactionQueue extends Model
{
	const STATUS_PENDING = 0;
	const STATUS_SUCCESS = 1;
	const MAX_RETRY = 20;

    public $table = 'rvm_transaction_queues';

	public $fillable = [
		'user_id',
		'bin_id',
		'qrcode',
		'last_result',
		'type',
		'status',
		'retry'
	];

	/**
     * Store a new transaction queue or increment retry if it already exists.
     *
     * @param  string $qrCodeValue
     * @param  \App\Models\User $user
     * @param  \App\Models\Bin $bin
     * @param  string $type
     * @param  string|null $lastResult
     * @return self
     */
    public static function storeOrRetry($qrCodeValue, $user, $bin, $type = 'RVM_SYSTEM', $lastResult = null)
    {
        $queue = self::firstOrNew(['qrcode' => $qrCodeValue]);

        if (!$queue->exists) {
            $queue->retry = 0; // new record
        } else {
            $queue->retry = ($queue->retry ?? 0) + 1; // existing record
        }

        $queue->user_id     = $user->id;
        $queue->bin_id      = $bin->id;
        $queue->type        = $type;
        $queue->status      = self::STATUS_PENDING;
        $queue->last_result = $lastResult;

        $queue->save();

        return $queue;
    }
}
