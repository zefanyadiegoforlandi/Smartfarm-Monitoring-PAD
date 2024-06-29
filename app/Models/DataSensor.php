<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    use HasFactory;

    protected $table = 'data_sensor';
    public $timestamps = false; // Nonaktifkan timestamps


    protected $fillable = [
        'id_sensor',
        'Light',
        'PersentaseKelembapanTanah',
        'AirQuality',
        'RainDrop',
        'H',
        'T',
        'Temperature',
        'Pressure',
        'ApproxAltitude',
        'TimeAdded',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }
}
