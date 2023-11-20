<?php

namespace View;

class View
{
    private string $templatesPath;
    private string $siteName;
    private array $extraVars = [];

    public function __construct(string $templatesPath)
    {
        $this->siteName = (require __DIR__ . '/../settings.php')['main']['site'];
        $this->templatesPath = $templatesPath;
    }

    public function setVar(string $name, $value): void
    {
        $this->extraVars[$name] = $value;
    }

    public function renderHtml(string $templateName, array $vars = [], int $code = 200): void
    {
        http_response_code($code);
        extract($this->extraVars);
        extract($vars);
        $siteName = $this->siteName;
        //Буфер вывода
        ob_start();
        include $this->templatesPath . '/' . $templateName;
        $buffer = ob_get_contents();
        ob_end_clean();

         //if($_SERVER['REMOTE_ADDR'] != '37.110.34.79'){
             //Минификация JS и CSS, закомментируй строку ниже если нужно выключить
             if (strpos($buffer, '<head>')){
                 $buffer = self::minify($buffer);
             }
        // }

        //Добавляем рекламу
       // $buffer = self::moduleReplacement($buffer);

        echo $buffer;
    }

    /**
     * @param $buffer
     * @return string
     * Замена позиций модулей на рекламный код
     */
    private function moduleReplacement($buffer): string
    {
        $banner2 = "<div id=\"yandex_rtb_R-A-60558-5\" style=\"max-height: 250px;overflow: hidden;\"></div> <script>window.yaContextCb.push(()=>{ Ya.Context.AdvManager.render({ renderTo: 'yandex_rtb_R-A-60558-5', blockId: 'R-A-60558-5' }) })</script>".PHP_EOL;
        $breadcrumb = "<div id=\"yandex_rtb_R-A-60558-4\" style=\"max-height: 250px;overflow: hidden;\"></div> <script>window.yaContextCb.push(()=>{ Ya.Context.AdvManager.render({ renderTo: 'yandex_rtb_R-A-60558-4', blockId: 'R-A-60558-4' }) })</script>".PHP_EOL;
        $moduleComments = "<div id=\"yandex_rtb_R-A-60558-6\"></div> <script>window.yaContextCb.push(()=>{ Ya.Context.AdvManager.render({ renderTo: 'yandex_rtb_R-A-60558-6', blockId: 'R-A-60558-6' }) })</script>".PHP_EOL;
        $reklamaOver10 = "<div id=\"yandex_rtb_R-A-60558-3\"></div> <script>window.yaContextCb.push(()=>{ Ya.Context.AdvManager.render({ renderTo: 'yandex_rtb_R-A-60558-3', blockId: 'R-A-60558-3' }) })</script>".PHP_EOL;
        //$floorAd = '<script>window.yaContextCb.push(()=>{ Ya.Context.AdvManager.render({ "blockId": "R-A-60558-7", "type": "floorAd" }) }) </script>';
        return str_replace(array("<!-- banner2 -->", "<!-- breadcrumb -->", "<!-- module-maps -->", "<!-- reklama-over-10 -->", "<!-- user9 -->"), array($banner2, $breadcrumb, $moduleComments, $reklamaOver10), $buffer);
    }

    private function minify($buffer): string
    {
        //css
/*        preg_match_all('~<link rel="stylesheet" href="(.*)">~mU', $buffer, $matchesCss);
        $styles = '';
        $hashCss = md5(serialize($matchesCss[1]));
        $filenameCss = __DIR__ . '/../templates/cache/' . $hashCss . '.css';
        if (!file_exists($filenameCss)) {
            foreach ($matchesCss[1] as $style) {
                $style = str_replace('/..', '', $style);
                $css = self::compress(file_get_contents(__DIR__ . '/..' . $style));
                //$css = file_get_contents(__DIR__ . '/..'.$style);
                $styles .= $css . PHP_EOL;
            }
            file_put_contents(__DIR__ . '/../templates/cache/' . $hashCss . '.css', $styles);
        }
        $buffer = preg_replace('~<!--css-->(.*?)<!--cssEnd-->~s', '<link rel="stylesheet" href="/templates/cache/' . $hashCss . '.css">' . PHP_EOL, $buffer);*/

        //js
        preg_match_all('~<script src="(.*)></script>~mU', $buffer, $matchesJs);
        if(!empty($matchesJs[1])){
            $scripts = '';
            $hashJs = md5(serialize($matchesJs[1]));
            $filenameJs = __DIR__ . '/../templates/cache/' . $hashJs . '.js';
            if (!file_exists($filenameJs)) {
                foreach ($matchesJs[1] as $script) {
                    if (preg_match('~/templates/~', $script)) {
                        $script = str_replace(array('"', '/..'), '', $script);
                        $js = self::compress(file_get_contents(__DIR__ . '/..' . $script));
                        //$js = file_get_contents(__DIR__ . '/..' . $script);
                        $scripts .= $js . PHP_EOL;
                    }
                }
                file_put_contents(__DIR__ . '/../templates/cache/' . $hashJs . '.js', $scripts);
            }
            $buffer = preg_replace('~<!--js-->(.*?)<!--jsEnd-->~s', '<script defer src="/templates/cache/' . $hashJs . '.js"></script>' . PHP_EOL, $buffer);
        }
        return $buffer;
    }

    private function compress(string $code): string
    {
        // комментарии кода
        $code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
        // удаляем табуляции и переходы на новую строку
        $code = str_replace(array("\r\n", "\r", "\n", "\t"), '', $code);
        // удаляем пробелы
        return preg_replace(array('~;\s+~', '~(\s+{|{\s+)~', '~(\s+}|}\s+)~', '~:\s+~'), array(';', '{', '}', ':'), $code);
    }
}