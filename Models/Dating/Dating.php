<?php

namespace Models\Dating;

use Services\Db;
use stdClass;

class Dating
{
    protected object $db;
    public static array $values = array();

    public array $config = array(
        '#ä|æ|ǽ#' => 'ae',
        '#ö|œ#' => 'oe',
        '#ü#' => 'ue',
        '#Ä#' => 'Ae',
        '#Ü#' => 'Ue',
        '#Ö#' => 'Oe',
        '#À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А#' => 'A',
        '#à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а#' => 'a',
        '#Ç|Ć|Ĉ|Ċ|Č|Ц#' => 'C',
        '#ç|ć|ĉ|ċ|č|ц#' => 'c',
        '#Ð|Ď|Đ|Д#' => 'D',
        '#ð|ď|đ|д#' => 'd',
        '#È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э#' => 'E',
        '#è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э#' => 'e',
        '#Ĝ|Ğ|Ġ|Ģ|Г#' => 'G',
        '#ĝ|ğ|ġ|ģ|г#' => 'g',
        '#Ĥ|Ħ|Х#' => 'H',
        '#ĥ|ħ|х#' => 'h',
        '#Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И#' => 'I',
        '#ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и#' => 'i',
        '#Ĵ#' => 'J',
        '#ĵ#' => 'j',
        '#Ķ|К#' => 'K',
        '#ķ|к#' => 'k',
        '#Ĺ|Ļ|Ľ|Ŀ|Ł|Л#' => 'L',
        '#ĺ|ļ|ľ|ŀ|ł|л#' => 'l',
        '#Ñ|Ń|Ņ|Ň|Н#' => 'N',
        '#ñ|ń|ņ|ň|ŉ|н#' => 'n',
        '#Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О#' => 'O',
        '#ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о#' => 'o',
        '#Ŕ|Ŗ|Ř|Р#' => 'R',
        '#ŕ|ŗ|ř|р#' => 'r',
        '#Ś|Ŝ|Ş|Ș|Š|С#' => 'S',
        '#ś|ŝ|ş|ș|š|ſ|с#' => 's',
        '#Ţ|Ț|Ť|Ŧ|Т#' => 'T',
        '#ţ|ț|ť|ŧ|т#' => 't',
        '#Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У#' => 'U',
        '#ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у#' => 'u',
        '#Ý|Ÿ|Ŷ|Й|Ы#' => 'Y',
        '#ý|ÿ|ŷ|й|ы#' => 'y',
        '#Ŵ#' => 'W',
        '#ŵ#' => 'w',
        '#Ź|Ż|Ž|З#' => 'Z',
        '#ź|ż|ž|з#' => 'z',
        '#Æ|Ǽ#' => 'AE',
        '#ß#' => 'ss',
        '#Ĳ#' => 'IJ',
        '#ĳ#' => 'ij',
        '#Œ#' => 'OE',
        '#ƒ|ф#' => 'f',
        '#б#' => 'b',
        '#в#' => 'v',
        '#ж#' => 'zh',
        '#м#' => 'm',
        '#п#' => 'p',
        '#ч#' => 'ch',
        '#ш#' => 'sh',
        '#щ#' => 'sch',
        '#ь#' => '',
        '#ъ#' => '',
        '#ю#' => 'yu',
        '#я#' => 'ya',
        '#ї#' => 'yi',
        '#є#' => 'ye',
        '#Б#' => 'B',
        '#В#' => 'V',
        '#Ж#' => 'Zh',
        '#М#' => 'M',
        '#П#' => 'P',
        '#Ф#' => 'F',
        '#Ч#' => 'Ch',
        '#Ш#' => 'Sh',
        '#Щ#' => 'Sch',
        '#Ь#' => '',
        '#Ъ#' => '',
        '#Ю#' => 'Yu',
        '#Я#' => 'Ya',
        '#Ї#' => 'yi',
        '#Є#' => 'ye',
    );

