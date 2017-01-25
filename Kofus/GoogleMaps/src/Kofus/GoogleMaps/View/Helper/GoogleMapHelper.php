<?php

namespace Kofus\GoogleMaps\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Uri\UriFactory;
use Zend\Math\Rand;

class GoogleMapHelper extends AbstractHelper
{
    
    protected $uriGoogleApis = 'https://maps.googleapis.com/maps/api/js';
    
    public function __invoke()
    {
        return $this;
    }
    
    protected $longitude;
    
    public function setLongitude($value)
    {
        $this->longitude = (float) $value; return $this;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    protected $latitude;
    
    public function setLatitude($value)
    {
        $this->latitude = (float) $value; return $this;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
    
    protected $zoom = 4;
    
    public function setZoom($value)
    {
        $this->zoom = (int) $value; return $this;
    }
    
    public function getZoom()
    {
        return $this->zoom;
    }
    
    protected $mapId;
    
    public function getMapId()
    {
        if (! $this->mapId)
            $this->mapId = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyz');
        return $this->mapId; 
    }
    
    public function render()
    {
        $uri = UriFactory::factory($this->uriGoogleApis);
        $uri->setQuery(array(
        	'key' => $this->getView()->config()->get('google_maps.api_key'),
            'callback' => 'initMap_' . $this->getMapId()
        ));
        $this->getView()->headScript()->appendFile($uri, 'text/javascript', array('async' => 'async', 'defer' => 'defer'));
        $this->getView()->headScript()->appendScript($this->buildScript());
        return '<div style="width: 100%; height: 400px; background-color: silver" id="map_'.$this->getMapId().'">MAP</div>';
    }
    
    protected function buildScript()
    {
        $s = " 
        function initMap_".$this->getMapId()."(){
            var map = new google.maps.Map(document.getElementById('map_".$this->getMapId()."'), {
                zoom: ".$this->getZoom().",
                center: {lat: ".$this->getLatitude().", lng: ".$this->getLongitude()."}
            });
        }";
        
        return $s;
    }
    
    
    public function __toString()
    {
        return $this->render();
    }
    

}