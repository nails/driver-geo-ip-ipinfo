<?php

namespace Nails\GeoIp\Driver\IpInfo\Settings;

use Nails\Common\Helper\Form;
use Nails\Common\Interfaces;
use Nails\Common\Service\FormValidation;
use Nails\Components\Setting;
use Nails\Factory;

/**
 * Class IpInfo
 *
 * @package Nails\GeoIp\Driver\IpInfo\Settings
 */
class IpInfo implements Interfaces\Component\Settings
{
    const KEY_ACCESS_TOKEN = 'sAccessToken';

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'Geo-IP: IPInfo';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        return [];
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        /** @var Setting $oAccessToken */
        $oAccessToken = Factory::factory('ComponentSetting');
        $oAccessToken
            ->setKey(static::KEY_ACCESS_TOKEN)
            ->setType(Form::FIELD_PASSWORD)
            ->setLabel('Access Token')
            ->setEncrypted(true)
            ->setValidation([
                FormValidation::RULE_REQUIRED,
            ]);

        return [
            $oAccessToken,
        ];
    }
}
