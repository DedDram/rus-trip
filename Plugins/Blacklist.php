<?php

namespace Plugins;

use Controllers\AbstractUsersAuthController;
use Models\Comments\Comments;
use Services\Db;
use Services\ReCaptcha;
use View\View;

class Blacklist  extends AbstractUsersAuthController
{
    protected object $db;

    protected View $view;

    protected string $ip;

    public function __construct()
    {
        parent::__construct();
        $this->db = Db::getInstance();
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function onBeforeCompileHead()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $server_ip = (require __DIR__ . '/../settings.php')['main']['server_ip'];

        if ($method == 'GET' && $server_ip != $this->ip) {
            // если агент - отключаем плагин
           // if (!empty($this->user) && $this->user->getUsersGroup() != 2) {
                $count = $this->db->query("SELECT id FROM `cl6s3_blacklist_items` WHERE whitelist = 0 AND cap = 0 AND ip = " . $this->db->quote(ip2long($this->ip)) . " LIMIT 1");
                // страницы с результатами ЕГЭ
                preg_match('~(.*)/exam~', $_SERVER['REQUEST_URI'], $urlExam, PREG_OFFSET_CAPTURE);

                if(!empty($urlExam[0][0])){
                    $result = $this->db->query("SELECT id,chek,ip FROM `chek_ip_exam` WHERE `ip` = " . $this->db->quote($this->ip) . ";");
                    if(empty($result)){
                        $this->db->query("INSERT INTO `chek_ip_exam` (`id`, `ip`, `chek`) VALUES (NULL, " . $this->db->quote($this->ip) . ", '1');");
                    }elseif ($result[0]->chek <=2){
                        $upChek = $result[0]->chek +1;
                        $this->db->query("UPDATE `chek_ip_exam` SET `chek` = '".$upChek."' WHERE `id` = '".$result[0]->id."';");
                    }elseif ($result[0]->chek >2){
                        $resultCOUNT = $this->db->query("SELECT COUNT(id) as total FROM `chek_ip_exam`");
                        if ($resultCOUNT[0]->total > 5000){
                            $this->db->query("TRUNCATE `chek_ip_exam`;");
                        }
                        $script = '<script src="/../templates/errors/js/site.cap.js"></script>' . PHP_EOL;
                        $this->view->setVar('script', $script);
                        $this->view->renderHtml('errors/blacklist.php', ['title' => 'Пройдите проверку, что вы не робот']);
                        exit();
                    }
                }
                if (!empty($count[0]->id)) {
                    $script = '<script src="/../templates/errors/js/site.cap.js"></script>' . PHP_EOL;
                    $this->view->setVar('script', $script);
                    $this->view->renderHtml('errors/blacklist.php', ['title' => 'Пройдите проверку, что вы не робот']);
                    exit();
                }
           // }
        }
    }

    public function resCode(): array
    {
        $code = (string) $_POST['code'];
        if(!empty($code))
        {
            $reCaptcha = new ReCaptcha;
            $reCaptcha->ReCaptcha("6LdzJ80SAAAAAJWyhHOIu5NOtyoHBZPWFxARZkdP");
            $resp = $reCaptcha->verifyResponse($this->ip, $code);
            if($resp->success)
            {
                if(preg_match('~(.*)/exam~', $_SERVER['HTTP_REFERER'])){
                    $this->db->query("DELETE  FROM `chek_ip_exam` WHERE `ip` = " . $this->db->quote($this->ip) . ";");
                }else{
                    $this->db->query("UPDATE `cl6s3_blacklist_items` SET `cap` = 1 WHERE `whitelist` = 0 AND `cap` = 0 AND `ip` = ".$this->db->quote(ip2long($this->ip))." LIMIT 1");
                }
                /**
                 * Функция очистки кеша Nginx
                 */
                Comments::clearCache($_SERVER['HTTP_REFERER']);

                return array(
                    'status' => 1
                );
            }
            return array(
                'status' => 0
            );
        }
        return array(
            'status' => 0
        );
    }
}
