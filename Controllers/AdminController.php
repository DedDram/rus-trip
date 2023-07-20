<?php

namespace Controllers;

use Exceptions\DbException;
use Exceptions\ForbiddenException;
use Exceptions\NotFoundException;
use Models\Admin\AdminSchools;
use Models\Comments\Webmaster;

class AdminController extends AbstractUsersAuthController
{
     protected object $adminSchools;
    /**
     * @throws ForbiddenException
     */
    public function __construct()
    {
        parent::__construct();
        if (empty($this->user) || !$this->user->isAdmin()) {
            throw new ForbiddenException();
        }
        $this->adminSchools = new AdminSchools();
    }

    /**
     * @throws DbException
     */
    public function getResponse()
    {
        $data = [];
        //добавляем адрес
        if (!empty($_POST['task']) && !empty($_POST['item_id']) && !empty($_POST['geo_code']) && $_POST['task'] == 'addAddress') {
            $data = $this->adminSchools->addAddress((int) $_POST['item_id'], addslashes($_POST['geo_code']));
        }
        //удаляем адрес
        if (!empty($_POST['task']) && $_POST['task'] == 'delAddress' && !empty($_POST['id'])) {
            $data = $this->adminSchools->delAddress((int) $_POST['id']);
        }
        //Обновляем инфу о школе
        if (!empty($_POST['task']) && $_POST['task'] == 'updateItem' && !empty($_POST['id'])) {
            $data = $this->adminSchools->updateSchool($_POST);
            $id = (int) $_POST['id'];
            header("Location: /admin/schools?id=$id&task=edit", true, 301);
        }
        //добавляем ЕГЭ по ср. баллу
        if (!empty($_POST['task']) && $_POST['task'] == 'addExam' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->addExam($_POST);
        }
        //удаляем ЕГЭ по ср. баллу
        if (!empty($_POST['task']) && $_POST['task'] == 'delExam' && !empty($_POST['id'])) {
            $data = $this->adminSchools->delExam($_POST['id']);
        }
        //добавляем ЕГЭ по 3 предметам
        if (!empty($_POST['task']) && $_POST['task'] == 'addExam_' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->addExam_($_POST);
        }
        //удаляем ЕГЭ по 3 предметам
        if (!empty($_POST['task']) && $_POST['task'] == 'delExam_' && !empty($_POST['id'])) {
            $data = $this->adminSchools->delExam_($_POST['id']);
        }
        //добавляем главное фото школы
        if (!empty($_POST['task']) && $_POST['task'] == 'addPreview' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->addPreview($_POST, $_FILES);
        }
        //удаляем главное фото школы
        if (!empty($_POST['task']) && $_POST['task'] == 'delPreview' && !empty($_POST['id'])) {
            $data = $this->adminSchools->_delPreview($_POST['id']);
        }
        //добавляем поле (телефон, мыло и т.п.)
        if (!empty($_POST['task']) && $_POST['task'] == 'addField' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->addField($_POST);
        }
        //удаляем поле (телефон, мыло и т.п.)
        if (!empty($_POST['task']) && $_POST['task'] == 'delField' && !empty($_POST['id'])) {
            $data = $this->adminSchools->delField($_POST['id']);
        }
        //копируем школу
        if (!empty($_POST['CopySchool']) && $_POST['CopySchool'] == 'Копировать' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->copy($_POST['item_id']);
        }
        //удаляем школу и все данные о ней
        if (!empty($_POST['DeleteSchool']) && $_POST['DeleteSchool'] == 'Удалить' && !empty($_POST['item_id'])) {
            $data = $this->adminSchools->delete($_POST['item_id']);
            header("Location: /admin/schools", true, 301);
        }
        //обновляем ближайшие школы выбранного города
        if (!empty($_POST['task']) && $_POST['task'] == 'geoNearby' && !empty($_POST['city'])) {
            $data = $this->adminSchools->geoNearby((string) $_POST['city']);
        }

        $this->view->renderHtml('json/json.php', [
            'data' => $data,
        ]);
    }

    public function main()
    {
        $this->view->renderHtml('admin/main.php', ['title' => 'Админка']);
    }
    public function schools()
    {
        $schools = $this->adminSchools->getSchools();
        $this->view->renderHtml('admin/schools.php', ['schools' => $schools, 'title' => 'Школы']);
    }

    /**
     * @throws NotFoundException
     */
    public function school(int $school_id)
    {
        $school = $this->adminSchools->getSchool($school_id);
        $this->view->renderHtml('admin/school.php', ['school' => $school, 'title' => $school->name]);
    }

    /**
     * @throws NotFoundException
     */
    public function editSchool(int $school_id)
    {
        $school = $this->adminSchools->editSchool($school_id);
        $fields = $this->adminSchools->getFields($school_id);
        $sections = $this->adminSchools->getSections();
        $categories = $this->adminSchools->getCategories();
        $types = $this->adminSchools->getTypes();
        $exam = $this->adminSchools->getExam($school_id);
        $exam_ = $this->adminSchools->getExam_($school_id);
        $address = $this->adminSchools->getAddress($school_id);
        $style = '<link rel="stylesheet" href="/../templates/admin/css/admin.item.css">' . PHP_EOL;
        $style .= '<link rel="stylesheet" href="/../templates/main/css/modal.css">' . PHP_EOL;
        $script = '<script src="/../templates/schools/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/admin/js/jquery-ui.min.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/admin/js/admin.item.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/modal.js"></script>' . PHP_EOL;
        $this->view->setVar('style', $style);
        $this->view->setVar('script', $script);
        $this->view->renderHtml('admin/editSchool.php',
            [
                'item' => $school,
                'fields' => $fields,
                'sections' => $sections,
                'types' => $types,
                'exam' => $exam,
                'exam_' => $exam_,
                'addresses' => $address,
                'categories' => $categories,
                'title' => 'Админ - '.$school->name
            ]);
    }

    public function webmaster()
    {
        $webmaster = new Webmaster();
        if(empty($_POST)){
            $item = $webmaster->getItem();
            $this->view->renderHtml('admin/webmaster.php',
                [
                    'item' => $item,
                    'title' => 'Обновление ключа уникальных текстов',
                ]);
        }else{
            $token = $webmaster->getToken();
            header("Location: /admin/webmaster?task=update", true, 301);
        }
    }

    public function geoNearby()
    {
        $script = '<script src="/../templates/admin/js/geoNearby.js"></script>' . PHP_EOL;
        $this->view->setVar('script', $script);
        $this->view->renderHtml('admin/geoNearby.php', ['title' => 'Обновить ближайшие школы']);
    }
}