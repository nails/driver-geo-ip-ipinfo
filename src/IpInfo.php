<?php

namespace Nails\GeoIp\Driver;

use Nails\Factory;
use Nails\GeoIp\Interfaces\Driver;

class IpInfo implements Driver
{
    /**
     * The base url of the ipinfo.io service.
     * @var string
     */
    const BASE_URL = 'http://ipinfo.io';

    // --------------------------------------------------------------------------

    /**
     * The value of a OK response
     * @var integer
     */
    const IPINFO_STATUS_OK = 200;

    // --------------------------------------------------------------------------

    /**
     * The access token for the ipinfo.io service
     * @var string
     */
    private $sAccessToken;

    // --------------------------------------------------------------------------

    /**
     * Construct the driver
     */
    public function __construct()
    {
        $this->sAccessToken = defined('APP_GEO_IP_ACCESS_TOKEN') ? APP_GEO_IP_ACCESS_TOKEN : '';
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $sIp  The IP address to look up
     * @return \Nails\GeoIp\Result\Ip
     */
    public function lookup($sIp)
    {
        $oIp     = Factory::factory('Ip', 'nailsapp/module-geo-ip');
        $oClient = Factory::factory('HttpClient');

        $oIp->setIp($sIp);

        try {

            $oResponse = $oClient->request(
                'GET',
                static::BASE_URL . '/' . $sIp . '/json',
                [
                    'query' => [
                        'token' => $this->sAccessToken
                    ]
                ]
            );

            if ($oResponse->getStatusCode() === static::IPINFO_STATUS_OK) {

                $oJson = json_decode($oResponse->getBody());

                if (!empty($oJson->hostname)) {
                    $oIp->setHostname($oJson->hostname);
                }

                if (!empty($oJson->city)) {
                    $oIp->setCity($oJson->city);
                }

                if (!empty($oJson->region)) {
                    $oIp->setRegion($oJson->region);
                }

                if (!empty($oJson->country)) {
                    $oIp->setCountry($oJson->country);
                }

                if (!empty($oJson->loc)) {

                    $aLatLng = explode(',', $oJson->loc);

                    if (!empty($aLatLng[0])) {
                        $oIp->setLat($aLatLng[0]);
                    }

                    if (!empty($aLatLng[1])) {
                        $oIp->setLng($aLatLng[1]);
                    }
                }
            }

        } catch (\Exception $e) {
            //  @log the exception somewhere
        }

        return $oIp;
    }
}
