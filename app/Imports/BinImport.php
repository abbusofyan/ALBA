<?php

namespace App\Imports;

use App\Models\Bin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\Helper;
use App\Models\BinType;
use Illuminate\Http\Client\RequestException;

class BinImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                if ((!is_array($row) && !is_object($row)) || empty($row['bin_type'])) {
                    continue;
                }

				$code = null;

				$code = $row['id'] ? str_replace('#', '', $row['id']) : Bin::generateCode();

				if (!$row['bin_type']) {
					throw new \Exception("Bin type cannot be empty");
				}

				$binType = BinType::where('name', $row['bin_type'])->first();
				if (!$binType) {
					throw new \Exception("Bin type '" . ($row['bin_type'] ?? 'undefined') . "' not found in the system.");
				}

				if (!preg_match('/^\d{6}$/', $row['postal_code'])) {
					throw new \Exception("Postal code '" . ($row['postal_code'] ?? 'undefined') . "' is invalid in row " . ($index + 2));
				}

				if (!$row['status']) {
					throw new \Exception("Status cannot be empty. It should have value Active or Inactive");
				}

				if (strtolower($row['status']) == 'active') {
					$status = 1;
				} elseif (strtolower($row['status']) == 'inactive') {
					$status = 0;
				} else {
					throw new \Exception("Status value invalid in row " . $index + 2 . ". It should be Active or Inactive");
				}

				$visibility = true;
				if (isset($row['is_hidden']) && strtolower($row['is_hidden']) == 'yes') {
					$visibility = false;
				}

                $lat = $long = $address = $radius = '';
				$lat = $row['latitude'];
				$long = $row['longitude'];
				$address = $row['address'];
				$radius = $row['radius'];

				$bin = Bin::where('code', $code)->first();
				if (!$bin || $bin->postal_code != $row['postal_code']) {
					$oneMapAPI = Helper::singaporeOneMapAPI($row['postal_code']);
					if ($oneMapAPI->successful()) {
						$results = $oneMapAPI->json()['results'] ?? [];
						if (!$results) {
							throw new \Exception("Postal code '" . ($row['postal_code'] ?? 'undefined') . "' is invalid in row " . ($index + 2));
						}

						$lat = $results[0]['LATITUDE'];
						$long = $results[0]['LONGITUDE'];
						$address = $results[0]['ADDRESS'];
					} else {
						throw new \Exception("OneMap API returned HTTP status: " . $oneMapAPI->status());
					}
				}

				if (isset($row['radius']) && $row['radius']) {
					$radius = str_replace(' ', '', $row['radius']);
					$isRadiusValid = preg_match('/^\d+(m)?$/', $radius);
					if (!$isRadiusValid) {
						throw new \Exception("Invalid radius in row " . ($index + 2) . '. It should be in this format : 50m, 100m, etc..');
					}
				}

				if (isset($row['address']) && $row['address']) {
					$address = $row['address'];
				}

				if (isset($row['latitude']) && $row['latitude']) {
					$lat = $row['latitude'];
				}

				if (isset($row['longitude']) && $row['longitude']) {
					$long = $row['longitude'];
				}

				Bin::updateOrCreate(
				    ['code' => $code],
				    [
				        'bin_type_id' => $binType->id,
				        'address'     => $address,
				        'postal_code' => $row['postal_code'],
				        'lat'         => $lat,
				        'long'        => $long,
				        'remark'      => $row['remark'] ?? '',
						'map_radius'  => $radius,
				        'qrcode'      => $row['qr_code'],
						'status' 	  => $status,
						'visibility'  => $visibility
				    ]
				);
            }

            DB::commit();
        } catch (RequestException $e) {
		    throw new \Exception("Failed to connect to OneMap API: " . $e->getMessage());
		} catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
