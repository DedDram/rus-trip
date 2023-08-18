<?php

namespace Models\Comments;

use Models\ActiveRecordEntity;
use Models\Users\User;
use Models\Users\UsersAuthService;
use Services\Db;
use Services\UploadFile;

class Comments extends ActiveRecordEntity
{
    protected object $db;
    /** @var string */
    protected string $object_group;
    /** @var int */
    protected int $object_id;
    /** @var string */
    protected string $created;
    /** @var int */
    protected int $ip;
    /** @var int */
    protected int $user_id;
    /** @var int */
    protected int $rate;
    /** @var string */
    protected string $country;
    /** @var int */
    protected int $status;
    /** @var string */
    protected string $username;
    /** @var string */
    protected string $email;
    /** @var int */
    protected int $isgood;
    /** @var int */
    protected int $ispoor;
    /** @var string */
    protected string $description;
    /** @var int */
    protected int $images;
    /** @var string */
    protected string $dir = __DIR__ . '/../../images/comments';
    /** @var User|null */
    protected ?User $user;
    /** @var string */
    protected string $comment_id;


    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->ip = ip2long($_SERVER['REMOTE_ADDR']);
        $this->user = UsersAuthService::getUserByToken();
        if (!empty($_POST['item_id'])) {
            $this->comment_id = (int)$_POST['item_id'];
        }
        if (!empty($_POST['object_id'])) {
            $this->object_id = (int)$_POST['object_id'];
        }
        if (!empty($_POST['object_group']) && ($_POST['object_group'] === 'city' || $_POST['object_group'] === 'memorial' || $_POST['object_group'] === 'hotel' || $_POST['object_group'] === 'restaurant')) {
            $this->object_group = $_POST['object_group'];
        }

