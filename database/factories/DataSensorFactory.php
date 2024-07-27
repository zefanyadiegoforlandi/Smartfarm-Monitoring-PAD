<?php

namespace Database\Factories;

use App\Models\DataSensor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DataSensorFactory extends Factory
{
    protected $model = DataSensor::class;

    private $timeAdded;
    private $step;
    private $phase;
    private $initialLight;
    private $initialHumidity;
    private $initialAirQuality;
    private $initialRainDrop;
    private $initialTemperature;
    private $initialPressure;
    private $upperLight;
    private $lowerLight;
    private $midLight;
    private $upperHumidity;
    private $lowerHumidity;
    private $midHumidity;
    private $upperAirQuality;
    private $lowerAirQuality;
    private $midAirQuality;
    private $upperRainDrop;
    private $lowerRainDrop;
    private $midRainDrop;
    private $upperTemperature;
    private $lowerTemperature;
    private $midTemperature;
    private $upperPressure;
    private $lowerPressure;
    private $midPressure;

    public function definition()
    {
        // Initialize timeAdded, step, and phase
        if (!$this->timeAdded) {
            $this->timeAdded = Carbon::create(2024, 6, 21, 0, 0, 0);
            $this->step = 0;
            $this->phase = 1;
            $this->initialLight = 1000;
            $this->initialHumidity = 80;
            $this->initialAirQuality = 30;
            $this->initialRainDrop = 0.0;
            $this->initialTemperature = 20.0;
            $this->initialPressure = 0.8;
            $this->upperLight = 1200;
            $this->lowerLight = 1000;
            $this->midLight = 1100;
            $this->upperHumidity = 100;
            $this->lowerHumidity = 80;
            $this->midHumidity = 90;
            $this->upperAirQuality = 70;
            $this->lowerAirQuality = 30;
            $this->midAirQuality = 50;
            $this->upperRainDrop = 1.0;
            $this->lowerRainDrop = 0.0;
            $this->midRainDrop = 0.5;
            $this->upperTemperature = 30.0;
            $this->lowerTemperature = 20.0;
            $this->midTemperature = 25.0;
            $this->upperPressure = 1.5;
            $this->lowerPressure = 0.8;
            $this->midPressure = 1.15;
        } else {
            // Add 5 minutes for each new data
            $this->timeAdded->addMinutes(5);
            $this->step++;
        }

        // Function to add small random fluctuation
        $fluctuation = function($value, $increment) {
            return $value + $increment + (rand(-10, 10) / 10);
        };

        // Initialize sensor values
        $lightValue = $this->initialLight;
        $humidityValue = $this->initialHumidity;
        $airQualityValue = $this->initialAirQuality;
        $rainDropValue = $this->initialRainDrop;
        $temperatureValue = $this->initialTemperature;
        $pressureValue = $this->initialPressure;

        if ($this->phase == 1) {
            // Phase 1: Increase from lower limit to upper limit (300 steps)
            $lightValue = $fluctuation($this->initialLight, ($this->upperLight - $this->lowerLight) / 300 * $this->step);
            $humidityValue = $fluctuation($this->initialHumidity, ($this->upperHumidity - $this->lowerHumidity) / 300 * $this->step);
            $airQualityValue = $fluctuation($this->initialAirQuality, ($this->upperAirQuality - $this->lowerAirQuality) / 300 * $this->step);
            $rainDropValue = $fluctuation($this->initialRainDrop, ($this->upperRainDrop - $this->lowerRainDrop) / 300 * $this->step);
            $temperatureValue = $fluctuation($this->initialTemperature, ($this->upperTemperature - $this->lowerTemperature) / 300 * $this->step);
            $pressureValue = $fluctuation($this->initialPressure, ($this->upperPressure - $this->lowerPressure) / 300 * $this->step);

            if ($this->step >= 300) {
                $this->phase = 2;
                $this->step = 0;
            }
        } elseif ($this->phase == 2) {
            // Phase 2: Decrease from upper limit to mid limit (200 steps)
            $lightValue = $fluctuation($this->upperLight, -($this->upperLight - $this->midLight) / 200 * $this->step);
            $humidityValue = $fluctuation($this->upperHumidity, -($this->upperHumidity - $this->midHumidity) / 200 * $this->step);
            $airQualityValue = $fluctuation($this->upperAirQuality, -($this->upperAirQuality - $this->midAirQuality) / 200 * $this->step);
            $rainDropValue = $fluctuation($this->upperRainDrop, -($this->upperRainDrop - $this->midRainDrop) / 200 * $this->step);
            $temperatureValue = $fluctuation($this->upperTemperature, -($this->upperTemperature - $this->midTemperature) / 200 * $this->step);
            $pressureValue = $fluctuation($this->upperPressure, -($this->upperPressure - $this->midPressure) / 200 * $this->step);

            if ($this->step >= 200) {
                $this->phase = 3;
                $this->step = 0;
            }
        } elseif ($this->phase == 3) {
            // Phase 3: Fluctuate around mid value, then decrease to lower limit (300 steps)
            if ($this->step < 150) {
                $lightValue = $fluctuation($this->midLight, rand(-2, 2) / 10);
                $humidityValue = $fluctuation($this->midHumidity, rand(-2, 2) / 10);
                $airQualityValue = $fluctuation($this->midAirQuality, rand(-2, 2) / 10);
                $rainDropValue = $fluctuation($this->midRainDrop, rand(-2, 2) / 10);
                $temperatureValue = $fluctuation($this->midTemperature, rand(-2, 2) / 10);
                $pressureValue = $fluctuation($this->midPressure, rand(-2, 2) / 10);
            } else {
                $lightValue = $fluctuation($this->midLight, -($this->midLight - $this->lowerLight) / 150 * ($this->step - 150));
                $humidityValue = $fluctuation($this->midHumidity, -($this->midHumidity - $this->lowerHumidity) / 150 * ($this->step - 150));
                $airQualityValue = $fluctuation($this->midAirQuality, -($this->midAirQuality - $this->lowerAirQuality) / 150 * ($this->step - 150));
                $rainDropValue = $fluctuation($this->midRainDrop, -($this->midRainDrop - $this->lowerRainDrop) / 150 * ($this->step - 150));
                $temperatureValue = $fluctuation($this->midTemperature, -($this->midTemperature - $this->lowerTemperature) / 150 * ($this->step - 150));
                $pressureValue = $fluctuation($this->midPressure, -($this->midPressure - $this->lowerPressure) / 150 * ($this->step - 150));
            }

            if ($this->step >= 300) {
                $this->phase = 1;
                $this->step = 0;
            }
        }

        return [
            'id_sensor' => 'S007',
            'Light' => max($this->lowerLight, min($this->upperLight, $lightValue)),
            'PersentaseKelembapanTanah' => max($this->lowerHumidity, min($this->upperHumidity, $humidityValue)),
            'AirQuality' => max($this->lowerAirQuality, min($this->upperAirQuality, $airQualityValue)),
            'RainDrop' => max($this->lowerRainDrop, min($this->upperRainDrop, $rainDropValue)),
            'H' => $this->faker->randomFloat(1, 40.0, 60.0),
            'T' => $this->faker->randomFloat(1, 20.0, 30.0),
            'Temperature' => max($this->lowerTemperature, min($this->upperTemperature, $temperatureValue)),
            'Pressure' => max($this->lowerPressure, min($this->upperPressure, $pressureValue)),
            'ApproxAltitude' => $this->faker->numberBetween(80, 120),
            'TimeAdded' => $this->timeAdded->toDateTimeString(),
        ];
    }
}
?>
