<?php

namespace Models\Maps;

use Exceptions\NotFoundException;
use Services\Db;

class Maps
{
    protected object $db;
    protected string $section_slug;
    protected string $category_slug;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getSections()
    {
        return $this->db->query("SELECT id,title,alias,Languages,LocalName FROM `bw283_maps_sections` ORDER BY `title` ASC");
    }

    /**
     * @throws NotFoundException
     */
    public function getSection(int $section_id, string $section_alias)
    {
        $section = $this->db->query("SELECT *, CONCAT_WS('-', id, alias) AS section_slug FROM `bw283_maps_sections` WHERE id = ".$this->db->quote($section_id)." LIMIT 1");

        if(!empty($section))
        {
            $this->section_slug = $section[0]->section_slug;
            if($section_id.$section_alias != $section[0]->section_slug)
            {
                header("Location: /go/".$this->section_slug, true, 301);
            }
        }else{
           throw new NotFoundException();
        }
        return $section[0];
    }

    /**
     * @throws NotFoundException
     */
    public function getCategories(int $section_id)
    {
        $categories = $this->db->query("SELECT c.*, c.id AS category_id, c.title AS category_title, COUNT(i.id) AS city_count FROM `bw283_maps_categories` AS c LEFT JOIN `bw283_maps_items` AS i ON c.id = i.category_id WHERE c.section_id = ".$this->db->quote($section_id)." GROUP BY c.id, c.title ORDER BY c.title");

        if(!empty($categories))
        {
            foreach($categories as $index => $value)
            {
                $categories[$index]->link = '/go/'.$this->section_slug.'/'.$value->id.'-'.$value->alias;
            }
            return $categories;
        }else{
            throw new NotFoundException();
        }
    }

