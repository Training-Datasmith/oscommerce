<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS gz.*, count(z2gz.association_id) as total_entries from :table_geo_zones gz left join :table_zones_to_geo_zones z2gz on (gz.geo_zone_id = z2gz.geo_zone_id) group by gz.geo_zone_id order by gz.geo_zone_name';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qgroups = $OSCOM_PDO->prepare($sql_query);
        if ($data['batch_pageset'] !== -1) {
            $Qgroups->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qgroups->bind_int(':batch_max_results', $data['batch_max_results']);
        }
        $Qgroups->execute();
        $result['entries'] = $Qgroups->fetch_all();
        $Qgroups->next_rowset();
        $result['total'] = $Qgroups->fetch_column();
        return $result;
    }
}