<?php

namespace Kofus\GoogleMaps\View\Helper;

use Zend\Uri\UriFactory;
use Zend\Math\Rand;
use Zend\View\Helper\AbstractHtmlElement;

class GoogleMapHelper extends AbstractHtmlElement
{
    
    protected $uriGoogleApis = 'https://maps.googleapis.com/maps/api/js';
    protected $attribs = array();
    protected $markers = array();
    
    public function __invoke(array $attribs = array())
    {
        $this->attribs = $attribs;
        return $this;
    }
    
    public function addMarker($lat, $lng, $title)
    {
        $this->markers[] = array('lat' => $lat, 'lng' => $lng, 'title' => $title);
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
    
    protected $apiKey;
    
    public function setApiKey($value)
    {
        $this->apiKey = $value; return $this;
    }
    
    public function getApiKey()
    {
        if ($this->apiKey)
            return $this->apiKey;
        return $this->getView()->config()->get('google_maps.api_key');
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
        	'key' => $this->getApiKey(),
            'callback' => 'initMap_' . $this->getMapId()
        ));
        $this->getView()->headScript()->appendFile($uri, 'text/javascript', array('async' => 'async', 'defer' => 'defer'));
        $this->getView()->headScript()->appendScript($this->buildScript());
        $this->attribs['id'] = $this->normalizeId($this->getMapId());
        if (! isset($this->attribs['style']))
            $this->attribs['style'] = 'width: 100%; height: 300px; background-color: #eee';
        return '<div '.$this->htmlAttribs($this->attribs).'></div>';
    }
    
    protected function renderMarkers()
    {
        $js = '';
        foreach ($this->markers as $marker) {
            $js .= " 
                new google.maps.Marker({
                    position: {lat: ".$marker['lat'].", lng: ".$marker['lng']."},
                    map: map,
                    title: '".$this->getView()->escapeHtml($marker['title'])."'
                });
            ";
        }
        return $js;
    }
    
    protected function buildScript()
    {
        $s = " 
        function initMap_".$this->getMapId()."(){
            var map = new google.maps.Map(document.getElementById('".$this->getMapId()."'), {
                zoom: ".$this->getZoom().",
                center: {lat: ".$this->getLatitude().", lng: ".$this->getLongitude()."}
            });
            ".$this->renderMarkers()."
        }";
        
        return $s;
    }
    
    
    public function __toString()
    {
        return $this->render();
    }
    

}