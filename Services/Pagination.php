<?php

namespace Services;

class Pagination
{

    public $currentPage;
    public $perpage;
    public $total;
    public $countPages;
    public $uri;

    public function __construct($page, $perpage, $total){
        $this->perpage = $perpage;
        $this->total = $total;
        $this->countPages = $this->getCountPages();
        $this->currentPage = $this->getCurrentPage($page);
        $this->uri = $this->getParams();
    }

    public function __toString(){
        return $this->getHtml();
    }

    public function getHtml(){
        $back = null; // ссылка НАЗАД
        $forward = null; // ссылка ВПЕРЕД
        $startpage = null; // ссылка В НАЧАЛО
        $endpage = null; // ссылка В КОНЕЦ
        $page2left = null; // вторая страница слева
        $page1left = null; // первая страница слева
        $page2right = null; // вторая страница справа
        $page1right = null; // первая страница справа

        if( $this->currentPage > 1 ){
            $back = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage - 1). "#type_comments'>&lt;</a></span>";
        }

        if( $this->currentPage < $this->countPages ){
            $forward = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage + 1). "#type_comments'>&gt;</a></span>";
        }

        if( $this->currentPage > 3 ){
            $startpage = "<span><a class='pagenav' href='{$this->uri}start=1#type_comments'>&laquo;</a></span>";
        }
        if( $this->currentPage < ($this->countPages - 2) ){
            $endpage = "<span><a class='pagenav' href='{$this->uri}start={$this->countPages}#type_comments'>&raquo;</a></span>";
        }
        if( $this->currentPage - 2 > 0 ){
            $page2left = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage-2). "#type_comments'>" .($this->currentPage - 2). "</a></span>";
        }
        if( $this->currentPage - 1 > 0 ){
            $page1left = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage-1). "#type_comments'>" .($this->currentPage-1). "</a></span>";
        }
        if( $this->currentPage + 1 <= $this->countPages ){
            $page1right = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage + 1). "#type_comments'>" .($this->currentPage+1). "</a></span>";
        }
        if( $this->currentPage + 2 <= $this->countPages ){
            $page2right = "<span><a class='pagenav' href='{$this->uri}start=" .($this->currentPage + 2). "#type_comments'>" .($this->currentPage + 2). "</a></span>";
        }

        return '<div class="pagination">' . $startpage.$back.$page2left.$page1left.'<span class="pagenav"><a>'.$this->currentPage.'</a></span>'.$page1right.$page2right.$forward.$endpage . '</div>';
    }

    public function getCountPages(){
        return ceil($this->total / $this->perpage) ?: 1;
    }

    public function getCurrentPage($page){
        if(!$page || $page < 1) $page = 1;
        if($page > $this->countPages) $page = $this->countPages;
        return $page;
    }

    public function getStart(){
        return ($this->currentPage - 1) * $this->perpage;
    }

    public function getParams(){
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $uri = $url[0] . '?';
        if(isset($url[1]) && $url[1] != ''){
            $params = explode('&', $url[1]);
            foreach($params as $param){
                if(!preg_match("#start=#", $param)) $uri .= "{$param}&amp;";
            }
        }
        return $uri;
    }
}