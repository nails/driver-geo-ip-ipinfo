<?php

namespace Nails\GeoIp\Driver;

use Nails\Common\Driver\Base;
use Nails\Factory;
use Nails\GeoIp;
use Nails\GeoIp\Exception\GeoIpDriverException;
use Nails\GeoIp\Interfaces\Driver;
use Nails\GeoIp\Result;

class IpInfo extends Base implements Driver
{
    /**
     * The base url of the ipinfo.io service.
     * @var string
     */
    const BASE_URL = 'http://ipinfo.io';

    // --------------------------------------------------------------------------

    /**
     * The value of an OK response
     * @var integer
     */
    const STATUS_OK = 200;

    // --------------------------------------------------------------------------

    /**
     * The value of a Rate Limited response
     * @var integer
     */
    const STATUS_RATE_LIMIT_EXCEEDED = 429;

    // --------------------------------------------------------------------------

    /**
     * The access token for the ipinfo.io service
     * @var string
     */
    protected $sAccessToken;

    // --------------------------------------------------------------------------

    /**
     * @param string $sIp The IP address to look up
     *
     * @return Result\Ip
     */
    public function lookup(string $sIp): Result\Ip
    {
        $oHttpClient = Factory::factory('HttpClient');
        $oIp         = Factory::factory('Ip', GeoIp\Constants::MODULE_SLUG);

        $oIp->setIp($sIp);

        try {

            if (empty($this->sAccessToken)) {
                throw new GeoIpDriverException('An ipinfo.io Access Token must be provided.');
            }

            try {

                $oResponse = $oHttpClient->request(
                    'GET',
                    static::BASE_URL . '/' . $sIp . '/json',
                    [
                        'query' => [
                            'token' => $this->sAccessToken,
                        ],
                    ]
                );

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $oJson = json_decode($e->getResponse()->getBody());
                if (!empty($oJson->error->message)) {
                    throw new GeoIpDriverException(
                        $oJson->error->message,
                        $e->getCode()
                    );
                } else {
                    throw new GeoIpDriverException(
                        $e->getMessage(),
                        $e->getCode()
                    );
                }
            }

            $oJson = json_decode($oResponse->getBody());

            if ($oResponse->getStatusCode() === static::STATUS_RATE_LIMIT_EXCEEDED) {

                throw new GeoIpDriverException(
                    'Rate limit exceeded',
                    static::STATUS_RATE_LIMIT_EXCEEDED
                );

            } elseif ($oResponse->getStatusCode() === static::STATUS_OK) {

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

        } catch (GeoIpDriverException $e) {
            $oIp->setError($e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $oIp;
    }
}
