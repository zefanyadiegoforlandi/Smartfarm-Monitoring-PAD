<?php

namespace Database\Factories;

use App\Models\DataSensor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DataSensorFactory extends Factory
{
    protected $model = DataSensor::class;

    private $timeAdded;

    public function definition()
    {
        // Mulai dari waktu awal
        if (!$this->timeAdded) {
            $this->timeAdded = Carbon::create(2024, 6, 21, 0, 0, 0);
        } else {
            // Tambah 5 menit untuk setiap data baru
            $this->timeAdded->addMinutes(5);
        }

        return [
            'id_sensor' => 'S007',
            'Light' => $this->faker->numberBetween(1000, 1200),
            'PersentaseKelembapanTanah' => $this->faker->numberBetween(80, 100),
            'AirQuality' => $this->faker->numberBetween(30, 70),
            'RainDrop' => $this->faker->randomFloat(1, 0.0, 1.0),
            'H' => $this->faker->randomFloat(1, 40.0, 60.0),
            'T' => $this->faker->randomFloat(1, 20.0, 30.0),
            'Temperature' => $this->faker->randomFloat(1, 20.0, 30.0),
            'Pressure' => $this->faker->randomFloat(1, 0.8, 1.5),
            'ApproxAltitude' => $this->faker->numberBetween(80, 120),
            'TimeAdded' => $this->timeAdded->toDateTimeString(),
        ];
    }
}


