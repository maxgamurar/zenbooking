<?php

class indexModel extends Model {

    public $_date_format = 'Ym';

    protected function init() {
        
    }

    public function read($_ym) {
        $data = array();
        $q    = "";
        if (!empty($_ym)) {

            $_ym_arr = $this->getDateYearMonth($_ym);

            $q     = "WHERE YEAR(`book_date`) = " . $_ym_arr['y'] . " AND MONTH(`book_date`) = " . $_ym_arr['m'];
            $query = "SELECT * FROM `room_bookings` {$q}";

            $result = $this->db->query($query);

            while ($row = $this->db->fetchAssoc($result)) {
                $data[] = $row;
            }

            if (MyHelpers::isAjax()) {
                return json_encode($data);
            } else {
                return $data;
            }
        }
        return $data;
    }

    public function updateRoomBooking($room_id, $_year, $_month, $_day, $qty) {
        $result_ok = 0;

        if ($this->getRooombyID($room_id)) {
            $_date = date('Y-m-d', strtotime($_year . '-' . $_month . '-' . $_day));

            $fields_map = ['room_ID' => $room_id, 'book_date' => $_date, 'qty' => $qty];

            if ($book_id = $this->getRooombyBookingbyDate($room_id, $_date)) {
                $this->db->update($fields_map, 'room_bookings', 'ID = ' . ( (int) $book_id));
            } else {
                $this->db->insert($fields_map, 'room_bookings');
            }

            $result_ok = 1;

            //sync with 3rd party service
            try {

                HTTPClient::connect('https://3rdparty.com', 80)
                        ->silentMode()
                        ->get('/data/update', $fields_map)
                        ->run();
            } catch (Http_Exception $e) {

                // log errors etc.
            }
        }
        return $result_ok;
    }

    public function getRoomswithBookings($_ym) {

        $rooms       = array();
        $rooms_alone = $this->db->select('*', 'rooms');

        foreach ($rooms_alone as $_room) {
            //fetch bookings
            $bookings           = $this->db->select("*", 'room_bookings', "room_ID = '" . $this->db->escapeString($_room['ID']) . "' AND YEAR(book_date) = '" . $this->db->escapeString($_ym['y']) . "' AND  MONTH(book_date) = '" . $this->db->escapeString($_ym['m']) . "'");
            $_room['_bookings'] = $bookings ? $bookings : [];
            $rooms[]            = $_room;
        }

        return $rooms;
    }

    public function getRooombyID($ID) {
        return $this->db->selectOne(['ID'], 'rooms', 'ID = ' . ((int) $ID));
    }

    public function getRooombyBookingbyDate($ID, $date) {
        return $this->db->selectOne(['ID'], 'room_bookings', 'room_ID = ' . ((int) $ID) . ' AND book_date = \'' . $this->db->escapeString($date) . '\'');
    }

    /** extract year month from date string */
    public function getDateYearMonth($_ym) {

        return ['y' => substr($_ym, 0, 4), 'm' => substr($_ym, 4, 2)];
    }

}