    protected array $country = array(
        0 => '--- не имеет значения ---',
        4 => 'Австралия',
        63 => 'Австрия',
        81 => 'Азербайджан',
        173 => 'Ангилья',
        177 => 'Аргентина',
        245 => 'Армения',
        248 => 'Белоруссия',
        401 => 'Белиз',
        404 => 'Бельгия',
        425 => 'Бермудские Острова',
        428 => 'Болгария',
        467 => 'Бразилия',
        616 => 'Великобритания',
        924 => 'Венгрия',
        971 => 'Вьетнам',
        994 => 'Гаити',
        1007 => 'Гваделупа',
        1012 => 'Германия',
        1206 => 'Нидерланды',
        1258 => 'Греция',
        1280 => 'Грузия',
        1366 => 'Дания',
        1380 => 'Египет',
        1393 => 'Израиль',
        1451 => 'Индия',
        1663 => 'Иран',
        1696 => 'Ирландия',
        1707 => 'Испания',
        1786 => 'Италия',
        1894 => 'Казахстан',
        2163 => 'Камерун',
        2172 => 'Канада',
        2297 => 'Кипр',
        2303 => 'Киргизия',
        2374 => 'Китай',
        2430 => 'Коста-Рика',
        2443 => 'Кувейт',
        2448 => 'Латвия',
        2509 => 'Ливия',
        2514 => 'Литва',
        2614 => 'Люксембург',
        2617 => 'Мексика',
        2788 => 'Молдавия',
        2833 => 'Монако',
        2837 => 'Новая Зеландия',
        2880 => 'Норвегия',
        2897 => 'Польша',
        3141 => 'Португалия',
        3156 => 'Реюньон',
        3159 => 'Россия',
        4963 => 'Албания',
        5647 => 'Сальвадор',
        5666 => 'Словакия',
        5673 => 'Словения',
        5678 => 'Суринам',
        5681 => 'Соединенные Штаты Америки',
        9575 => 'Таджикистан',
        9618 => 'Боливия',
        9638 => 'Туркменистан',
        9701 => 'Туркс и Кейкос',
        9705 => 'Турция',
        9782 => 'Уганда',
        9787 => 'Узбекистан',
        9908 => 'Украина',
        10648 => 'Финляндия',
        10668 => 'Франция',
        10874 => 'Чехия',
        10904 => 'Швейцария',
        10933 => 'Швеция',
        10968 => 'Эстония',
        10996 => 'Босния и Герцеговина',
        11002 => 'Югославия',
        11014 => 'Южная Корея',
        11060 => 'Япония',
        11365 => 'Венесуэла',
        11366 => 'Камбоджа',
        11367 => 'Колумбия',
        11370 => 'Саудовская Аравия',
        11371 => 'Сербия',
        277553 => 'Хорватия',
        277555 => 'Румыния',
        277557 => 'Сянган (Гонконг)',
        277559 => 'Индонезия',
        277561 => 'Иордания',
        277563 => 'Малайзия',
        277565 => 'Сингапур',
        277567 => 'Тайвань',
        582029 => 'Багамские Острова',
        582031 => 'Чили',
        582039 => 'Исландия',
        582040 => 'Северная Корея',
        582041 => 'Македония',
        582043 => 'Мальта',
        582044 => 'Пакистан',
        582045 => 'Папуа-Новая Гвинея',
        582046 => 'Перу',
        582047 => 'Филиппины',
        582050 => 'Таиланд',
        582051 => 'О.А.Э.',
        582052 => 'Гренландия',
        582056 => 'Зимбабве',
        582057 => 'Кения',
        582059 => 'Алжир',
        582060 => 'Ливан',
        582061 => 'Ботсвана',
        582062 => 'Танзания',
        582063 => 'Намибия',
        582064 => 'Эквадор',
        582065 => 'Марокко',
        582066 => 'Гана',
        582067 => 'Сирия',
        582068 => 'Непал',
        582069 => 'Мавритания',
        582071 => 'Сейшельские острова',
        582072 => 'Парагвай',
        582075 => 'Уругвая',
        582076 => 'Конго (Brazzaville)',
        582077 => 'Куба',
        582080 => 'Нигерия',
        582081 => 'Замбия',
        582082 => 'Мозамбик',
        582086 => 'Ангола',
        582087 => 'Шри-Ланка',
        582088 => 'Эфиопия',
        582090 => 'Тунис',
        582093 => 'Панама',
        582094 => 'Малави',
        582095 => 'Лихтенштейн',
        582097 => 'Бахрейн',
        582098 => 'Барбадос',
        582101 => 'Чад',
        582105 => 'Остров Мэн',
        582106 => 'Ямайка',
        582108 => 'Мали',
        582109 => 'Мадагаскар',
        582110 => 'Сенегал',
        582112 => 'Того',
        2567393 => 'Гондурас',
        2577958 => 'Доминиканская Республика',
        2687701 => 'Монголия',
        3410238 => 'Ирак',
        3661568 => 'Южная Африка',
        7716093 => 'Арулько',
        20738587 => 'Гибралтар',
        23269622 => 'Афганистан',
        23269623 => 'Андорра',
        23269625 => 'Антигуа и Барбуда',
        23269627 => 'Бангладеш',
        23269629 => 'Бенин',
        23269630 => 'Бутан',
        23269633 => 'Британские Виргинские о-ва',
        23269634 => 'Бруней',
        23269635 => 'Буркина Фасо',
        23269636 => 'Бурунди',
        23269638 => 'Кабо-Верде',
        23269645 => 'Коморские Острова',
        23269646 => 'Конго (Kinshasa)',
        23269647 => 'Кука Острова',
        23269649 => 'Кот-д\'Ивуар',
        23269650 => 'Джибути',
        23269652 => 'Восточный Тимор',
        23269653 => 'Экваториальная Гвинея',
        23269654 => 'Эритрея',
        23269657 => 'Фиджи',
        23269658 => 'Французская Гвиана',
        23269659 => 'Французская Полинезия',
        23269661 => 'Габон',
        23269662 => 'Гамбия',
        23269665 => 'Гренада',
        23269666 => 'Гватемала',
        23269667 => 'Гернси о-в',
        23269668 => 'Гвинея',
        23269669 => 'Гвинея-Бисау',
        23269670 => 'Гайана',
        23269674 => 'Джерси о-в',
        23269676 => 'Кирибати',
        23269677 => 'Лаос',
        23269678 => 'Лесото',
        23269679 => 'Либерия',
        23269681 => 'Мальдивы',
        23269682 => 'Мартиника',
        23269683 => 'Маврикий',
        23269686 => 'Мьянма',
        23269687 => 'Науру',
        23269688 => 'Антильские острова (Нид.)',
        23269689 => 'Новая Каледония',
        23269690 => 'Никарагуа',
        23269691 => 'Нигер',
        23269693 => 'Норфолк',
        23269694 => 'Оман',
        23269696 => 'Питкэрн',
        23269697 => 'Катар',
        23269698 => 'Руанда',
        23269699 => 'Святой Елены остров',
        23269700 => 'Сент-Китс и Невис',
        23269701 => 'Сент-Люсия',
        23269702 => 'Сен-Пьер и Микелон',
        23269703 => 'Сент Винсент и Гренадины',
        23269704 => 'Западное Самоа',
        23269705 => 'Сан-Марино',
        23269706 => 'Сан-Томе и Принсипи',
        23269708 => 'Сьерра-Леоне',
        23269709 => 'Соломоновы Острова',
        23269710 => 'Сомали',
        23269713 => 'Судан',
        23269715 => 'Свазиленд',
        23269716 => 'Токелау',
        23269717 => 'Тонга',
        23269718 => 'Тринидад и Тобаго',
        23269720 => 'Тувалу',
        23269721 => 'Вануату',
        23269722 => 'Валлис и Футуна о-ва',
        23269723 => 'Западная Сахара',
        23269724 => 'Йемен',
        34851252 => 'Пуэрто-Рико',
        297039407 => 'Южная Осетия',
        298612880 => 'Черногория',
    );

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function dating(object $city): stdClass
    {
        $Dating = new stdClass();
        $Dating->country = new stdClass();
        $Dating->region = new stdClass();
        $Dating->city = new stdClass();


        // load countries and validate country id
        $Dating->country->items = $this->country;
        if (!$Dating->country->id || !array_key_exists($Dating->country->id, $Dating->country->items)) {
            $Dating->country->id = 3159; // Россия
        }

        // load region and city data
        $Dating->region = self::load_dating_object($city->federal_subject, $Dating->region, $Dating->country->id, 'regions', 'reg');
        $Dating->city = self::load_dating_object($city->name, $Dating->city, $Dating->region->id, 'cities', 'cities');

        // build fields
        $Fields = new stdClass();
        foreach ($Dating as $name => $obj) {
            $Fields->$name = self::build_dropdown($name, array(
                'title' => $name,
                'value' => $obj->id,
                'required' => false,
                'fn' => 'build_dropdown',
                'options' => $obj->items,
                'attrs' => array(
                    'id' => "dating-location-{$name}"
                )
            ));
        }
        return $Fields;
    }

