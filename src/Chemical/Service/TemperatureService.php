<?php

namespace Chemical\Service;

class TemperatureService implements TemperatureServiceInterface
{

    /**
     * {@inheritDoc}
     */
    public function getTemperatureConversionArray($kelvinScale)
    {
	return array(
	    "kelvin" => array(
		'scale' => 'Kelvin',
		'value' => $kelvinScale),
	    "celsius" => array(
		'scale' => 'Celsius',
		'value' => ($kelvinScale - 273.15)),
	    "fahrenheit" => array(
		'scale' => 'Fahrenheit',
		'value' => ($kelvinScale * (9 / 5) - 459.67))
	);
    }

}
