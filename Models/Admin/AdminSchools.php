<?php

namespace Models\Admin;

use Exception;
use Exceptions\DbException;
use Exceptions\NotFoundException;
use Services\Db;

class AdminSchools
{
    protected object $db;
    protected string $dir;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->dir = __DIR__ . '/../../images/schools';
    }

    public function getSchools()
    {
        return $this->db->query("SELECT * FROM `cl6s3_schools_items` ORDER BY `cl6s3_schools_items`.`id` DESC limit 10");
    }

    /**
     * @throws NotFoundException
     */
    public function getSchool(int $school_id)
    {
        $item = $this->db->query("SELECT * FROM `cl6s3_schools_items` WHERE id = '" . $school_id . "';");
        if (!empty($item[0])) {
            return $item[0];
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function editSchool(int $school_id)
    {
        $item = $this->db->query("SELECT t1.*, t2.name AS category_name, t5.morfer_name, t4.district_r,t4.district,t2.name as catname, t2.alias as category_alias, t3.alias as section_alias, t2.title AS ctitle, t6.id as rayonId " .
            "FROM `cl6s3_schools_items` AS t1  " .
            "INNER JOIN `cl6s3_schools_categories` AS t2 ON t1.category_id = t2.id  " .
            "INNER JOIN `cl6s3_schools_sections` AS t3 ON t1.section_id = t3.id " .
            "LEFT JOIN `cl6s3_schools_address` AS t4 ON t1.id = t4.item_id " .
            "LEFT JOIN `cl6s3_schools_districts_r` AS t5 ON (t4.district_r = t5.alias and t5.parent = t2.name)  " .
            "LEFT JOIN `cl6s3_schools_districts` AS t6 ON (t4.district = t6.alias and t6.parent = t1.rayon)  " .
            "WHERE t1.id = '" . $school_id . "';");
        if (!empty($item[0])) {
            $stats = (object)[];
            $stat = $this->db->query("SELECT * FROM `cl6s3_schools_stat` WHERE `item_id` = '" . $school_id . "';");
            if (!empty($stat)) {
                $states = array();
                unset($stat->id);
                unset($stat->item_id);
                unset($stat->busGovId);
                foreach ($stat as $key => $value) {
                    $states[$key] = $value;
                }
                $stats->stats = $states;
                $item[0]->stats = $stats;
            }

            return $item[0];
        } else {
            throw new NotFoundException();
        }
    }

    public function getExam(int $school_id)
    {
        return $this->db->query("SELECT * FROM `cl6s3_schools_exam` WHERE item_id = '" . $school_id . "'  ORDER BY year DESC;");
    }

    public function getExam_(int $school_id): array
    {
        return $this->db->query("SELECT * FROM `cl6s3_schools_exam2` WHERE item_id = '" . $school_id . "' ORDER BY year DESC");
    }

    public function addExam(array $postData): array
    {
        $item_id = (int)$postData['item_id'];
        $year = (int)$postData['year'];
        $value = (float)str_replace(',', '.', $postData['value']);

        if (!empty($year) && !empty($value)) {
            $this->db->query("INSERT INTO `cl6s3_schools_exam` (item_id, year, value) VALUES ('" . $item_id . "', '" . $year . "', '" . $value . "')");
            $id = $this->db->getLastInsertId();

            $response = array(
                'status' => 1,
                'id' => $id,
                'year' => $year,
                'value' => $value
            );
        } else {
            $response = array(
                'status' => 2
            );
        }
        return $response;
    }

    public function addExam_(array $postData): array
    {
        $item_id = (int)$postData['item_id'];
        $year = (int)$postData['year'];
        $value = (float)$postData['value'];

        if (!empty($year) && !empty($value)) {
            $this->db->query("INSERT INTO `cl6s3_schools_exam2` (item_id, year, value) VALUES ('" . $item_id . "', '" . $year . "', '" . $value . "')");
            $id = $this->db->getLastInsertId();

            $response = array(
                'status' => 1,
                'id' => $id,
                'year' => $year,
                'value' => $value
            );
        } else {
            $response = array(
                'status' => 2
            );
        }
        return $response;
    }

    public function delExam(int $id): array
    {
        if (!empty($id)) {
            $this->db->query("DELETE FROM `cl6s3_schools_exam` WHERE id = " . $id . " LIMIT 1");
        }
        return array();
    }

    public function delExam_(int $id): array
    {
        if (!empty($id)) {
            $this->db->query("DELETE FROM `cl6s3_schools_exam2` WHERE id = " . $id . " LIMIT 1");
        }
        return array();
    }

    public function getAddress(int $school_id)
    {
        $addresses = $this->db->query("SELECT t1.*, d.name as district_name, dr.name as district_r_name FROM  `cl6s3_schools_address` as t1 " .
            "LEFT JOIN `cl6s3_schools_items` as items on items.id = t1.item_id " .
            "LEFT JOIN `cl6s3_schools_categories` as cat on cat.id = items.category_id " .
            "LEFT JOIN `cl6s3_schools_districts` as d on d.alias = t1.district AND d.section_id = items.section_id AND (d.parent = t1.locality OR d.parent = cat.name) " .
            "LEFT JOIN `cl6s3_schools_districts_r` as dr ON dr.alias = t1.district_r AND dr.parent = cat.name " .
            "WHERE t1.item_id = '" . $school_id . "' GROUP BY t1.id;");

        foreach ($addresses as $address) {
            $metro = '';
            if ($address->metro) {
                $results = $this->db->query("SELECT name FROM `cl6s3_schools_metro` WHERE id IN(" . $address->metro . ")");
                foreach ($results as $result) {
                    $metro .= $result->name . ', ';
                }
                $metro = rtrim($metro, ' ,');
            }
            $address->metro = $metro;
        }

        return $addresses;
    }

    public function getTypes()
    {
        return $this->db->query("SELECT * FROM `cl6s3_schools_fields_type`");
    }

    public function getFields(int $school_id)
    {
        return $this->db->query("SELECT t1.*, t2.text AS name FROM `cl6s3_schools_fields_value` AS t1 INNER JOIN `cl6s3_schools_fields_type` AS t2 ON t1.type_id = t2.id WHERE t1.item_id = '" . $school_id . "' ORDER BY t2.ordering ASC");
    }

    public function getSections()
    {
        return $this->db->query("SELECT id, title FROM `cl6s3_schools_sections` ORDER BY title ASC");
    }

    public function getCategories()
    {
        return $this->db->query("SELECT id, title FROM `cl6s3_schools_categories` ORDER BY title ASC");
    }

    /**
     * @throws DbException
     */
    public function copy(int $school_id): bool
    {
        try {
            $data = $this->db->query('SELECT * FROM `cl6s3_schools_items` WHERE id = ' . $school_id . ' LIMIT 1');
            if (!empty($data[0])) {
                $freeId = $this->db->query("SELECT MIN(id + 1) AS next_id FROM cl6s3_schools_items WHERE NOT EXISTS ( SELECT * FROM cl6s3_schools_items AS t WHERE t.id = cl6s3_schools_items.id + 1 )");
                $this->db->query("INSERT INTO `cl6s3_schools_items` (`id`, `category_id`, `section_id`, `created`, `modified`, `alias`, `name`, `title`, `keywords`, `description`, `header`, `affiliation`, `text`, `preview_src`, `preview_alt`, `preview_title`, `preview_border`, `okrug`, `rayon`, `hideexam`, `rate`, `vote`, `average`, `maps`, `nearby`, `inn`, `mesto_ege2019`, `mesto_ege2020`, `mesto_ege2021`, `mesto_ege2022`, `sid`) VALUES (" . $this->db->quote($freeId[0]->next_id) . ", " . $this->db->quote($data[0]->category_id) . ", " . $this->db->quote($data[0]->section_id) . ", current_timestamp(), current_timestamp(), " . $this->db->quote($data[0]->alias) . ", " . $this->db->quote($data[0]->name) . ", " . $this->db->quote($data[0]->title) . ", " . $this->db->quote($data[0]->keywords) . ", " . $this->db->quote($data[0]->description) . ", " . $this->db->quote($data[0]->header) . ", '', " . $this->db->quote($data[0]->text) . ", '', '', '', '1', " . $this->db->quote($data[0]->okrug) . ", " . $this->db->quote($data[0]->rayon) . ", '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');");
            }
            $id = $this->db->getLastInsertId();

            $this->db->query("INSERT INTO `cl6s3_schools_fields_value` (`item_id`, `type_id`, `text`) SELECT " . $id . ", `type_id`, `text` FROM `cl6s3_schools_fields_value` WHERE `item_id` = '" . $school_id . "';");
            header("Location: /admin/schools?id=$id&task=edit", true, 301);
            return true;
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при записи в базу данных: ' . $e->getMessage());
        }
    }

    public function updateSchool(array $postData): array
    {
        if (!empty($postData['hideexam'])) {
            $hideexam = 1;
        } else {
            $hideexam = 0;
        }
        $this->db->query("UPDATE `cl6s3_schools_items` 
             SET `category_id` = '" . $postData['category_id'] . "', 
             `section_id` = '" . $postData['section_id'] . "',
             `modified` = NOW(),
             `alias` = '" . $postData['alias'] . "',
             `inn` = '" . $postData['inn'] . "',
             `name` = '" . $postData['name'] . "',
             `title` = '" . $postData['title'] . "',
             `keywords` = '" . $postData['keywords'] . "',
             `description` = '" . $postData['description'] . "',
             `header` = '" . $postData['header'] . "',
             `affiliation` = '" . $postData['affiliation'] . "',
             `text` = '" . $postData['text'] . "',
             `preview_alt` = '" . $postData['preview_alt'] . "',
             `preview_title` = '" . $postData['preview_title'] . "',
             `okrug` = '" . $postData['okrug'] . "',
             `rayon` = '" . $postData['rayon'] . "',
             `hideexam` = '" . $hideexam . "',
              `inn` = '" . $postData['inn'] . "'
             WHERE `id` = '" . $postData['id'] . "' LIMIT 1");

        if (!empty($postData['statistics']['infoUp'])) {
            $result = $this->db->query("SELECT * FROM `cl6s3_schools_stat` WHERE `item_id` = '" . $postData['id'] . "';");
            if (!empty($result)) {
                if (empty($postData['statistics']['students'])) {
                    $postData['statistics']['students'] = 0;
                }
                $this->db->query("UPDATE `cl6s3_schools_stat` SET `infoUp` = '" . $postData['statistics']['infoUp'] . "' ,
                            `workers` = '" . $postData['statistics']['workers'] . "' ,
                            `budget` = '" . $postData['statistics']['budget'] . "' ,
                            `salary` = '" . $postData['statistics']['salary'] . "' ,
                            `students` = '" . $postData['statistics']['students'] . "' 
                            WHERE `cl6s3_schools_stat`.`item_id` = '" . $postData['id'] . "';");
            } else {
                $this->db->query("INSERT INTO `cl6s3_schools_stat` (`id`, `item_id`, `infoUp`, `workers`, `budget`, `salary`, `students`, `busGovId`) VALUES (NULL, '" . $postData['id'] . "', '" . $postData['statistics']['infoUp'] . "', '" . $postData['statistics']['workers'] . "', '" . $postData['statistics']['budget'] . "', '" . $postData['statistics']['salary'] . "', '" . $postData['statistics']['students'] . "', '0');");
            }
        }
        if (!empty($postData['fields'])) {
            foreach ($postData['fields'] as $id => $text) {
                $this->db->query("UPDATE `cl6s3_schools_fields_value` SET text = '" . $text . "' WHERE id = '" . $id . "' LIMIT 1");
            }
        }
        return ['status' => 1, 'msg' => 'ok'];
    }

    /**
     * @throws DbException
     */
    public function delete(int $school_id): bool
    {
        try {
            $result = $this->db->query("SELECT * FROM `cl6s3_schools_items` WHERE `id` = '" . $school_id . "';");
            if (!empty($result)) {
                //удаляем саму школу
                $this->db->query("DELETE FROM `cl6s3_schools_items` WHERE `id` = '" . $school_id . "';");
                //удаляем статистику
                $this->db->query("DELETE FROM `cl6s3_schools_stat` WHERE `item_id` = '" . $school_id . "';");
                //удаляем адреса
                $this->db->query("DELETE FROM `cl6s3_schools_address` WHERE `item_id` = '" . $school_id . "';");
                //удаляем поля
                $this->db->query("DELETE FROM `cl6s3_schools_fields_value` WHERE `item_id` = '" . $school_id . "';");
                //удаляем отзывы
                $this->db->query("DELETE FROM `cl6s3_comments_items` WHERE `object_group` = 'com_schools' AND `object_id` = '" . $school_id . "';");
                if (!empty($result[0]->preview_src && file_exists($this->dir . '/' . $school_id . '/' . $result[0]->preview_src))) {
                    //удаляем фото школы
                    unlink($this->dir . '/' . $school_id . '/' . $result[0]->preview_src);
                }
                //удаляем результаты ЕГЭ
                $this->db->query("DELETE FROM `cl6s3_schools_exam` WHERE `item_id` = '" . $school_id . "';");
                $this->db->query("DELETE FROM `cl6s3_schools_exam2` WHERE `item_id` = '" . $school_id . "';");
                return true;
            }
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при удалении из базы данных: ' . $e->getMessage());
        }
    }

    public function transliterate($string): string
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $str = strtr($string, $converter);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        return trim($str, "-");
    }

    public function addAddress(int $item_id, string $geo_code): array
    {
        if (!empty($geo_code)) {
            $xml = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&geocode=' . urlencode(strip_tags($geo_code)) . '&results=1');
            $geo = explode(' ', $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);
            //
            $GeoObject = $xml->GeoObjectCollection->featureMember->GeoObject;
            $AdministrativeArea = $GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea;
            //
            $country_name = $GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->CountryName;
            if (!empty($AdministrativeArea->SubAdministrativeArea->Locality->LocalityName)) {
                $region = $AdministrativeArea->AdministrativeAreaName . ', ' . $AdministrativeArea->SubAdministrativeArea->SubAdministrativeAreaName;
                $locality = $AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
                if (!empty($AdministrativeArea->SubAdministrativeArea->Locality->DependentLocality->DependentLocalityName)) {
                    $street_address = $AdministrativeArea->SubAdministrativeArea->Locality->DependentLocality->DependentLocalityName . ', ' . $GeoObject->name;
                } else {
                    $street_address = $GeoObject->name;
                }
            } else {
                $region = $AdministrativeArea->AdministrativeAreaName;
                $locality = $AdministrativeArea->Locality->LocalityName;
                $street_address = $GeoObject->name;
            }
            if (!empty($country_name) && !empty($region) && !empty($locality) && !empty($street_address) && !empty($geo[0]) && !empty($geo[1]) && $geo[0] > 0 && $geo[1] > 0) {
                $district = $this->getDistrict($item_id, $geo[0] . ',' . $geo[1]);
                $districtr = $this->getDistrictr($item_id, $geo[0] . ',' . $geo[1]);
                $metro = $this->getMetro((string)$locality[0], $geo[0], $geo[1]);
                $district_name = '';
                $district_alias = '';
                $districtr_name = '';
                $districtr_alias = '';
                $metro_names = '';
                $metro_ids = '';
                if (!empty($district)) {
                    $district_name = (string)$district['name'];
                    $district_alias = (string)$district['alias'];
                }
                if (!empty($districtr)) {
                    $districtr_alias = str_replace(array('-munitsipalniy-okrug'), '', (string)$districtr['alias']);
                    $districtr_name = str_replace(array(' муниципальный округ'), ' округ', (string)$districtr['name']);
                }
                if (!empty($metro)) {
                    $metro_names = (string)$metro['names'];
                    $metro_ids = (string)$metro['ids'];
                }

                $this->db->query("INSERT INTO `cl6s3_schools_address` (item_id, geo_code, geo_lat, geo_long, country_name, region, district_r, locality, street_address, district, metro) VALUES ('" . $item_id . "', '" . $geo_code . "', '" . $geo[1] . "', '" . $geo[0] . "', '" . $country_name . "', '" . $region . "', '" . $districtr_alias . "', '" . $locality . "', '" . $street_address . "', '" . $district_alias . "', '" . $metro_ids . "')");

                $id = $this->db->getLastInsertId();

                $response = array(
                    'status' => 1,
                    'id' => $id,
                    'geo_code' => $geo_code,
                    'geo_long' => $geo[0],
                    'geo_lat' => $geo[1],
                    'country_name' => (string)$country_name,
                    'region' => (string)$region,
                    'locality' => (string)$locality,
                    'street_address' => (string)$street_address,
                    'district_name' => $district_name,
                    'districtr_name' => $districtr_name,
                    'metro' => $metro_names
                );
            } else {
                $response = array(
                    'status' => 2
                );
            }
        } else {
            $response = array(
                'status' => 3
            );
        }
        return $response;
    }

    public function delAddress(int $id): array
    {
        if (!empty($id)) {
            $this->db->query("DELETE FROM `cl6s3_schools_address` WHERE id = " . $id . " LIMIT 1");
        }
        return array();
    }

    public function getDistrict(int $id, $coord): array
    {
        $district = array();
        $parent = $this->db->query("SELECT t2.rayon FROM `cl6s3_schools_items` AS t1 LEFT JOIN `cl6s3_schools_big` AS t2 ON t2.rayon = t1.rayon WHERE t1.id = '" . $id . "';");
        if (!empty($parent[0]->rayon)) {
            $district = $this->getCityDistrict($parent[0]->rayon, $coord, 9);
        } else {
            $result = $this->db->query("SELECT t1.section_id, t2.name FROM `cl6s3_schools_items` AS t1 LEFT JOIN `cl6s3_schools_categories` AS t2 ON t2.id = t1.category_id WHERE t1.id = '" . $id . "';");
            if (in_array($result[0]->section_id, array(13, 14, 15, 31)) || $result[0]->name == 'Санкт-Петербург') {
                $district = $this->getSubjectDistrict($result[0]->name, $coord, 1, $result[0]->section_id);
            }
        }
        return $district;
    }

    public function getCityDistrict($parent, $coord, $section): array
    {
        $distr = array();
        $name = '';
        $data = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&kind=district&geocode=' . $coord);
        if ($fms = $data->GeoObjectCollection->featureMember) {
            $count = count($fms);
            if ($count == 3) {
                $fm = $fms[2];
            } elseif ($count == 2) {
                $fm = $fms[1];
            } elseif ($count == 1) {
                $fm = $fms[0];
            }
            if ($fm->GeoObject->metaDataProperty->GeocoderMetaData->text) {
                $obj = explode(',', $fm->GeoObject->metaDataProperty->GeocoderMetaData->text);
                if (strpos($obj[1], $parent) !== false &&
                    strpos($obj[2], 'район') !== false &&
                    strpos($obj[2], 'микрорайон') === false &&
                    strpos($obj[2], 'Микрорайон') === false &&
                    strpos($obj[2], 'исторический') === false) {
                    $name = trim(str_replace(array('район', 'жилой'), '', $obj[2])) . ' район';
                } elseif (strpos($obj[1], $parent) !== false && strpos($obj[2], 'округ') !== false) {
                    $name = trim(str_replace('округ', '', $obj[2])) . ' округ';
                } elseif (strpos($obj[2], $parent) !== false && strpos($obj[3], 'район') !== false && strpos($obj[3], 'микрорайон') === false && strpos($obj[3], 'Микрорайон') === false && strpos($obj[3], 'исторический') === false) {
                    $name = trim(str_replace(array('район', 'жилой'), '', $obj[3])) . ' район';
                } elseif (strpos($obj[2], $parent) !== false && strpos($obj[3], 'округ') !== false) {
                    $name = trim(str_replace('округ', '', $obj[3])) . ' округ';
                }
                if ($name) {
                    $d_alias = $this->transliterate(substr($name, 0, strrpos(trim($name), ' ')));
                    $result = $this->db->query("SELECT name FROM `cl6s3_schools_districts` WHERE parent = '" . $parent . "' AND name = '" . $name . "' AND section_id = " . $section);
                    if (!$result) {
                        $this->db->query("INSERT INTO `cl6s3_schools_districts` (section_id,parent,name,alias) VALUES ('" . $section . "','" . $parent . "','" . $name . "','" . $d_alias . "')");
                    }
                    $distr['name'] = $name;
                    $distr['alias'] = $d_alias;
                }
            }
        }
        return $distr;
    }

    public function getDistrictr(int $id, $coord): array
    {
        $district = array();
        $result = $this->db->query("SELECT t1.section_id, t2.name FROM `cl6s3_schools_items` AS t1 LEFT JOIN `cl6s3_schools_categories` AS t2 ON t2.id = t1.category_id WHERE t1.id = '" . $id . "';");
        if ($result[0]->section_id == 9 && $result[0]->name != 'Санкт-Петербург') {
            $district = $this->getSubjectDistrict($result[0]->name, $coord);
        }
        return $district;
    }

    public function getSubjectDistrict($parent, $coord, $c = null, $section = null): array
    {
        $distr = array();
        $name = '';
        $fm = null;
        if ($c) {
            $kind = '&kind=district';
        } else {
            $kind = '';
        }

        $data = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526' . $kind . '&geocode=' . $coord);

        if ($parent == 'Санкт-Петербург') {
            if (!empty($data) && $data->GeoObjectCollection->featureMember) {
                foreach ($data->GeoObjectCollection->featureMember as $element) {
                    if ($element->GeoObject->metaDataProperty->GeocoderMetaData->text) {
                        $obj = explode(',', $element->GeoObject->metaDataProperty->GeocoderMetaData->text);
                    }
                    if ($obj && strpos($obj[2], 'район') !== false && strpos($obj[2], 'микрорайон') === false) {
                        $fm = $element;
                        break;
                    }
                }
            }
            if (!$fm) {
                $data = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&geocode=' . $coord);
                if ($data->GeoObjectCollection->featureMember) {
                    foreach ($data->GeoObjectCollection->featureMember as $element) {
                        if ($element->GeoObject->metaDataProperty->GeocoderMetaData->text) {
                            $obj = explode(',', $element->GeoObject->metaDataProperty->GeocoderMetaData->text);
                        }
                        if ($obj && strpos($obj[2], 'район') !== false && strpos($obj[2], 'микрорайон') === false) {
                            $fm = $element;
                            break;
                        }
                    }
                }
            }
            if ($fm) {
                if ($fm->GeoObject->metaDataProperty->GeocoderMetaData->text) {
                    $obj = explode(',', $fm->GeoObject->metaDataProperty->GeocoderMetaData->text);
                    if (strpos($obj[1], $parent) !== false) {
                        if (strpos($obj[2], 'район') !== false && strpos($obj[2], 'микрорайон') === false) {
                            $name = trim(str_replace('район', '', $obj[2])) . ' район';
                        }
                        if ($name) {
                            $d_alias = $this->transliterate(substr($name, 0, strrpos(trim($name), ' ')));
                            $res = array();
                            $result = $this->db->query("SELECT name FROM `cl6s3_schools_districts` WHERE parent = '" . $parent . "' AND name = '" . $name . "' AND section_id = " . $section);
                            if (!$result) {
                                $this->db->query("INSERT INTO `cl6s3_schools_districts` (section_id,parent,name,alias) VALUES ('" . $section . "','" . $parent . "','" . $name . "','" . $d_alias . "')");
                            }
                            $distr['name'] = $name;
                            $distr['alias'] = $d_alias;
                        }
                    }
                }
            }
        } elseif (!$c) {
            $cat = $parent;
            //Переопределяем имя региона в соответствии с официальным названием
            $cat_geo = array(
                'Еврейская АО' => 'Еврейская автономная область',
                'Кабардино-Балкария' => 'Кабардино-Балкарская Республика',
                'Карачаево-Черкесия' => 'Карачаево-Черкесская Республика',
                'Марий Эл' => 'Республика Марий Эл',
                'Ненецкий АО' => 'Ненецкий автономный округ',
                'Удмуртская республика' => 'Удмуртская Республика',
                'Усть-Ордынский Бурятский АО' => 'Иркутская область',
                'Ханты-Мансийский АО' => 'Ханты-Мансийский автономный округ',
                'Чукотский АО' => 'Чукотский автономный округ',
                'Ямало-Ненецкий АО' => 'Ямало-Ненецкий автономный округ',
                'Дагестан' => 'Республика Дагестан',
                'Северная Осетия - Алания' => 'Республика Северная Осетия — Алания'
            );
            if (array_key_exists($parent, $cat_geo)) $cat = $cat_geo[$parent];
            if (!empty($data) && $fms = $data->GeoObjectCollection->featureMember) {
                $count = count($fms);
                if ($count == 3) {
                    $fm = $fms[2];
                } elseif ($count == 2) {
                    $fm = $fms[1];
                } elseif ($count == 1) {
                    $fm = $fms[0];
                } elseif ($count > 3) {
                    $fm = $fms[0];
                }

                if (!empty($fm) && $fm->GeoObject->metaDataProperty->GeocoderMetaData->text) {
                    $obj = explode(',', $fm->GeoObject->metaDataProperty->GeocoderMetaData->text);
                    if (count($obj) > 2 && strpos($obj[1], $cat) !== false && strpos($obj[2], 'городской округ') !== false) {
                        $name = 'городской округ ' . trim(str_replace('городской округ', '', $obj[2]));
                    } elseif (count($obj) > 2 && strpos($obj[1], $cat) !== false && strpos($obj[2], 'район') !== false && strpos($obj[2], 'микрорайон') === false && strpos($obj[2], 'Микрорайон') === false && strpos($obj[2], 'исторический') === false) {
                        $name = trim(str_replace(array('район', 'муниципальный'), '', $obj[2])) . ' район';
                    } elseif (count($obj) > 3 && strpos($obj[2], $cat) !== false && strpos($obj[3], 'городской округ') !== false) {
                        $name = 'городской округ ' . trim(str_replace('городской округ', '', $obj[3]));
                    } elseif (count($obj) > 3 && strpos($obj[2], $cat) !== false && strpos($obj[3], 'район') !== false && strpos($obj[3], 'микрорайон') === false && strpos($obj[3], 'Микрорайон') === false && strpos($obj[3], 'исторический') === false) {
                        $name = trim(str_replace(array('район', 'муниципальный'), '', $obj[3])) . ' район';
                    }
                    //Если ничего не определено, то определяем по компонентам адреса
                    if (!$name) {
                        $a_components = $fm->GeoObject->metaDataProperty->GeocoderMetaData->Address->Component;
                        $area = array();
                        foreach ($a_components as $a_component) {
                            $area["$a_component->kind"] = $a_component->name;
                        }
                        if (array_key_exists('province', $area) && $area['province'] == $cat) $name = str_replace(' Город ', ' ', $area['area']);
                    }

                    if ($name) {
                        if (strpos($name, 'улус') !== false) $name = trim(str_replace('район', '', $name));
                        $d_alias = $this->transliterate(trim(str_replace(array('район', 'городской округ', 'улус', 'территориальный округ'), '', $name)));
                        $result = $this->db->query("SELECT alias FROM `cl6s3_schools_districts_r` WHERE parent = '{$parent}' ");
                        if (!$result) {
                            $this->db->query("INSERT INTO `cl6s3_schools_districts_r` (parent,name,alias) VALUES ('" . $parent . "','" . $name . "','" . $d_alias . "')");
                        }
                        $distr['name'] = $name;
                        $distr['alias'] = $d_alias;
                    } else {
                        //Записываем признак того, что строка обработана
                        $distr['name'] = '';
                        $distr['alias'] = 'none';
                    }
                }
            }
        } else {
            $city_name = $parent;
            //Переопределяем имя города в соответствии с официальным названием
            $city_geo = array(
                'Днепропетровск' => 'Днепр',
                'Алма-Ата' => 'Алматы'
            );
            if (array_key_exists($city_name, $city_geo)) $city_name = $city_geo[$city_name];

            if (!empty($data->GeoObjectCollection->featureMember)) $fms = $data->GeoObjectCollection->featureMember;
            if (!empty($fms)) {
                $name = $this->locName($fms, $city_name);
            }
            if (!$name) {
                $data = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&kind=area&geocode=' . $coord);
                if (!empty($data->GeoObjectCollection->featureMember)) $fms = $data->GeoObjectCollection->featureMember;
                if (!empty($fms)) {
                    $name = $this->locName($fms, $city_name);
                }
            }
            if ($name) {
                $d_alias = $this->transliterate(trim(str_replace(array('район', 'округ', 'территориальный округ', 'административный округ'), '', $name)));
                $result = $this->db->query("SELECT name FROM `cl6s3_schools_districts` WHERE parent = '" . $parent . "' AND name = '" . $name . "' AND section_id = " . $section);
                if (!$result) {
                    $this->db->query("INSERT INTO `cl6s3_schools_districts` (section_id,parent,name,alias) VALUES ('" . $section . "','" . $parent . "','" . $name . "','" . $d_alias . "')");
                }
                $distr['name'] = $name;
                $distr['alias'] = $d_alias;
            }
        }

        return $distr;
    }

    private function locName($fms, $c_name): string
    {
        $name = '';
        $count = count($fms);
        if ($count == 3) {
            $fm = $fms[2];
        } elseif ($count == 2) {
            $fm = $fms[1];
        } elseif ($count == 1) {
            $fm = $fms[0];
        }
        $loc = $fm->GeoObject->metaDataProperty->GeocoderMetaData->text;
        $obj = explode(',', $loc);
        if (count($obj) > 2 && trim($obj[1]) == $c_name && strpos($obj[2], 'район') !== false && strpos($obj[2], 'микрорайон') === false && strpos($obj[2], 'Микрорайон') === false && strpos($obj[2], 'исторический') === false) {
            $name = trim(str_replace(array('район', 'жилой', 'муниципальный'), '', $obj[2])) . ' район';
        } elseif (count($obj) > 2 && trim($obj[1]) == $c_name && strpos($obj[2], 'округ') !== false) {
            $name = trim(str_replace(array('округ', 'муниципальный', 'территориальный', 'административный'), '', $obj[2])) . ' округ';
        } elseif (count($obj) > 3 && trim($obj[2]) == $c_name && strpos($obj[3], 'район') !== false && strpos($obj[3], 'микрорайон') === false && strpos($obj[3], 'Микрорайон') === false && strpos($obj[3], 'исторический') === false) {
            $name = trim(str_replace(array('район', 'жилой', 'муниципальный'), '', $obj[3])) . ' район';
        } elseif (count($obj) > 3 && trim($obj[2]) == $c_name && strpos($obj[3], 'округ') !== false) {
            $name = trim(str_replace(array('округ', 'муниципальный', 'территориальный', 'административный'), '', $obj[3])) . ' округ';
        }
        return $name;
    }

    public function getMetro($locality, $geo_long, $geo_lat): array
    {
        $m_arr = array();
        $cities = array('Москва', 'Санкт-Петербург', 'Екатеринбург', 'Казань', 'Нижний Новгород', 'Новосибирск', 'Самара', 'Киев', 'Минск', 'Харьков');
        $c_lat = array(
            'Москва' => 55.73,
            'Санкт-Петербург' => 59.93,
            'Екатеринбург' => 56.85,
            'Казань' => 55.80,
            'Нижний Новгород' => 56.28,
            'Новосибирск' => 55.02,
            'Самара' => 53.21,
            'Киев' => 50.40,
            'Минск' => 53.88,
            'Харьков' => 49.99
        );

        if (in_array($locality, $cities)) {
            //Длина 1 градуса параллели в метрах
            $length = 111321 * cos($c_lat["$locality"] * 2 * pi() / 360);
            $max_distance = 3000;
            $data = simplexml_load_file('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&geocode=' . $geo_long . ',' . $geo_lat . '&kind=metro&results=2');
            $metro = array();
            if ($fms = $data->GeoObjectCollection->featureMember) {
                foreach ($fms as $fm) {
                    if (strpos($fm->GeoObject->metaDataProperty->GeocoderMetaData->text, "$locality") !== false) {
                        $coord = explode(' ', $fm->GeoObject->Point->pos);
                        $distance = sqrt(pow(($geo_long - $coord[0]) * $length, 2) + pow(($geo_lat - $coord[1]) * 111400, 2));
                        $name = trim(str_replace('метро', '', $fm->GeoObject->name));
                        if ($distance < $max_distance && strpos($name, 'станция') === false) {
                            $metro[$name] = array(
                                'name' => $name,
                                'geo_long' => $coord[0],
                                'geo_lat' => $coord[1]
                            );
                        }
                    }
                }
            }

            if ($metro) {
                $list_id = '';
                $list_name = '';
                foreach ($metro as $key => $value) {
                    $mid = $this->db->query("SELECT id, name FROM `cl6s3_schools_metro` WHERE name = '" . $key . "' AND city = '" . $locality . "'");
                    if (!$mid) {
                        $this->db->query("INSERT INTO `cl6s3_schools_metro` (city,name,alias,geo_long,geo_lat) VALUES('" . $locality . "','" . $key . "','" . $this->transliterate($key) . "','" . $value['geo_long'] . "','" . $value['geo_lat'] . "')");
                    }
                    if ($mid) {
                        $list_id .= $mid[0]->id . ',';
                        $list_name .= $mid[0]->name . ', ';
                    }
                }
                if ($list_id && $list_name) {
                    $m_arr['ids'] = rtrim($list_id, ',');
                    $m_arr['names'] = rtrim($list_name, ', ');
                }
            }
        }
        return $m_arr;
    }

    public function addPreview($postData, $postFiles): array
    {
        $item_id = $postData['item_id'];
        if(!empty($postFiles['myfile'])){
            $file = $postFiles['myfile'];
        }
        if (!empty($file['tmp_name'])) {
            $imageSize = getimagesize($file['tmp_name']);
            if ($imageSize[2] == 1) {
                $img_ext = "gif";
            } else if ($imageSize[2] == 2) {
                $img_ext = "jpg";
            } else if ($imageSize[2] == 3) {
                $img_ext = "png";
            } else {
                return array(
                    'status' => 2
                );
            }
            $this->_delPreview($item_id);

            $dir = $this->_getDir($item_id);
            $preview_src = md5(uniqid(rand(), 1)) . '.' . $img_ext;
            move_uploaded_file($file['tmp_name'], $dir . '/' . $preview_src);

            $this->db->query("UPDATE `cl6s3_schools_items` SET preview_src = '" . $preview_src . "' WHERE id = " . $item_id . " LIMIT 1");

            return array(
                'status' => 1,
                'src' => '/images/schools/' . $item_id . '/' . $preview_src
            );
        } else {
            return array(
                'status' => 3
            );
        }

    }

    private function _getDir(int $id): string
    {
        $ph = $this->dir . '/' . $id;
        if (!file_exists($ph)) {
            mkdir($ph, 0755, true);
        }
        return $ph;
    }

    public function _delPreview(int $item_id): array
    {
        $item = $this->db->query("SELECT * FROM `cl6s3_schools_items` WHERE id = " . $item_id . " LIMIT 1");
        if (!empty($item[0]->preview_src)) {
            unlink($this->dir . '/' . $item[0]->id . '/' . $item[0]->preview_src);
            $this->db->query("UPDATE `cl6s3_schools_items` SET preview_src = '' WHERE id = " . $item_id . " LIMIT 1");
        }
        return array();
    }

    public function addField($postData): array
    {
        $item_id = (int)$postData['item_id'];
        $type_id = (int)$postData['type_id'];
        $text = (string)$postData['text'];
        if (!empty($text)) {
            $this->db->query("INSERT INTO `cl6s3_schools_fields_value` (item_id, type_id, text) VALUES ('" . $item_id . "', '" . $type_id . "', " . $this->db->quote($text) . ");");
            $id = $this->db->getLastInsertId();;

            $response = array(
                'status' => 1,
                'id' => $id
            );
        } else {
            $response = array(
                'status' => 2
            );
        }
        return $response;
    }

    public function delField(int $id): array
    {
        if (!empty($id)) {
            $this->db->query("DELETE FROM `cl6s3_schools_fields_value` WHERE id = '" . $id . "' LIMIT 1");
        }
        return array();
    }

    public function geoNearby(string $nameCity): array
    {
        $this->db->query("UPDATE `cl6s3_schools_items` SET `nearby`= '0' WHERE `rayon` = " . $this->db->quote($nameCity) . ";");
        $this->db->query("UPDATE `cl6s3_schools_address` as t1 LEFT JOIN `cl6s3_schools_items` as t2 on t1.item_id = t2.id SET t1.nearby = 0 WHERE t2.nearby = 0");

        $allAddress = $this->db->query("SELECT geo_lat,geo_long,item_id,id FROM `cl6s3_schools_address` WHERE nearby=0 LIMIT 1000");
        if (!empty($allAddress)) {
            foreach ($allAddress as $item) {
                $address = $this->db->query("SELECT t1.id FROM `cl6s3_schools_items` AS t1 INNER JOIN `cl6s3_schools_address` AS t4 ON t1.id = t4.item_id " .
                    "WHERE  " .
                    "(t4.geo_long BETWEEN " . ($item->geo_long - 0.015) . " AND " . ($item->geo_long + 0.015) . ") AND " .
                    "(t4.geo_lat BETWEEN " . ($item->geo_lat - 0.015) . " AND " . ($item->geo_lat + 0.015) . ") GROUP BY t1.id LIMIT 100");
                if (!empty($address)) {
                    $num = count($address);
                    $this->db->query("UPDATE `cl6s3_schools_items` SET nearby = nearby + " . $this->db->quote($num) . " WHERE id=" . $item->item_id);
                    $this->db->query("UPDATE `cl6s3_schools_address` SET nearby=" . $this->db->quote($num) . " WHERE id=" . $item->id);
                }
            }
            return array(
                'status' => 1
            );
        }else{
            return array(
                'status' => 2
            );
        }
    }
}