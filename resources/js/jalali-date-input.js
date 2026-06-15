import dayjs from 'dayjs/esm';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import calendarSystems from '@calidy/dayjs-calendarsystems';
import PersianCalendarSystem from '@calidy/dayjs-calendarsystems/calendarSystems/PersianCalendarSystem';
import fa from 'dayjs/locale/fa';

dayjs.extend(customParseFormat);
dayjs.extend(calendarSystems);
dayjs.registerCalendarSystem('persian', new PersianCalendarSystem());
dayjs.locale(fa);

function parseGregorian(value) {
    if (! value) {
        return null;
    }

    const parsed = dayjs(value, ['YYYY-MM-DD', 'YYYY-MM-DD HH:mm:ss'], true);

    return parsed.isValid() ? parsed.toCalendarSystem('persian') : null;
}

function formatGregorian(persianDate) {
    return persianDate.toCalendarSystem('gregory').format('YYYY-MM-DD');
}

export function initJalaliDateInputs() {
    document.querySelectorAll('[data-jalali-date-input]').forEach((root) => {
        if (root.dataset.jalaliInitialized === '1') {
            return;
        }

        root.dataset.jalaliInitialized = '1';

        const wireModel = root.dataset.wireModel;
        const livewireComponent = root.closest('[wire\\:id]');

        if (! wireModel || ! livewireComponent || ! window.Livewire) {
            return;
        }

        const componentId = livewireComponent.getAttribute('wire:id');
        const livewire = window.Livewire.find(componentId);

        if (! livewire) {
            return;
        }

        const display = root.querySelector('[data-jalali-display]');
        const panel = root.querySelector('[data-jalali-panel]');
        const monthSelect = root.querySelector('[data-jalali-month]');
        const yearInput = root.querySelector('[data-jalali-year]');
        const daysGrid = root.querySelector('[data-jalali-days]');
        const clearButton = root.querySelector('[data-jalali-clear]');
        const trigger = root.querySelector('[data-jalali-trigger]');

        let focusedDate = dayjs().toCalendarSystem('persian').hour(0).minute(0).second(0);
        let selectedDate = parseGregorian(livewire.get(wireModel));

        if (selectedDate) {
            focusedDate = selectedDate;
        }

        const renderCalendar = () => {
            monthSelect.value = String(focusedDate.month());
            yearInput.value = String(focusedDate.year());

            const emptyDays = focusedDate.startOf('month').day();
            const daysInMonth = focusedDate.daysInMonth();

            daysGrid.innerHTML = '';

            for (let i = 0; i < emptyDays; i += 1) {
                daysGrid.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= daysInMonth; day += 1) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = String(day);
                button.className = 'jalali-day';

                const dayDate = focusedDate.date(day);
                const isToday = dayDate.isSame(dayjs().toCalendarSystem('persian'), 'day');
                const isSelected = selectedDate && dayDate.isSame(selectedDate, 'day');

                if (isToday) {
                    button.classList.add('is-today');
                }

                if (isSelected) {
                    button.classList.add('is-selected');
                }

                button.addEventListener('click', () => {
                    selectedDate = dayDate;
                    livewire.set(wireModel, formatGregorian(selectedDate));
                    display.value = selectedDate.format('YYYY/MM/DD');
                    panel.hidden = true;
                    renderCalendar();
                });

                daysGrid.appendChild(button);
            }
        };

        const syncFromLivewire = () => {
            selectedDate = parseGregorian(livewire.get(wireModel));
            display.value = selectedDate ? selectedDate.format('YYYY/MM/DD') : '';

            if (selectedDate) {
                focusedDate = selectedDate;
            }

            renderCalendar();
        };

        syncFromLivewire();

        trigger?.addEventListener('click', () => {
            panel.hidden = ! panel.hidden;
            renderCalendar();
        });

        monthSelect?.addEventListener('change', () => {
            focusedDate = focusedDate.month(Number(monthSelect.value));
            renderCalendar();
        });

        yearInput?.addEventListener('change', () => {
            const year = Number(yearInput.value);

            if (! Number.isNaN(year) && year >= 1300 && year <= 1500) {
                focusedDate = focusedDate.year(year);
                renderCalendar();
            }
        });

        clearButton?.addEventListener('click', () => {
            selectedDate = null;
            livewire.set(wireModel, null);
            display.value = '';
            panel.hidden = true;
            renderCalendar();
        });

        document.addEventListener('click', (event) => {
            if (! root.contains(event.target)) {
                panel.hidden = true;
            }
        });

        const registerLivewireSync = () => {
            if (root.dataset.jalaliHooked === '1' || ! window.Livewire?.hook) {
                return;
            }

            root.dataset.jalaliHooked = '1';

            window.Livewire.hook('commit', ({ component, succeed }) => {
                if (component.id !== componentId) {
                    return;
                }

                succeed(() => {
                    syncFromLivewire();
                });
            });
        };

        registerLivewireSync();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initJalaliDateInputs());
} else {
    initJalaliDateInputs();
}

document.addEventListener('livewire:init', () => initJalaliDateInputs());
document.addEventListener('livewire:navigated', () => initJalaliDateInputs());
