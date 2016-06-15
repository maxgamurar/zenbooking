<?php

class indexController extends Controller {

    protected function init() {
        $this->db = new MySqlDataAdapter($this->cfg['db']['hostname'], $this->cfg['db']['username'], $this->cfg['db']['password'], $this->cfg['db']['database']);
    }

    public function index($params) {
        
        //set calendar year/month from request or current date year/month        
        if ($params['id'] && strlen($params['id']) == 6) {
            $_current_ym = $params['id'];
        } else {
            $_current_ym = date($this->_model->_date_format);
        }

        $data = $this->_model->read($_current_ym);

        if (MyHelpers::isAjax()) {
            header('Content-type: application/json');
            echo $data;
        } else {
            $_ym_arr = $this->_model->getDateYearMonth($_current_ym);
            $this->view->set('date_year', $_ym_arr['y']);
            $this->view->set('date_month', $_ym_arr['m']);
            $this->view->set('nav_month_next', $this->navGetNextPrevMonth($_ym_arr));
            $this->view->set('nav_month_prev', $this->navGetNextPrevMonth($_ym_arr, false));
            $this->view->set('nav_year_next', $this->navGetNextPrevYear($_ym_arr));
            $this->view->set('nav_year_prev', $this->navGetNextPrevYear($_ym_arr, false));
            $this->view->set('days_in_month', cal_days_in_month(CAL_GREGORIAN, $_ym_arr['m'], $_ym_arr['y']));
            $this->view->set('rooms', $this->_model->getRoomswithBookings($_ym_arr));
            return $this->view();
        }
    }

    public function dobook() {
        if (MyHelpers::isAjax()) {

            $_result = ['success' => 1];

            $book_result = $this->_model->updateRoomBooking($_GET['_room_id'], $_GET['_year'], $_GET['_month'], $_GET['_day'], $_GET['qty']);
            if (!$book_result) {
                $_result['success'] = 0;
            }
            header('Content-type: application/json');
            echo(json_encode($_result));
            exit();
        }
    }

    public function navGetNextPrevMonth($_ym = array(), $_isnext = true) {
        return date($this->_model->_date_format, strtotime(($_isnext ? '+' : '-') . '1 month', strtotime($_ym['y'] . '-' . $_ym['m'] . '-01')));
    }

    public function navGetNextPrevYear($_ym = array(), $_isnext = true) {
        return date($this->_model->_date_format, strtotime(($_isnext ? '+' : '-') . '1 year', strtotime($_ym['y'] . '-' . $_ym['m'] . '-01')));
    }

}
