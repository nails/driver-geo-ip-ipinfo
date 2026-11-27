<?php

namespace Nails\GeoIp\Driver\IpInfo\Admin\Permission\Settings;

use Nails\Admin\Interfaces\Permission;

class IpInfo implements Permission
{
    public function label(): string
    {
        return 'Can manage Geo-IP: IPInfo settings';
    }

    public function group(): string
    {
        return 'Settings';
    }
}
