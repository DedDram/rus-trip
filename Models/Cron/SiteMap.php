<?php

namespace Models\Cron;

use DOMDocument;
use Services\Db;

class SiteMap
{
    protected object $db;
    public function __construct()
    {
        $this->db = Db::getInstance();

        $this->getSchools();
        $this->getDistrictsMetro();

        $this->save();
    }

    public function getSchools(): void
    {
        $items = $this->db->query("SELECT t1.modified, CONCAT_WS('-', t1.id, t1.alias) AS item_alias, CONCAT_WS('-', t2.id, t2.alias) AS category_alias, CONCAT_WS('-', t3.id, t3.alias) AS section_alias FROM `cl6s3_schools_items` AS t1 INNER JOIN `cl6s3_schools_categories` AS t2 ON t1.category_id = t2.id INNER JOIN `cl6s3_schools_sections` AS t3 ON t1.section_id = t3.id ORDER BY t1.id ASC");

        $data = array_chunk($items, 42000);

        foreach($data as $n => $rows)
        {
            $max = 0;
            $result = array();
            foreach($rows as $item)
            {
                //
                $timestamp = strtotime($item->modified);
                if($timestamp > 0)
                {
                    $lastmod = date('c', $timestamp);
                }else{
                    $lastmod = date('c', 1377029410);
                }
                if($lastmod > $max)
                {
                    $max = $lastmod;
                }
                //
                $result[] = (object) array(
                    'loc' => 'https://schoolotzyv.ru/schools/'.$item->section_alias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'lastmod' => $lastmod
                );
            }
            $this->setXml($result, 'sitemap_schools_'.$n.'.xml', $max);
        }
    }

    public function getDistrictsMetro(): void
    {
        $results = $this->db->query("SELECT t1.parent, t1.alias, CONCAT(t2.id, '-', t2.alias) as city ".
            "FROM `cl6s3_schools_districts` as t1 INNER JOIN `cl6s3_schools_big` as t2 ON t1.parent =  t2.rayon");

        $results_ = $this->db->query("SELECT t1.alias, CONCAT(t2.id, '-', t2.alias) as region, CONCAT(t3.id, '-', t3.alias) as section ".
            "FROM `cl6s3_schools_districts` as t1 LEFT JOIN `cl6s3_schools_categories` as t2 ON t1.parent =  t2.name AND t2.section_id = t1.section_id ".
            "LEFT JOIN `cl6s3_schools_sections` as t3 ON t3.id = t1.section_id WHERE t3.id IN (13,14,15,31)");

        $areas = $this->db->query("SELECT t1.alias, CONCAT(t2.id, '-', t2.alias) as region ".
            "FROM `cl6s3_schools_districts_r` as t1 LEFT JOIN `cl6s3_schools_categories` as t2 ON t1.parent =  t2.name");

        $metros = $this->db->query("SELECT CONCAT_WS('-', id, alias) as alias FROM `cl6s3_schools_metro`");

        foreach($results as $result){
            $d_items[$result->parent][] = $result->city.'/'.$result->alias;
        }

        $result = array();
        foreach($d_items as $key=>$d_item){
            if(count($d_item)>1){
                if($key == 'Санкт-Петербург'){
                    foreach($d_item as $item){
                        $result[] = (object) array(
                            'loc' => 'https://schoolotzyv.ru/schools/9-russia/173-sankt-peterburg'.$item
                        );
                    }
                }else{
                    foreach($d_item as $item){
                        $result[] = (object) array(
                            'loc' => 'https://schoolotzyv.ru/school/'.$item
                        );
                    }
                }
            }
        }
        foreach($results_ as $result_){
            $result[] = (object) array(
                'loc' => 'https://schoolotzyv.ru/schools/'.$result_->section.'/'.$result_->region.'/'.$result_->alias
            );
        }
        foreach($areas as $area){
            $result[] = (object) array(
                'loc' => 'https://schoolotzyv.ru/schools/9-russia/'.$area->region.'/'.$area->alias
            );
        }
        foreach($metros as $metro){
            $result[] = (object) array(
                'loc' => 'https://schoolotzyv.ru/metro/'.$metro->alias
            );
        }

        $this->setXml($result, 'sitemap_schools_dm.xml', date('c'));
    }

    private function setXml($items, $filename, $lastmod): void
    {
        $this->files[] = (object) array('name' => $filename, 'lastmod' => $lastmod);
        //
        $xml = new DomDocument('1.0','utf-8');
        $rssElement = $xml->createElement('urlset');
        $rssAttribute = $xml->createAttribute('xmlns');
        $rssAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $rssElement->appendChild($rssAttribute);
        $rss = $xml->appendChild($rssElement);

        foreach($items as $item)
        {
            $rowsElement = $xml->createElement('url');
            $row = $rss->appendChild($rowsElement);

            $aElement = $xml->createElement('loc', $item->loc);
            $a = $row->appendChild($aElement);
            if(!empty($item->lastmod)){
                $bElement = $xml->createElement('lastmod', $item->lastmod);
                $b = $row->appendChild($bElement);
            }
        }
        $xml->formatOutput = true;
        $xml->save(__DIR__.'/../../xml/'.$filename);
    }

    private function save(): void
    {
        $xml = new DomDocument('1.0','utf-8');
        //
        $rssElement = $xml->createElement('sitemapindex');
        $rssAttribute = $xml->createAttribute('xmlns');
        $rssAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $rssElement->appendChild($rssAttribute);
        $rss = $xml->appendChild($rssElement);
        //
        foreach($this->files as $file)
        {
            $rowsElement = $xml->createElement('sitemap');
            $row = $rss->appendChild($rowsElement);
            //
            $aElement = $xml->createElement('loc', 'https://schoolotzyv.ru/xml/'.$file->name);
            $a = $row->appendChild($aElement);
            //
            $bElement = $xml->createElement('lastmod', $file->lastmod);
            $b = $row->appendChild($bElement);
        }
        $xml->formatOutput = true;
        $xml->save(__DIR__.'/../../xml/sitemap.xml');
    }


}