        if (!empty($this->user)) {
            $this->user_id = $this->user->getId();
        } else {
            $this->user_id = 0;
        }
    }

    protected static function getTableName(): string
    {
        return 'cl6s3_comments_items';
    }

    public static function getComments(string $objectGroup, int $objectId, int $limit, int $offset, int $start, User $user = null): array
    {
        $db = Db::getInstance();
        if (!empty($user) && $user->isAdmin()) {
            $rate = $db->query("SELECT rate FROM `cl6s3_comments_items` WHERE object_group = '" . $objectGroup . "' AND object_id = " . $objectId . "");
            $items['comments'] = $db->query("SELECT t1.*, t1.username AS guest_name, t2.name AS user_name, t2.id AS registered 
                                    FROM `cl6s3_comments_items` AS t1 
                                        LEFT JOIN `cl6s3_users` AS t2 ON t1.user_id = t2.id
                                         WHERE t1.object_group = '" . $objectGroup . "' AND t1.object_id = " . $objectId . " ORDER BY t1.created DESC LIMIT " . $limit . " OFFSET " . $offset . ";");
        } else {
            $rate = $db->query("SELECT rate FROM `cl6s3_comments_items` WHERE object_group = '" . $objectGroup . "' AND object_id = " . $objectId . " AND status = 1");
            $items['comments'] = $db->query("SELECT t1.*, IF(NOW() < DATE_ADD(t1.created, INTERVAL 15 MINUTE), 1, 0) AS edit, t1.username AS guest_name, t2.name AS user_name, t2.id AS registered 
                                   FROM `cl6s3_comments_items` AS t1 
                                       LEFT JOIN `cl6s3_users` AS t2 ON t1.user_id = t2.id 
                                         WHERE t1.object_group = '" . $objectGroup . "' AND t1.object_id = " . $objectId . " AND t1.status = 1 ORDER BY t1.created DESC LIMIT " . $limit . " OFFSET " . $offset . ";");
        }
        if (empty($start)) {
            $start = 1;
        }
        $n = count($rate) - $limit * ($start - 1);
        foreach ($items['comments'] as $index => $value) {
            $items['comments'][$index]->n = $n;
            $n--;
        }
        $items['blacklist'] = self::getBlacklist();
        $items['total'] = count($rate);
        $items['rate'] = $rate;
        return $items;
    }

    private static function getBlacklist(): bool
    {
        $ip = ip2long($_SERVER['REMOTE_ADDR']);
        $db = Db::getInstance();
        $blacklist = $db->query("SELECT COUNT(*) as ban FROM `cl6s3_comments_blacklist` WHERE ip = '" . $ip . "' AND created > DATE_SUB(NOW(), INTERVAL 1 DAY) LIMIT 1");

        if (!empty($blacklist[0]->ban)) {
            return true;
        } else {
            return false;
        }
    }

    public function create(): array
    {
        $rate = (int)$_POST['star'];
        if (empty($this->user)) {
            $username = self::input($_POST['username']);
            $email = (string)$_POST['email'];
        } else {
            $username = $this->user->getUserName();
            $email = $this->user->getEmail();
        }
        if (!empty($_POST['subscribe'])) {
            $subscribe = $_POST['subscribe'];
        }
        $description = (string)$_POST['description'];
        $attach = (string)$_POST['attach'];

        // Проголосовать можно один раз
        $temp = $this->getRate();
        if (empty($temp[0]->itog) && ($rate < 1 || $rate > 5)) {
            return array(
                'status' => 2,
                'msg' => 'Вы не проголосовали! Выберите оценку отзыва.'
            );
        }
        // Данные гостя
        if (empty($this->user_id)) {
            if (empty($username)) {
                return array(
                    'status' => 2,
                    'msg' => 'Пожалуйста, введите Ваше имя'
                );
            }
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return array(
                        'status' => 2,
                        'msg' => 'Пожалуйста, введите корректный E-mail'
                    );
                }
            } else {
                return array(
                    'status' => 2,
                    'msg' => 'Пожалуйста, введите E-mail'
                );
            }
        }

        // Проверка отзыва
        $comment = self::_clearComment($description);
        if (!empty($comment)) {
            if (strlen($comment) < 100) {
                return array(
                    'status' => 2,
                    'msg' => 'Минимальная длина отзыва - 100 символов'
                );
            }
        } else {
            return array(
                'status' => 2,
                'msg' => 'Пожалуйста, введите текст отзыва'
            );
        }

        // Проверка латиницы
        preg_match_all('/[a-z]/i', $comment, $lat);
        $lat_count = sizeof($lat[0]);
        $all_count = strlen($comment);

        if (($lat_count / $all_count * 100) > 30) {
            return array(
                'status' => 2,
                'msg' => 'Отзывы на латинице запрещены'
            );
        }

        // Проверка спама ссылок
        preg_match('/\[url/m', $comment, $spam);
        if (!empty($spam[0])) {
            return array(
                'status' => 2,
                'msg' => 'Спам не пройдет!'
            );
        }

        // Фильтр
        $username = self::input($username);
        $description = self::input($description);
        //удаляем <br> на конце отзыва
        $description = preg_replace('~^(.*)(<br>|<br />|<br/>){1,}$~msU', '$1', $description);
        //удаляем <br> в начале отзыва
        $description = preg_replace('~^(<br>|<br />|<br/>){1,}(.*)$~ms', '$2', $description);

        try {
            $this->db->query("INSERT INTO `cl6s3_comments_items` (created, object_group, object_id, user_id, ip, username, rate, description, email) VALUES (NOW(), '" . $this->object_group . "', '" . $this->object_id . "', '" . $this->user_id . "', '" . $this->ip . "', " . $this->db->quote($username) . ", '" . $rate . "', " . $this->db->quote($description) . ", " . $this->db->quote($email) . ")");
            $item_id = $this->db->getLastInsertId();
            $this->db->query("UPDATE `cl6s3_comments_images` SET item_id = '" . $item_id . "' WHERE item_id = 0 AND attach = '" . $attach . "';");
            $this->db->query("UPDATE `cl6s3_comments_items` SET images = (SELECT COUNT(*) FROM `cl6s3_comments_images` WHERE item_id = '" . $item_id . "') WHERE id = '" . $item_id . "';");

        } catch (\PDOException $e) {
            return array(
                'status' => 2,
                'msg' => 'Удалите из отзыва специальные символы (смайлики и т.п.), оставьте просто текст.'
            );
        }
        if (!empty($this->user_id)) {
            // Отписываемся
/*            if (empty($subscribe)) {
                self::unsubscribe($this->object_group, $this->object_id, $this->user_id);
            }*/
            // Публикуем
            self::publishItems($item_id);
            // Подписываемся
            if (!empty($subscribe)) {
                self::subscribe($this->object_group, $this->object_id, $this->user_id);
            }
        }
        // Уведомление админу
        self::setNotification($this->object_group, $this->object_id, $item_id, 2);

        if (!empty($this->user_id)) {
            return array(
                'status' => 1,
                'msg' => 'Спасибо, Ваш отзыв опубликован, чтобы увидеть его - перезагрузите страницу'
            );
        } else {
            return array(
                'status' => 1,
                'msg' => 'Спасибо, Ваш отзыв будет добавлен после проверки модератором'
            );
        }
    }

    public function edit($comment_id): array
    {
        $description = (string)$_POST['description'];
        $attach = (string)$_POST['attach'];
        if (!empty($this->user_id)) {
            $temp = $this->db->query("SELECT * FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "' AND user_id = '" . $this->user_id . "' AND DATE_ADD(created, INTERVAL 15 MINUTE) > NOW() LIMIT 1");

            if (!empty($temp[0]) || $this->user->isAdmin()) {
                // Проверка отзыва
                $comment = self::_clearComment($description);
                if (!empty($comment)) {
                    if (strlen($comment) < 50) {
                        return array(
                            'status' => 2,
                            'msg' => 'Минимальная длина отзыва - 50 символов'
                        );
                    }
                } else {
                    return array(
                        'status' => 2,
                        'msg' => 'Пожалуйста, введите текст отзыва'
                    );
                }
                $description = self::input($description);
                //
                $this->db->query("UPDATE `cl6s3_comments_items` SET description = " . $this->db->quote($description) . " WHERE id = " . $this->db->quote($comment_id));

                $this->db->query("UPDATE `cl6s3_comments_images` SET item_id = " . $this->db->quote($comment_id) . " WHERE item_id = 0 AND attach = " . $this->db->quote($attach));

                $this->db->query("UPDATE `cl6s3_comments_items` SET images = (SELECT COUNT(*) FROM `cl6s3_comments_images` WHERE item_id = " . $this->db->quote($comment_id) . ") WHERE id = " . $this->db->quote($comment_id));

                return array(
                    'status' => 1,
                    'msg' => 'Спасибо, ваш отзыв сохранен, перезагрузите страницу.'
                );
            }
        }
        return array(
            'status' => 1,
            'msg' => 'Ошибка доступа'
        );
    }

    public function unpublishItems(int $comment_id): array
    {
        if (!empty($comment_id)) {
            $items = $this->db->query("SELECT * FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");
            // Чистим рассылку
            self::_clearNotifications($comment_id);
            // Снимаем с публикации
            $this->db->query("UPDATE `cl6s3_comments_items` SET status = 0 WHERE id = '" . $comment_id . "';");

            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!empty($item->status)) {
                        // Пересчитываем рейтинг
                        self::_rate($item->object_group, $item->object_id, '-' . $item->rate);
                    }
                }
            }
            return array(
                'msg' => 'Комментарий снят с публикации'
            );
        }
        return array(
            'msg' => 'Ошибка снятия с публикации'
        );
    }

    public function publishItems(int $comment_id): array
    {
        if (!empty($comment_id)) {
            // Проверка на плохие слова
            self::parseCurseWords($comment_id);

            // Публикуем
            $this->db->query("UPDATE `cl6s3_comments_items` SET status = 1 WHERE id = '" . $comment_id . "';");

            $items = $this->db->query("SELECT * FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");
            if (!empty($items)) {
                foreach ($items as $item) {
                    // YandexWebmaster
                    //  $comment = self::_clearComment($item->description);
                    //  if (strlen($comment) > 500) {
                        //     self::YandexWebmaster($comment);
                        // }
                    // Пересчитываем рейтинг
                    self::_rate($item->object_group, $item->object_id, '+' . $item->rate);
                    // Добавляем рассылку
                    self::setNotification($item->object_group, $item->object_id, $item->id, 1);
                    // Добавляем страницу на переобход роботом
                    // $temp = self::getItem($item->object_group, $item->object_id);
                   // self::YandexWebmasterOverride('https://rus-trip.ru' . $temp['url']);
                }
            }
            return array(
                'msg' => 'Комментарий опубликован'
            );
        }
        return array(
            'msg' => 'Ошибка при публикации'
        );
    }

    public function blacklist($comment_id): array
    {
        if (!empty($comment_id)) {
            $this->db->query("REPLACE INTO `cl6s3_comments_blacklist` (created, ip) SELECT NOW(), ip FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");
            return array(
                'msg' => 'IP добавлен в черный список'
            );
        }
        return array(
            'msg' => 'Ошибка добавления IP добавлен в черный список'
        );
    }

    public function remove(int $comment_id): array
    {
        if (!empty($comment_id)) {
            $items = $this->db->query("SELECT * FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");
            // Удаляем изображения
            if (!empty($items[0]->images)) {
                self::_clearImages($comment_id);
            }
            // Чистим рассылку
            self::_clearNotifications($comment_id);

            // Удаляем комментарии
            $this->db->query("DELETE FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");

            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!empty($item->status)) {
                        // Пересчитываем рейтинг
                        self::_rate($item->object_group, $item->object_id, '-' . $item->rate);
                    }
                }
            }
            return array(
                'msg' => 'Комментарий удален'
            );
        }
        return array(
            'msg' => 'Ошибка'
        );
    }

    private function subscribe($object_group, $object_id, $user_id)
    {
        $this->db->query("REPLACE INTO `cl6s3_comments_subscribers` (object_group, object_id, created, user_id) VALUES ('" . $object_group . "', '" . $object_id . "', NOW(), '" . $user_id . "')");
    }

    private function getRate(): ?array
    {
        if (!empty($this->user)) {
            return $this->db->query("SELECT COUNT(*) as itog FROM `cl6s3_comments_items` WHERE object_group = '" . $this->object_group . "' AND object_id = '" . $this->object_id . "' AND user_id = '" . $this->user_id . "' LIMIT 1");
        } else {
            return $this->db->query("SELECT COUNT(*) as itog FROM `cl6s3_comments_items` WHERE object_group = '" . $this->object_group . "' AND object_id = '" . $this->object_id . "' AND ip = '" . $this->ip . "' AND DATE_ADD(created, INTERVAL 1 DAY) > NOW() LIMIT 1");
        }
    }

    private function input($text): string
    {
        //удаляем эмодзи
        $text = self::removeEmoji($text);
        $text = strip_tags($text, array("<br>", "\r", "\n", "<br/>", "<br><blockquote>"));
        // переносы
        $text = str_replace("\r\n", '<br>', $text);
        // пробелы
        $text = str_replace('&nbsp;', ' ', $text);
        $text = preg_replace("/\s{2,}/", ' ', $text);
        $text = preg_replace("~</blockquote>(<br>)+~", '</blockquote>', $text);
        $text = preg_replace('/(!{1,}|\.{1,}|,{1,}|\?{1,})(\S)/', '$1 $2', $text);
        $text = preg_replace('/(\s)(!|\.|,|\?)/', '$2', $text);
        return trim($text);
    }

    private function removeEmoji($text): string
    {
        return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
    }

    public function unsubscribe($object_group, $object_id, $user_id): array
    {
        $this->db->query("DELETE FROM `cl6s3_comments_subscribers` WHERE object_group = '" . $object_group . "' AND object_id = '" . $object_id . "' AND user_id = '" . $user_id . "';");
        return array(
            'msg' => 'Вы отписались от уведомлений о новых комментариях'
        );
    }

    private function setNotification($object_group, $object_id, $item_id, $type)
    {
        $temp = self::getItem($object_group, $object_id);
        $title = $temp['title'];
        $url = $temp['url'];

        if ($type == 1) {
            $this->db->query("REPLACE INTO `cl6s3_comments_cron` (item_id, user_id, type, title, url) SELECT '" . $item_id . "', user_id, '" . $type . "', '" . $title . "', '" . $url . "' FROM `cl6s3_comments_subscribers` WHERE object_group = '" . $object_group . "' AND object_id = '" . $object_id . "';");
        }
        if ($type == 2 || $type == 3) {
            $this->db->query("REPLACE INTO `cl6s3_comments_cron` (item_id, type, title, url) VALUES ('" . $item_id . "', '" . $type . "', '" . $title . "', '" . $url . "')");
        }
        self::clearCache($url);
    }

    private function _clearComment($comment): string
    {
        $comment = preg_replace("/\<blockquote\>(.*)\<\/blockquote\>/", '', $comment);
        $comment = strip_tags($comment, array("<br>", "\r", "\n", "<br/>"));
        return trim($comment);
    }

    private function YandexWebmaster($comment)
    {
        $item = $this->db->query("SELECT * FROM `cl6s3_comments_webmaster` WHERE id = 1 LIMIT 1");

        if (!empty($item[0]->user_id) && !empty($item[0]->host_id) && !empty($item[0]->access_token)) {
            $data = json_encode(array(
                "content" => $comment
            ));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.webmaster.yandex.net/v4/user/" . $item[0]->user_id . "/hosts/" . $item[0]->host_id . "/original-texts/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth " . $item[0]->access_token, "Accept: application/json", "Content-type: application/json"));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    private function YandexWebmasterOverride($url)
    {
        $item = $this->db->query("SELECT * FROM `cl6s3_comments_webmaster` WHERE id = 1 LIMIT 1");

        if (!empty($item[0]->user_id) && !empty($item[0]->host_id) && !empty($item[0]->access_token)) {
            $data = json_encode(array(
                "url" => $url
            ));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.webmaster.yandex.net/v4/user/" . $item[0]->user_id . "/hosts/" . $item[0]->host_id . "/recrawl/queue/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth " . $item[0]->access_token, "Accept: application/json", "Content-type: application/json"));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    private function parseCurseWords(int $comment_id)
    {
        $values = array();
        $words = array();

        $items = $this->db->query("SELECT * FROM `cl6s3_comments_cursewords`");

        if (!empty($items)) {
            foreach ($items as $item) {
                $words[] = $item->text;
            }
        }

        if (!empty($ids)) {
            $items = $this->db->query("SELECT id, description FROM `cl6s3_comments_items` WHERE id = '" . $comment_id . "';");

            if (!empty($items)) {
                foreach ($items as $item) {
                    foreach ($words as $word) {
                        if (strpos($item['description'], $word)) {
                            $description = preg_replace('/([^\d\w]|^)' . $word . '([^\d\w]|$)/iu', '$1***$2', $item['description']);
                            $values[] = "('{$item['id']}', '{$description}')";
                        }
                    }
                }
            }
        }

        if (!empty($values)) {
            $this->db->query("INSERT INTO `cl6s3_comments_items` (id, description) VALUES " . implode(', ', $values) . " ON DUPLICATE KEY UPDATE description = VALUES(description)");
        }
    }

    private function _clearImages(int $comment_id): void
    {
        if (!empty($comment_id)) {
            $images = $this->db->query("SELECT * FROM `cl6s3_comments_images` WHERE item_id = '" . $comment_id . "';");

            if (!empty($images)) {
                foreach ($images as $image) {
                    unlink(__DIR__ . '/../../images/comments/' . $image->thumb);
                    unlink(__DIR__ . '/../../images/comments/' . $image->original);
                }
                $this->db->query("DELETE FROM `cl6s3_comments_images` WHERE item_id = '" . $comment_id . "';");
            }
        }
    }

    private function _clearNotifications(int $comment_id)
    {
        if (!empty($comment_id)) {
            $this->db->query("DELETE FROM `cl6s3_comments_cron` WHERE item_id = '" . $comment_id . "';");
        }
    }

    private function _rate(string $object_group, int $object_id, int $value)
    {
        $rate = 0;
        $vote = 0;
        if ($value > 0) {
            $rate = $value;
            $vote = 1;
        }
        if ($value < 0) {
            $rate = $value;
            $vote = -1;
        }
        if ($object_group == 'city') {
            $this->db->query("UPDATE `cities` SET rate = rate + '" . $rate . "', vote = vote + '" . $vote . "', average = CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END, comments = (SELECT COUNT(*) FROM `cl6s3_comments_items` WHERE object_id = '" . $object_id . "' AND object_group = '" . $object_group . "' AND status = 1) WHERE id = '" . $object_id . "' LIMIT 1");
        }
        if ($object_group == 'memorial') {
            $this->db->query("UPDATE `memorials` SET rate = rate + '" . $rate . "', vote = vote + '" . $vote . "', average = CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END, comments = (SELECT COUNT(*) FROM `cl6s3_comments_items` WHERE object_id = '" . $object_id . "' AND object_group = '" . $object_group . "' AND status = 1) WHERE id = '" . $object_id . "' LIMIT 1");
        }
        if ($object_group == 'hotel') {
            $this->db->query("UPDATE `hotels` SET rate = rate + '" . $rate . "', vote = vote + '" . $vote . "', average = CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END, comments = (SELECT COUNT(*) FROM `cl6s3_comments_items` WHERE object_id = '" . $object_id . "' AND object_group = '" . $object_group . "' AND status = 1) WHERE id = '" . $object_id . "' LIMIT 1");
        }
        if ($object_group == 'restaurant') {
            $this->db->query("UPDATE `restaurants` SET rate = rate + '" . $rate . "', vote = vote + '" . $vote . "', average = CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END, comments = (SELECT COUNT(*) FROM `cl6s3_comments_items` WHERE object_id = '" . $object_id . "' AND object_group = '" . $object_group . "' AND status = 1) WHERE id = '" . $object_id . "' LIMIT 1");
        }
    }

    private function getItem(string $object_group, int $object_id): ?array
    {
        $item = [];

        if ($object_group == 'city') {
            $item = $this->db->query("SELECT * FROM `cities` WHERE `id` = ".$this->db->quote($object_id).";");
            $item['title'] = $item[0]->name;
            $item['url'] = '/' . $item[0]->alias;
        }
        if ($object_group == 'memorial') {
            $item = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($object_id));
            $item['title'] = $item[0]->name;
            $item['url'] = '/' . $item[0]->cityAlias. '/memorial-'.$item[0]->alias.'-'.$item[0]->id;
        }
        if ($object_group == 'hotel') {
            $item = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($object_id));
            $item['title'] = $item[0]->name;
            $item['url'] = '/' . $item[0]->cityAlias. '/hotel-'.$item[0]->alias.'-'.$item[0]->id;
        }
        if ($object_group == 'restaurant') {
            $item = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($object_id));
            $item['title'] = $item[0]->name;
            $item['url'] = '/' . $item[0]->cityAlias. '/restaurant-'.$item[0]->alias.'-'.$item[0]->id;
        }
        return $item;
    }

    public function vote(): array
    {
        $item_id = (int)$_POST['id'];
        $value = (string)$_POST['value'];

        $temp = $this->db->query("SELECT COUNT(*) as itog FROM `cl6s3_comments_votes` WHERE item_id = '" . $item_id . "' AND ip = '" . $this->ip . "' LIMIT 1");

        if (empty($temp[0]->itog)) {
            if ($value == 'up') {
                $this->db->query("INSERT INTO `cl6s3_comments_votes` (item_id, ip, value) VALUES ('" . $item_id . "', '" . $this->ip . "', 1)");
            }
            if ($value == 'down') {
                $this->db->query("INSERT INTO `cl6s3_comments_votes` (item_id, ip, value) VALUES ('" . $item_id . "', '" . $this->ip . "', -1)");
            }
            if ($value == 'up' || $value == 'down') {
                $this->db->query("UPDATE `cl6s3_comments_items` SET isgood = (SELECT SUM(value) FROM `cl6s3_comments_votes` WHERE item_id = '" . $item_id . "' AND value > 0), ispoor = (SELECT SUM(value)*-1 FROM `cl6s3_comments_votes` WHERE item_id = '" . $item_id . "' AND value < 0) WHERE id = '" . $item_id . "';");
            }
            return array(
                'msg' => 'Спасибо, Ваш голос принят!'
            );
        } else {
            return array(
                'msg' => 'Повторное голосование не учитывается!'
            );
        }
    }

    public function votes(): ?array
    {
        $votes = (string)$_POST['votes'];
        $object_id = (int)$_POST['objectid'];
        $object_group = (string)$_POST['objectgroup'];
        if (empty($object_group)) {
            $object_group = 'com_content';
        }

        if ($votes == 'good') {
            $rate = ' AND t1.rate >=4';
        } elseif ($votes == 'neutrally') {
            $rate = ' AND (t1.rate =3 or t1.rate = "")';
        } elseif ($votes == 'bad') {
            $rate = ' AND t1.rate <=2 AND t1.rate != ""';
        } else {
            $rate = '';
        }

        return $this->db->query("SELECT t1.*, t1.username AS guest_name,t2.name AS user_name, t2.id AS registered  FROM `cl6s3_comments_items` as t1
         LEFT JOIN `cl6s3_users` AS t2 ON t1.user_id = t2.id 
         WHERE `object_group` = '" . $object_group . "' AND t1.object_id = '" . $object_id . "' AND t1.status = 1 " . $rate . " ORDER BY t1.created DESC");

    }

    /**
     * Показ и загрузка фото к отзывам
     */
    public function cut(): ?array
    {
        $item_id = (int)$_POST['id'];
        return $this->db->query("SELECT * FROM  `cl6s3_comments_images` WHERE item_id = '" . $item_id . "' ORDER BY id ASC");
    }

    public function addImage()
    {
        if(!empty($_FILES['file'])){
            $file = (array)$_FILES['file'];
        }
        if(!empty($_POST['attach'])){
            $attach = (string)$_POST['attach'];
        }

        if (!empty($attach)) {
            if (!empty($file['tmp_name'])) {
                $handle = new UploadFile($file['tmp_name']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body = md5(uniqid(rand(), 1));
                    $handle->mime_check = true;
                    $handle->allowed = array('image/jpeg', 'image/png');
                    if ($handle->image_src_x > 800) {
                        $handle->image_resize = true;
                        $handle->image_ratio_y = true;
                        $handle->image_x = 800;
                        if ($handle->image_src_type == 'jpg') {
                            $handle->jpeg_quality = 90;
                        }
                    }
                    $handle->process($this->dir);
                    if ($handle->processed) {
                        $original = $handle->file_dst_name;
                        $handle->clean();
                        $handle = new UploadFile($this->dir . '/' . $original);
                        if ($handle->uploaded) {
                            $handle->file_new_name_body = md5(uniqid(rand(), 1));
                            $handle->file_name_body_add = '_thumb';
                            $handle->image_resize = true;
                            $handle->image_ratio_crop = true;
                            $handle->image_y = 200;
                            $handle->image_x = 200;
                            $handle->process($this->dir);
                            if ($handle->processed) {
                                $thumb = $handle->file_dst_name;
                                $this->db->query("INSERT INTO `cl6s3_comments_images` (created, attach, thumb, original) VALUES (NOW(), " . $this->db->quote($attach) . ", " . $this->db->quote($thumb) . ", " . $this->db->quote($original) . ")");

                                $id = $this->db->getLastInsertId();

                                return array(
                                    'status' => 1,
                                    'attach' => $attach,
                                    'thumb' => $thumb,
                                    'id' => $id
                                );
                            }
                        }
                    } else {
                        return array(
                            'status' => 2,
                            'msg' => 'Ошибка : ' . $handle->error
                        );
                    }
                }
            } else {
                return array(
                    'status' => 2,
                    'msg' => 'Укажите файл для загрузки.'
                );
            }
        } else {
            return array(
                'status' => 2,
                'msg' => 'Ошибка загрузки изображения.'
            );
        }
    }

    public function removeImage(): array
    {
        $id_img = (int)$_POST['id_img'];
        $attach = (string)$_POST['attach'];

        $item = $this->db->query("SELECT * FROM `cl6s3_comments_images` WHERE id = '" . $id_img . "' LIMIT 1");

        if (!empty($user_id) && !empty($item[0]->item_id)) {
            $temp = $this->db->query("SELECT COUNT(*) FROM `cl6s3_comments_images` WHERE id = '" . $item[0]->item_id . "' AND user_id = '" . $user_id . "' LIMIT 1");
        } else {
            $temp = $this->db->query("SELECT COUNT(*) FROM `cl6s3_comments_images` WHERE item_id = 0 AND id = '" . $id_img . "' AND attach = '" . $attach . "' LIMIT 1");
        }

        if (!empty($temp)) {
            self::removeImages($id_img, $item);

            return array(
                'status' => 1
            );
        }
        return array(
            'status' => 2,
            'msg' => 'Ошибка удаления изображения'
        );
    }

    private function removeImages(int $id_img, array $item): void
    {
        $db = Db::getInstance();

        unlink(__DIR__ . '/../../images/comments/' . $item[0]->thumb);
        unlink(__DIR__ . '/../../images/comments/' . $item[0]->original);

        $this->db->query("DELETE FROM `cl6s3_comments_images` WHERE id = '" . $id_img . "';");
        $this->db->query("UPDATE `cl6s3_comments_items` SET images = (SELECT COUNT(*) FROM `cl6s3_comments_images` WHERE `item_id` = '" . $item[0]->item_id . "') WHERE id ='" . $item[0]->item_id . "';");

    }

    /**
     * Функция очистки кеша Nginx
     * @param string $value
     * @return void
     */
    static function clearCache(string $value): void
    {
        if (!empty($value)) {
            $data = parse_url($value);
            $filename = md5('GET|rus-trip.ru|' . $data['path']);
            if (file_exists('/var/cache/nginx/rus/' . substr($filename, -1) . '/' . substr($filename, -3, 2) . '/' . $filename)) {
                unlink('/var/cache/nginx/rus/' . substr($filename, -1) . '/' . substr($filename, -3, 2) . '/' . $filename);
            }
            return;
        }
    }
}