    public static function build_dropdown($name, $params = array()): string
    {
        $default_value = self::set_value($name, $params['value']);
        $options = '';

        foreach ($params['options'] as $val => $option) {
            if (is_string($option)) {
                $selected = (strcmp($default_value, $val) === 0) ? "selected='selected'" : '';
                $options .= "<option {$selected} value='{$val}'>{$option}</option>";
            } else {
                $group_options = '';
                foreach ($option as $subval => $group) {
                    $selected = ($default_value == $subval) ? "selected='selected'" : '';
                    $group_options .= "<option {$selected} value='{$subval}'>{$group}</option>";
                }
                $options .= "<optgroup label='{$val}'>{$group_options}</optgroup>";
            }
        }

        $params['attrs']['name'] = $name;

        $elem_attrs = self::build_attributes($params['attrs']);
        return "<select {$elem_attrs}>{$options}</select>";
    }

    private static function build_attributes($attrs): string
    {
        foreach ($attrs as $attr => $val) {
            $attrs[$attr] = "{$attr}='{$val}'";
        }
        return implode(' ', $attrs);
    }

    private static function set_value($name, $value)
    {
        if (is_string($value) && strpos($value, 'config::') === 0) {
            if (is_callable($value)) {
                $value = call_user_func($value, $name);
            } else {
                if (preg_match('#config::get\[(?P<cfgfile>.+?)\]#', $value, $m)) {
                    self::load($m['cfgfile']);
                    $value = self::get($m['cfgfile'], $name);
                }
            }
        }

        if (strpos($name, 'lines:') === 0) {
            $value = implode("\n", $value);
        }

        if (is_string($value)) {
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            return self::safe_html($value);
        } else {
            return $value;
        }
    }

