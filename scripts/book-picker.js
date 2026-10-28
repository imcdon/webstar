(function () {
    'use strict';

    var root = document.querySelector('[data-book-picker]');
    if (!root) {
        return;
    }

    var dataEl = document.getElementById('book-slots-data');
    var fallback = root.querySelector('[data-book-fallback]');
    var ui = root.querySelector('[data-book-ui]');
    var select = root.querySelector('#book-slot-select');
    var grid = root.querySelector('[data-book-grid]');
    var monthLabel = root.querySelector('[data-book-month]');
    var dayLabel = root.querySelector('[data-book-day-label]');
    var timesEl = root.querySelector('[data-book-times]');
    var emptyEl = root.querySelector('[data-book-empty]');
    var prevBtn = root.querySelector('[data-book-prev]');
    var nextBtn = root.querySelector('[data-book-next]');

    if (!dataEl || !fallback || !ui || !select || !grid || !monthLabel || !dayLabel || !timesEl || !emptyEl || !prevBtn || !nextBtn) {
        return;
    }

    var payload;
    try {
        payload = JSON.parse(dataEl.textContent || '{}');
    } catch (err) {
        return;
    }

    var days = payload.days || {};
    var available = Object.keys(days).sort();
    if (available.length === 0) {
        return;
    }

    var selectedDate = payload.firstDate && days[payload.firstDate] ? payload.firstDate : available[0];
    var view = parseYmd(selectedDate);
    view.setDate(1);

    monthLabel.id = 'book-cal-month-label';

    fallback.hidden = true;
    select.disabled = true;
    select.removeAttribute('name');
    select.removeAttribute('required');
    ui.hidden = false;

    function parseYmd(ymd) {
        var parts = String(ymd).split('-');
        return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    }

    function ymd(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function monthKey(date) {
        return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
    }

    var firstMonth = parseYmd(available[0]);
    firstMonth.setDate(1);
    var lastMonth = parseYmd(available[available.length - 1]);
    lastMonth.setDate(1);

    function renderTimes(dateStr) {
        var day = days[dateStr];
        timesEl.innerHTML = '';
        emptyEl.hidden = true;

        if (!day || !day.slots || day.slots.length === 0) {
            dayLabel.textContent = 'Choose a day';
            emptyEl.hidden = false;
            return;
        }

        dayLabel.textContent = day.label;
        day.slots.forEach(function (slot, index) {
            var id = 'book-slot-' + dateStr + '-' + index;
            var label = document.createElement('label');
            label.className = 'book-slot';
            label.setAttribute('for', id);

            var input = document.createElement('input');
            input.type = 'radio';
            input.name = 'slot';
            input.id = id;
            input.value = slot.start;
            input.required = index === 0;
            input.setAttribute('aria-label', day.label + ', ' + slot.label);

            var span = document.createElement('span');
            span.textContent = slot.label;

            label.appendChild(input);
            label.appendChild(span);
            timesEl.appendChild(label);
        });
    }

    function renderCalendar() {
        var year = view.getFullYear();
        var month = view.getMonth();
        monthLabel.textContent = view.toLocaleString('en-US', { month: 'long', year: 'numeric' });

        prevBtn.disabled = monthKey(view) <= monthKey(firstMonth);
        nextBtn.disabled = monthKey(view) >= monthKey(lastMonth);

        grid.innerHTML = '';
        var firstDow = new Date(year, month, 1).getDay();
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var i;

        for (i = 0; i < firstDow; i += 1) {
            var pad = document.createElement('span');
            pad.className = 'book-cal__pad';
            pad.setAttribute('aria-hidden', 'true');
            grid.appendChild(pad);
        }

        for (i = 1; i <= daysInMonth; i += 1) {
            var date = new Date(year, month, i);
            var key = ymd(date);
            var hasOpen = Object.prototype.hasOwnProperty.call(days, key);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'book-cal__day';
            btn.textContent = String(i);
            btn.dataset.date = key;
            btn.setAttribute('aria-label', date.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }));

            if (!hasOpen) {
                btn.disabled = true;
                btn.classList.add('book-cal__day--muted');
            } else {
                btn.setAttribute('aria-pressed', key === selectedDate ? 'true' : 'false');
                if (key === selectedDate) {
                    btn.classList.add('book-cal__day--selected');
                }
                btn.addEventListener('click', onDayClick);
            }

            grid.appendChild(btn);
        }
    }

    function onDayClick(event) {
        var btn = event.currentTarget;
        var key = btn.dataset.date;
        if (!key || !days[key]) {
            return;
        }
        selectedDate = key;
        renderCalendar();
        renderTimes(selectedDate);
    }

    prevBtn.addEventListener('click', function () {
        if (prevBtn.disabled) {
            return;
        }
        view.setMonth(view.getMonth() - 1);
        renderCalendar();
    });

    nextBtn.addEventListener('click', function () {
        if (nextBtn.disabled) {
            return;
        }
        view.setMonth(view.getMonth() + 1);
        renderCalendar();
    });

    renderCalendar();
    renderTimes(selectedDate);
})();
