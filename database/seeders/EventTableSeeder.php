<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Event;
use App\Models\WasteType;
use App\Models\Bin;
use App\Models\District;
use App\Models\User;
use App\Models\EventWasteType;
use App\Helpers\Helper;

class EventTableSeeder extends Seeder
{
	public function run(): void
    {
        $faker = Faker::create('en_SG');

        DB::beginTransaction();
        try {

			$districts = District::all();
			// $postalCodes = [
			//   "238858", "118553", "498800",
			//   "546080", "498794", "737736",
			//   "018926", "718919", "098322",
			//   "757752", "629122", "569830",
			//   "189970", "189969", "018956",
			//   "440085", "768442", "018906",
			//   "129817", "018951", "440082",
			//   "440081", "440083", "440087",
			//   "440086", "440080", "569933"
			// ];

			foreach ($districts as $district) {
				$oneMapAPI = Helper::singaporeOneMapAPI($district->name);
				if ($oneMapAPI->successful()) {
					$location = $oneMapAPI->json()['results'][0] ?? [];
					if (!$location || $location['POSTAL'] == 'NIL') {
						continue;
					}

					$eventTypeId = rand(1, 4);
					$districtId = $district->id;
					$userId = User::inRandomOrder()->first()->id ?? 1;

					$dateStart = $faker->dateTimeBetween('now', '+10 days')->format('Y-m-d');
					$dateEnd = $faker->dateTimeBetween('+11 days', '+20 days')->format('Y-m-d');

					$secretCode = null;
					$eventName = null;
					if ($eventTypeId == 3 || $eventTypeId == 4) {
						$eventName = $faker->sentence(3);
					}
					if ($eventTypeId == 3) {
						$secretCode = $this->generateSecretCode();
					}
					$event = Event::create([
						'code' => Event::generateCode($eventTypeId),
						'secret_code' => strtoupper($secretCode),
						'event_type_id' => $eventTypeId,
						'district_id' => $districtId,
						'user_id' => $userId,
						'name' => $eventName,
						'address' => $location['ADDRESS'],
						'postal_code' => $location['POSTAL'],
						'lat' => $location['LATITUDE'],
						'long' => $location['LONGITUDE'],
						'date_start' => $dateStart,
						'date_end' => $dateEnd,
						'time_start' => $faker->time('H:i'),
						'time_end' => $faker->time('H:i'),
						'description' => $faker->paragraph,
						'image' => null,
						'status' => 1,
					]);

					if ($eventTypeId != 3 && $eventTypeId != 4) {
						$fakeWasteTypes = [];
						for ($j = 0; $j < rand(1, 3); $j++) {
							$fakeWasteTypes[] = [
								'name' => ucfirst($faker->word),
								'price' => $faker->numberBetween(1000, 10000),
							];
						}
						$this->syncEventWasteTypes($event->id, $fakeWasteTypes);
					} else {
						$bins = Bin::inRandomOrder()->take(rand(1, 3))->get();
						$pivotData = [];
						foreach ($bins as $bin) {
							$pivotData[$bin->id] = ['point' => rand(10, 100)];
						}
						$event->bins()->attach($pivotData);
					}
				}
			}

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Seeder failed: " . $e->getMessage() . "\n";
        }
    }

    private function syncEventWasteTypes($eventId, $request)
    {
        try {
            $updatedWasteTypeIds = [];

            foreach ($request as $wasteTypeData) {
                $wasteType = WasteType::firstOrCreate(['name' => $wasteTypeData['name']]);

                EventWasteType::updateOrCreate(
                    [
                        'event_id' => $eventId,
                        'waste_type_id' => $wasteType->id
                    ],
                    [
                        'price' => $wasteTypeData['price']
                    ]
                );

                $updatedWasteTypeIds[] = $wasteType->id;
            }

            EventWasteType::where('event_id', $eventId)
                ->whereNotIn('waste_type_id', $updatedWasteTypeIds)
                ->delete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to save event waste types: ' . $e->getMessage());
            return false;
        }
    }

	public static function generateSecretCode() {
		$faker = Faker::create('en_SG');
		$randomWord1 = ucfirst(strtolower($faker->word));
		$randomWord2 = ucfirst(strtolower($faker->word));

		$secretCode = $randomWord1 . $randomWord2;
		return $secretCode;
	}
}
