<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\WasteType;
use App\Models\District;
use App\Services\EventService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Helpers\Helper;
use DateTime;

class EventImport implements WithMultipleSheets
{
    public $eventTypeId;
    public $eventIds = [];
    public $wasteTypeIds = [];

    public function __construct($eventTypeId)
    {
        $this->eventTypeId = $eventTypeId;
    }

    public function sheets(): array
    {
        return [
            0 => new class($this) implements ToCollection, WithHeadingRow {
                protected $parent;
                public function __construct($parent) { $this->parent = $parent; }

				private function isValidYmdDate(string $date): bool
				{
					$d = DateTime::createFromFormat('Y-m-d', $date);
					return $d && $d->format('Y-m-d') === $date;
				}

                public function collection(Collection $rows)
                {
                    foreach ($rows as $index => $row) {

						if ((!is_array($row) && !is_object($row)) || empty($row['district'])) {
		                    continue;
		                }

						$startDate = $row['start_date'];
						$endDate = $row['end_date'];

						if (is_numeric($startDate)) {
						    $row['start_date'] = Date::excelToDateTimeObject($startDate)->format('Y-m-d');
						}

						if (!$this->isValidYmdDate($row['start_date'])) {
							throw new \Exception("Start date should be in Y-m-d format");
						}

						if (is_numeric($endDate)) {
						    $row['end_date'] = Date::excelToDateTimeObject($endDate)->format('Y-m-d');
						}

						if (!$this->isValidYmdDate($row['end_date'])) {
							throw new \Exception("End date should be in Y-m-d format");
						}

						$startTime = $endTime = '';
						$startTime = $this->normalizeExcelTime($row['start_time']);
						if ($startTime === false) {
						    throw new \Exception(
						        "Invalid start time format in row " . ($index + 2) .
						        ". Accepted formats: HH:MM AM/PM, HH:MM, HH:MM:SS"
						    );
						}

						$endTime = $this->normalizeExcelTime($row['end_time']);
						if ($endTime === false) {
						    throw new \Exception(
						        "Invalid end time format in row " . ($index + 2) .
						        ". Accepted formats: HH:MM AM/PM, HH:MM, HH:MM:SS"
						    );
						}


						$district = District::where('name', $row['district'])->first();
						if (!$district) {
							throw new \Exception("District not found in row " . ($index + 2));
						}

						if (!preg_match('/^\d{6}$/', $row['postal_code'])) {
							throw new \Exception("Postal code '" . ($row['postal_code'] ?? 'undefined') . "' is invalid in row " . ($index + 2));
						}

						$lat = $long = $address = null;

						$event = Event::where('code', $row['id'])->first();
						if (!$event || $event->postal_code != $row['postal_code']) {
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

						if ($event) {
							$event->update([
								'district_id' => $district->id,
								'address' => $address ?? $event->address,
								'postal_code' => $row['postal_code'],
								'lat' => $lat ?? $event->lat, //default center point of singapore lat
								'long' => $long ?? $event->long, // default center point of singapore lng
								'date_start' => $row['start_date'],
								'date_end' => $row['end_date'],
								'time_start' => $startTime,
								'time_end' => $endTime,
							]);
						} else {
							$event = Event::create([
								'code' => Event::generateCode($this->parent->eventTypeId),
								'secret_code' => $row['secret_code'] ?? '',
								'event_type_id' => $this->parent->eventTypeId,
								'district_id' => $district->id,
								// 'user_id' => $request->user_id,
								// 'name' => $request->name,
								'address' => $address,
								'postal_code' => $row['postal_code'],
								'lat' => $lat ?? '1.3521', //default center point of singapore lat
								'long' => $long ?? '103.8198', // default center point of singapore lng
								'date_start' => $row['start_date'],
								'date_end' => $row['end_date'],
								'time_start' => $startTime,
								'time_end' => $endTime,
								// 'description' => $request->description,
								// 'image' => $image,
								// 'use_all_bins' => $request->select_all_bins
							]);
						}

                        $this->parent->eventIds[] = $event->id;
                    }
                }

				function normalizeExcelTime($time)
				{
				    if (is_numeric($time)) {
				        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($time)
				            ->format('H:i:s');
				    }

				    $time = trim($time);

				    if (strtotime($time) === false) {
				        return false;
				    }

				    return date('H:i:s', strtotime($time));
				}

            },

            1 => new class($this) implements ToCollection, WithHeadingRow {
                protected $parent;
                public function __construct($parent) { $this->parent = $parent; }

				public function collection(Collection $rows)
				{
				    $wasteTypes = $rows->map(fn($row) => [
				        'name'  => $row['recyclables'],
						'price' => $row['price'] ?? $row['pricekg'] ?? 0,
				    ])->toArray();

				    foreach ($this->parent->eventIds as $eventId) {
				        EventService::syncEventWasteTypes($eventId, $wasteTypes);
				    }
				}
            },
        ];
    }

}
