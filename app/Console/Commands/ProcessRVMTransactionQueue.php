<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\RVMTransactionQueue;
use App\Services\RVMSystemService;
use App\Services\EnvipcoService;
use Carbon\Carbon;

class ProcessRVMTransactionQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-rvm-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing Pending RVM Transaction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$rvmTransactionQueue = RVMTransactionQueue::where('status', RVMTransactionQueue::STATUS_PENDING)
			->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())
			->get();
		foreach ($rvmTransactionQueue as $rvmTransaction) {
			$user = User::find($rvmTransaction->user_id);
			if ($user) {
				if ($rvmTransaction->type == 'RVM_SYSTEM') {
					$rvmSystemService = new RVMSystemService;
					$rvmSystemService->scan($user, $rvmTransaction->qrcode);
				}

				if ($rvmTransaction->type == 'ENVIPCO') {
					$envipcoService = new EnvipcoService;
					$envipcoService->scan($user, $rvmTransaction->qrcode);
				}
			}
		}
    }
}
