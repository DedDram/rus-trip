<?php

namespace Models\Comments;

use Services\Db;

class Webmaster
{
    protected object $db;
    public function __construct()
    {
        $this->db = Db::getInstance();
    }
    public function store(): bool
    {
        $client_id = $_POST['client_id'];
        $client_secret = $_POST['client_secret'];

        if(!empty($client_id) && !empty($client_secret))
        {
            $this->db->query("UPDATE `cl6s3_comments_webmaster` SET `host_id` = '', `user_id` = '', `access_token` = '', `expires_in` = '', `update_time` = '', `client_id` = ".$this->db->quote($client_id).", `client_secret` = ".$this->db->quote($client_secret)." WHERE id = 1 LIMIT 1");
            return true;
        }
        return false;
    }

    function getItem()
    {
        $item =  $this->db->query("SELECT * FROM `cl6s3_comments_webmaster` WHERE id = 1 LIMIT 1");
        return $item[0];
    }

    function getToken(): bool
    {
        $code = $_POST['code'];

        if(!empty($code))
        {
            $item = $this->db->query("SELECT * FROM `cl6s3_comments_webmaster` WHERE id = 1 LIMIT 1");

            $token = self::getTokenCurl($item[0]->client_id, $item[0]->client_secret, $code);

            if(!empty($token->access_token) && empty($token->error))
            {
                $user_id = self::getUserId($token->access_token);
                $host_id = self::getHostId($token->access_token, $user_id);

                $this->db->query("UPDATE `cl6s3_comments_webmaster` SET `host_id` = ".$this->db->quote($host_id).", `user_id` = ".$this->db->quote($user_id).", `access_token` = ".$this->db->quote($token->access_token).", `expires_in` = ".$this->db->quote($token->expires_in).", `update_time` = ".$this->db->quote(time())." WHERE id = 1");

                return true;
            }
        }
        return false;
    }

    private function getTokenCurl($client_id, $client_secret, $code)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth.yandex.ru/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret
        )));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    private function getUserId($access_token)
    {
        if(!empty($access_token))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.webmaster.yandex.net/v4/user/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
            $response = curl_exec($ch);
            curl_close($ch);
        }
        $data = json_decode($response);

        return $data->user_id;
    }

    private function getHostId($access_token, $user_id)
    {
        if(!empty($access_token))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.webmaster.yandex.net/v4/user/".$user_id."/hosts/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
            $response = curl_exec($ch);
            curl_close($ch);
        }

        $data = json_decode($response);
        $host_id = '';

        if(!empty($data->hosts))
        {
            foreach($data->hosts as $host)
            {
                if(self::clearUrl($_SERVER['HTTP_HOST']) == self::clearUrl($host->unicode_host_url))
                {
                    $host_id = $host->host_id;
                    break;
                }
            }
        }
        return $host_id;
    }

    private function clearUrl($url): string
    {
        if(preg_match('/http/', $url))
        {
            $parse_url = parse_url($url);
            $url = $parse_url['host'];
        }
        $result = preg_replace('/^www.(.*)/', '$1', $url);
        $result = strtolower($result);
        return trim($result);
    }
}