    public function getAddress(int $section_id): array
    {
        $result = array();

        $address = $this->db->query("SELECT t1.name, CONCAT_WS('-', t1.id, t1.alias) AS item_slug, CONCAT_WS('-', t2.id, t2.alias) AS category_slug, t3.geo_lat, t3.geo_long FROM `bw283_maps_items` AS t1 LEFT JOIN `bw283_maps_categories` AS t2 ON t1.category_id = t2.id LEFT JOIN `bw283_maps_address` AS t3 ON t1.id = t3.item_id WHERE t1.section_id = ".$this->db->quote($section_id));

        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' => 'https://rus-trip.ru/go/'.$this->section_slug.'/'.$item->category_slug.'/'.$item->item_slug,
                    'text' => $item->name
                );
            }
        }
        return $result;
    }

    /**
     * @throws NotFoundException
     */
    public function getCategory(int $section_id, string $section_alias, int $category_id, string $category_alias)
    {
        $category = $this->db->query("SELECT t1.*, CONCAT_WS('-', t1.id, t1.alias) AS category_slug, CONCAT_WS('-', t2.id, t2.alias) AS section_slug FROM `bw283_maps_categories` AS t1 INNER JOIN `bw283_maps_sections` AS t2 ON t1.section_id = t2.id WHERE t1.id = ".$this->db->quote($category_id)." LIMIT 1");

        if(!empty($category))
        {
            $this->section_slug = $category[0]->section_slug;
            $this->category_slug = $category[0]->category_slug;
            if($section_id.$section_alias != $category[0]->section_slug || $category_id.$category_alias != $category[0]->category_slug)
            {
                header("Location: /go/".$category[0]->section_slug."/".$category[0]->category_slug, true, 301);
            }
        }else{
            throw new NotFoundException();
        }
        return $category[0];
    }

    /**
     * @throws NotFoundException
     */
    public function getItems(int $category_id)
    {
        $items = $this->db->query("SELECT t1.* FROM `bw283_maps_items` AS t1 LEFT JOIN `bw283_maps_address` AS t2 ON t1.id = t2.item_id WHERE t1.category_id = ".$this->db->quote($category_id)." GROUP BY t1.id");

        if(!empty($items))
        {
            $n = 1;
            foreach($items as $index => $value)
            {
                $items[$index]->link = 'https://rus-trip.ru/go/'.$this->section_slug.'/'.$this->category_slug.'/'.$value->id.'-'.$value->alias;
                $items[$index]->name = $value->name;
                $items[$index]->n = $n;
                $n++;
            }
            return $items;
        }else{
            throw new NotFoundException();
        }
    }

    public function getAddressItems(int $category_id): array
    {
        $result = array();
        $address = $this->db->query("SELECT t1.*, t2.geo_lat, t2.geo_long FROM `bw283_maps_items` AS t1 INNER JOIN `bw283_maps_address` AS t2 ON t1.id = t2.item_id WHERE t1.category_id = ".$this->db->quote($category_id));
        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' =>  'https://rus-trip.ru/go/'.$this->section_slug.'/'.$this->category_slug.'/'.$item->id.'-'.$item->alias,
                    'text' => $item->name
                );
            }
        }
        return $result;
    }

    public function getAddresses(): array
    {
        $result = array();
        $section_id = (int) $_POST['id'];
        $step = (int) $_POST['step'];
        $quant = 1000;
        $start = $quant*($step-1);

        $section_slug = $this->db->query("SELECT CONCAT_WS('-', id, alias) as slug FROM `bw283_maps_sections` WHERE id = ".$this->db->quote($section_id));

        $address = $this->db->query("SELECT t1.name, CONCAT_WS('-', t1.id, t1.alias) AS item_slug, ".
            "CONCAT_WS('-', t2.id, t2.alias) AS category_slug, t3.geo_lat, t3.geo_long ".
            "FROM `bw283_maps_items` AS t1 LEFT JOIN `bw283_maps_categories` AS t2 ON t1.category_id = t2.id ".
            "LEFT JOIN `bw283_maps_address` AS t3 ON t1.id = t3.item_id WHERE t1.section_id = ".$this->db->quote($section_id).
            " LIMIT ".$start.", ".$quant);


        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' => 'https://rus-trip.ru/go/'.$section_slug[0]->slug.'/'.$item->category_slug.'/'.$item->item_slug,
                    'text' => $item->name
                );
            }
        }
        return $result;
    }

    public function getAddressDataCategory(): array
    {
        $result = array();
        $category_id = (int) $_POST['id'];
        $step = (int) $_POST['step'];
        $quant = 1000;
        $start = $quant*($step-1);

        $address = $this->db->query("SELECT t1.name, CONCAT_WS('-', t1.id, t1.alias) AS item_slug, ".
            "CONCAT_WS('-', t2.id, t2.alias) AS category_slug, CONCAT_WS('-', t4.id, t4.alias) AS section_slug, t3.geo_lat, t3.geo_long ".
            "FROM `bw283_maps_items` AS t1 ".
            "LEFT JOIN `bw283_maps_sections` AS t4 ON t1.section_id = t4.id ".
            "LEFT JOIN `bw283_maps_categories` AS t2 ON t1.category_id = t2.id ".
            "LEFT JOIN `bw283_maps_address` AS t3 ON t1.id = t3.item_id 
            WHERE t1.category_id = ".$this->db->quote($category_id).
            " LIMIT ".$start.", ".$quant);


        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' => 'https://rus-trip.ru/go/'.$item->section_slug.'/'.$item->category_slug.'/'.$item->item_slug,
                    'text' => $item->name
                );
            }
        }
        return $result;
    }

    /**
     * @throws NotFoundException
     */
    public function getItem(int $section_id, string $section_alias, int $category_id, string $category_alias, int $city_id, string $city_alias)
    {
        $item = $this->db->query("SELECT t1.*, CONCAT_WS('-', t1.id, t1.alias) AS item_slug, t2.title AS ctitle, ".
            "CONCAT_WS('-', t2.id, t2.alias) AS category_slug, CONCAT_WS('-', t3.id, t3.alias) AS section_slug ".
            "FROM `bw283_maps_items` AS t1 INNER JOIN `bw283_maps_categories` AS t2 ON t1.category_id = t2.id ".
            "INNER JOIN `bw283_maps_sections` AS t3 ON t1.section_id = t3.id WHERE t1.id = '".$city_id."' LIMIT 1");

        if(!empty($item))
        {
            if($section_id.$section_alias != $item[0]->section_slug || $category_id.$category_alias != $item[0]->category_slug || $city_id.$city_alias != $item[0]->item_slug)
            {
                header("Location: /go/".$item[0]->section_slug."/".$item[0]->category_slug."/".$item[0]->item_slug, true, 301);
            }
            return $item[0];
        }else{
            throw new NotFoundException();
        }
    }
}