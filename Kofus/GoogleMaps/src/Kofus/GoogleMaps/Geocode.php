<?php
namespace Kofus\GoogleMaps;

class Geocode
{
    protected $data = array();
    
    public function __construct(array $data=array())
    {
        $this->data = $data;
    }
    
    public function getLongitude()
    {
        foreach ($this->data['results'] as $result) {
            if (isset($result['geometry']['location']['lng']))
                return $result['geometry']['location']['lng'];
        }
    }
    
    public function getLatitude()
    {
    	foreach ($this->data['results'] as $result) {
    		if (isset($result['geometry']['location']['lat']))
    			return $result['geometry']['location']['lat'];
    	}
    }
    
}