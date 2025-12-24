<?php

namespace App\Imports;

use App\Models\Bin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\Helper;
use App\Models\Voucher;

class VoucherImport implements ToCollection, WithHeadingRow
{
	public $rewardId;

	public function __construct($rewardId) {
		$this->rewardId = $rewardId;
	}

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
			if (!isset($rows[0]['voucher_code'])) {
				throw new \Exception('Import failed : column "voucher code" not found in the excel file.');
			}

            foreach ($rows as $index => $row) {
                if ((!is_array($row) && !is_object($row)) || empty($row['voucher_code'])) {
                    continue;
                }

				// $isVoucherExists = Voucher::where('reward_id', $this->rewardId)->where('code', $row['voucher_code'])->exists();
				// if ($isVoucherExists) {
				// 	throw new \Exception("Import failed : Cannot import duplicated voucher : " . $row['voucher_code']);
				// }

                Voucher::create([
					'reward_id' => $this->rewardId,
                    'code' => $row['voucher_code'],
					'status' => 1
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // Re-throw the error to Excel import so it halts
        }
    }
}
