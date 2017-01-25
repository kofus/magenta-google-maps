<?php
namespace Kofus\GoogleMaps\Service;
use Kofus\System\Service\AbstractService;
use Zend\Uri\UriFactory;
use Zend\Http\Client as HttpClient;
use Kofus\GoogleMaps\Geocode;

class GoogleMapsService extends AbstractService
{
    protected $uriGeocode = 'https://maps.googleapis.com/maps/api/geocode/json';
    
    public function getGeocode($address)
    {
        // Assemble uri
        $uri = UriFactory::factory($this->uriGeocode);
        $uri->setQuery(array(
        	'address' => $address,
            'key' => $this->getApiKey()
        ));
        
        // Send http request
        $client = new HttpClient();
        $client->setUri($uri);
        $client->setMethod('get');
        $response = $client->send();
        
        // Create Geocode object
        $data = \Zend\Json\Json::decode($response->getBody(), 1);
        return new Geocode($data); 
    }
    
    public function getApiKey()
    {
        $apiKey = $this->config()->get('google_maps.api_key');
        if (! $apiKey)
            throw new \Exception('GoogleMapsService requires "google_maps.api_key" in config');
        return $apiKey; 
    }
}