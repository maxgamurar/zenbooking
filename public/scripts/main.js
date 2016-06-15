/* - APP - */

var _ZENAPP = function () {};

_ZENAPP.prototype = {
    DOM: {},
    INIT: function () {
        _me = this;
        _me.cacheDOM();
        _zen('.room-book-act').on('click', _me.invokeBookForm);
        _zen('.btn-close').on('click', _me.closeFormAction);
        _zen('.btn-submit').on('click', _me.submitFormAction);

    },
    invokeBookForm: function () {
        _me.DOM.$calendar_day.val(_zen(this).data('day'));
        _me.DOM.$book_room_id.val(_zen(this).data('room-id'));
        _me.DOM.$book_room_qty.val(_zen(this).data('rooms-free'));
        _me.DOM.$book_rooms_free.val(_zen(this).data('rooms-free'));
        _me.DOM.$book_rooms_max.val(_zen(this).data('rooms-max'));
        _me.DOM.$zen_calendar_form.show();
    },
    submitFormAction: function (e) {

        e.preventDefault();

        //validate input
        input_qty = _me.DOM.$book_room_qty.getval();

        if (_me.isInt(input_qty) && input_qty >= 0 && input_qty <= parseInt(_me.DOM.$book_rooms_max.getval())) {

            var available_rooms = parseInt(_me.DOM.$book_rooms_max.getval()) - parseInt(_me.DOM.$book_room_qty.getval());
            _zen('.btn-close').hide();
            _zen('.btn-submit').hide();
            _zen('.progress-spin').show();

            //submit ajax booking
            ajax.get(WEB_ROOT + 'index/dobook', {
                _year: _zen('#calendar-year').getval(),
                _month: _zen('#calendar-month').getval(),
                _day: _me.DOM.$calendar_day.getval(),
                _room_id: _me.DOM.$book_room_id.getval(),
                qty: available_rooms,
            }, function (data) {
                response = JSON.parse(data);

                if (response.success == 1) {
                    _zen('#room-book-cell-' + _me.DOM.$book_room_id.getval() + '-' + _me.DOM.$calendar_day.getval()).html(_me.DOM.$book_room_qty.getval());
                    alert('Room Availability Updated!');
                } else {
                    alert('System Error! Try again later.');

                }
                _zen('.btn-close').show();
                _zen('.btn-submit').show();
                _zen('.progress-spin').hide();
                _me.DOM.$zen_calendar_form.hide();
            });

        } else {
            alert('Input correct rooms number!');
        }

        return false;
    },
    closeFormAction: function (e) {
        _me.DOM.$zen_calendar_form.hide();
        _zen('#calendar-day').val(0);
        _zen('#book-room-id').val(0);
        _zen('#book-room-qty').val('');
        _zen('#book-rooms-free').val(0);
        e.preventDefault();
        return false;
    },
    cacheDOM: function () {
        //cache DOM els

        _me.DOM.$zen_calendar_form = _zen('#zen-calendar-form');
        _me.DOM.$calendar_day = _zen('#calendar-day');
        _me.DOM.$book_room_id = _zen('#book-room-id');
        _me.DOM.$book_room_qty = _zen('#book-room-qty');
        _me.DOM.$book_rooms_free = _zen('#book-rooms-free');
        _me.DOM.$book_rooms_max = _zen('#book-rooms-max');

    },
    isInt: function (value) {
        return !isNaN(value) && parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
    }

};

_zen(document).ready(function () {
    var zenAPP = new _ZENAPP();
    zenAPP.INIT();
});
