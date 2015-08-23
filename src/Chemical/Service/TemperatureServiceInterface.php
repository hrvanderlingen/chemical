<?php

namespace Chemical\Service;

interface TemperatureServiceInterface
{

    /**
     * Return an array with the temperature converted to other scales
     *
     * @param  int $kelvinScale The temperature in Kelvins
     *
     * @return array
     */
    public function getTemperatureConversionArray($kelvinScale);
}
