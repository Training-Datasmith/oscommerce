<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\SQL\My_Sql\V5;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Import_Fk
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $sql_file = OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/sql/oscommerce_innodb.sql';
        $OSCOM_PDO->import_sql($sql_file, $data['table_prefix']);
        $OSCOM_PDO->exec('DROP PROCEDURE IF EXISTS CountriesGetAll;
CREATE PROCEDURE CountriesGetAll (IN pageset INT, IN maxresults INT)
BEGIN
  IF pageset is null THEN
    SELECT SQL_CALC_FOUND_ROWS c.*, COUNT(z.zone_id) AS total_zones
    FROM osc_countries c
    LEFT JOIN osc_zones z ON (c.countries_id = z.zone_country_id)
    GROUP BY c.countries_id
    ORDER BY c.countries_name;
  ELSE
    PREPARE STMT FROM
      "SELECT SQL_CALC_FOUND_ROWS c.*, COUNT(z.zone_id) AS total_zones
       FROM osc_countries c
       LEFT JOIN osc_zones z ON (c.countries_id = z.zone_country_id)
       GROUP BY c.countries_id
       ORDER BY c.countries_name
       LIMIT ?, ?";
    SET @START = pageset;
    SET @LIMIT = maxresults;
    EXECUTE STMT USING @START, @LIMIT;
  END IF;

  SELECT FOUND_ROWS() as total;
END;');
    }
}