<?php
$this->layout = '~/views/shared/_layout.php';
?>
<div class="block bgWhite">
    <div class="content page-calendar">
        <h1>AVAILABILITY CALENDAR</h1>
        <?php
        //lets build calendar view
        ?>
        <div class="table-calendar-wrap">
            <form action="<?= WEB_ROOT ?>index/book" id="zen-calendar-form">
                <input type="hidden" name="_year" id="calendar-year" value="<?= $date_year ?>" />
                <input type="hidden" name="_month" id="calendar-month" value="<?= $date_month ?>" />
                <input type="hidden" name="_day" id="calendar-day" value="0" />
                <input type="hidden" name="_room-id" id="book-room-id" value="0" />
                <input type="hidden" name="_rooms-free" id="book-rooms-free" value="0" />
                <input type="hidden" name="_rooms-free" id="book-rooms-max" value="0" />
                <div>
                    <input type="number" min="0" step="1" name="_room-qty" id="book-room-qty" value="" />
                    <button class="form-btn btn-submit">&check;</button>  <button class="form-btn btn-close">X</button>
                    <span class="progress-spin">Loading...</span>
                </div>
            </form>
            <table width="100%" border="0" cellspacing="0" cellpadding="4" class="zen-calendar-table">
                <tr class="calendar-nav-row">
                    <td colspan="<?= $days_in_month + 1; ?>" class="calendar-nav"><a href="<?= WEB_ROOT ?>index/<?= $nav_month_prev ?>">&larr;</a> <?= DateTime::createFromFormat('!m', $date_month)->format('F'); ?>,  <a href="<?= WEB_ROOT ?>index/<?= $nav_year_prev ?>">&dArr;</a> <?= $date_year ?> <a href="<?= WEB_ROOT ?>index/<?= $nav_year_next ?>">&uArr;</a> <a href="<?= WEB_ROOT ?>index/<?= $nav_month_next ?>">&rarr;</a></td>
                </tr>
                <tr class="day-names">
                    <td class="first-col  top-left">Price and Availability</td>
                    <?php for ($_daynum = 1; $_daynum <= $days_in_month; $_daynum++): ?>
                        <td class="day-num-<?= date('w', strtotime($date_year . '-' . $date_month . '-' . $_daynum)) ?>"><?= date('D', strtotime($date_year . '-' . $date_month . '-' . $_daynum)); ?></td>
                    <?php endfor; ?>
                </tr>
                <tr class="day-numbers"> 
                    <td class="first-col">&nbsp;</td>
                    <?php for ($_daynum = 1; $_daynum <= $days_in_month; $_daynum++): ?>
                        <td><?= $_daynum; ?></td>
                    <?php endfor; ?>
                </tr>
                <?php
                //loop thru all rooms and fetch availability
                foreach ($rooms as $_room):
                    ?>
                    <tr>
                        <td class="room-name-row first-col"><?= $_room['name'] ?></td>
                        <td colspan="<?= $days_in_month; ?>"  class="room-name-row">&nbsp;</td>
                    </tr>
                    <tr>
                        <td  class="first-col">Rooms Available</td>
                        <?php
                        for ($_daynum = 1; $_daynum <= $days_in_month; $_daynum++):

                            $_book_date = date('Y-m-d', strtotime($date_year . '-' . $date_month . '-' . $_daynum));

                            // get booked qty of the room for given day
                            $_book_day_idx = array_search($_book_date, array_column($_room['_bookings'], 'book_date'));
                            $booked_qty    = 0;

                            if ($_book_day_idx !== false) {
                                $booked_qty = $_room['_bookings'][$_book_day_idx]['qty'];
                            }
                            $available_rooms = $_room['qty'] - $booked_qty;
                            ?>
                            <td><span class="room-book-act" id="room-book-cell-<?= $_room['ID'] ?>-<?= $_daynum ?>" data-room-id="<?= $_room['ID'] ?>" data-day="<?= $_daynum ?>" data-rooms-max="<?= $_room['qty'] ?>" data-rooms-free="<?= $available_rooms ?>"><?= $available_rooms ?></span></td>
                            <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>