    public static function load($filename)
    {
        if (!self::get($filename)) {
            $config = array();

            /*$cfg_file = CORE . "config/{$filename}.php";
            if (is_file($cfg_file)) {
                require_once $cfg_file;
            }*/
            self::$values[$filename] = $config;
        }
        return self::get($filename);
    }

    public static function get($key, $subkey = null)
    {
        if (isset(self::$values[$key])) {
            if ($subkey) {
                return isset(self::$values[$key][$subkey]) ? self::$values[$key][$subkey] : false;
            } else {
                return self::$values[$key];
            }
        } else {
            return false;
        }
    }

    public static function safe_html($str): string
    {
        return trim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
    }

    private function load_dating_object($itemtitle, $item, $parentid, $type, $key)
    {
        if ($parentid) {
            if (!$item->items) {
                $jsdata = file_get_contents("http://love.rus-trip.ru/?a=geojson&fs={$key}_{$parentid}");
                $item->items = (array)json_decode($jsdata, true);
                // cache::save('dating', "{$type}_{$parentid}", $jsdata);
            }

            if (!array_key_exists($item->id, $item->items)) {
                $item->id = key($item->items);

                if ($itemtitle) {
                    $alias = $this->translit($itemtitle);
                    foreach ($item->items as $id => $title) {
                        if (strpos($this->translit($title), $alias) !== false) {
                            $item->id = $id;
                            break;
                        }
                    }
                }
            }
        } else {
            $item->items = array();
        }

        //array_unshift($item->items, '--- не имеет значения ---');
        return $item;
    }

    public function translit($str): string
    {
        $chars = $this->get_foreign_chars();
        $str = self::decode_html(preg_replace($chars['keys'], $chars['values'], $str));
        return trim(preg_replace('#[^a-z0-9]+#i', '-', mb_strtolower($str)), '-');
    }

    private function get_foreign_chars(): ?array
    {
        static $chars = null;
        if (!$chars) {
            $all_chars = $this->config;
            $chars = array(
                'keys' => array_keys($all_chars),
                'values' => array_values($all_chars)
            );
        }

        return $chars;
    }

    public static function decode_html($str): string
    {
        $html = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        if ($str == $html) {
            return $html;
        } else {
            return self::decode_html($html);
        }
    }
}