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

        $this->getCity();
        $this->getMemorials();
        $this->getHotels();
        $this->getRestaurants();
        $this->save();
    }

    public function getCity(): void
    {
        $items = $this->db->query("SELECT alias FROM `cities`");

        $data = array_chunk($items, 42000);

        foreach($data as $n => $rows)
        {
            $result = array();
            foreach($rows as $item)
            {
                $timestamp = time();
                $lastmod = date('c', $timestamp);

                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias,
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/karta',
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/memorials',
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/hotels',
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/restaurants',
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/znakomstva',
                    'lastmod' => $lastmod
                );
                $result[] = (object) array(
                    'loc' => 'https://rus-trip.ru/'.$item->alias.'/foto',
                    'lastmod' => $lastmod
                );
            }
            $this->setXml($result, 'sitemap_city_'.$n.'.xml', $lastmod);
        }
    }

    public function getMemorials(): void
    {
        $memorials = $this->db->query("SELECT t2.alias as cityAlias,t1.alias,t1.id FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id");
        $result = array();
        foreach($memorials as $item){
            $timestamp = time();
            $lastmod = date('c', $timestamp);
            $result[] = (object) array(
                'loc' => 'https://rus-trip.ru/'.$item->cityAlias.'/memorial-'.$item->alias.'-'.$item->id,
                'lastmod' => $lastmod
            );

        }
        $this->setXml($result, 'sitemap_memorials.xml', date('c'));
    }

    public function getHotels(): void
    {
        $hotels = $this->db->query("SELECT t2.alias as cityAlias,t1.alias,t1.id FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id");
        $result = array();
        foreach($hotels as $item){
            $timestamp = time();
            $lastmod = date('c', $timestamp);
            $result[] = (object) array(
                'loc' => 'https://rus-trip.ru/'.$item->cityAlias.'/restaurant-'.$item->alias.'-'.$item->id,
                'lastmod' => $lastmod
            );

        }
        $this->setXml($result, 'sitemap_hotels.xml', date('c'));
    }

    public function getRestaurants(): void
    {
        $restaurants = $this->db->query("SELECT t2.alias as cityAlias,t1.alias,t1.id FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id");
        $result = array();
        foreach($restaurants as $item){
            $timestamp = time();
            $lastmod = date('c', $timestamp);
            $result[] = (object) array(
                'loc' => 'https://rus-trip.ru/'.$item->cityAlias.'/restaurant-'.$item->alias.'-'.$item->id,
                'lastmod' => $lastmod
            );

        }
        $this->setXml($result, 'sitemap_restaurants.xml', date('c'));
    }

    /**
     * @throws \DOMException
     */
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

    /**
     * @throws \DOMException
     */
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
            $aElement = $xml->createElement('loc', 'https://rus-trip.ru/xml/'.$file->name);
            $a = $row->appendChild($aElement);
            //
            $bElement = $xml->createElement('lastmod', $file->lastmod);
            $b = $row->appendChild($bElement);
        }
        $xml->formatOutput = true;
        $xml->save(__DIR__.'/../../xml/sitemap.xml');
    }


}