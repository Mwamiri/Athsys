/*
Template Name: Menilo - Admin & Dashboard Template
Author: Pixeleyez
Website: https://pixeleyez.com/
File: prodcast management init js
*/

var swiper = new Swiper(".music-list", {
  loop: true,
  slidesPerView: 1,
  spaceBetween: 25,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  autoplay: {
    delay: 1500,
    disableOnInteraction: false,
  },
  breakpoints: {
    768: {
      slidesPerView: 4,
    },
    1025: {
      slidesPerView: 5,
    },
    1441: {
      slidesPerView: 7,
    },
  },
});

document.addEventListener('DOMContentLoaded', function () {
  let statusSelectChoice = document.getElementById('statusSelect');
  if (statusSelectChoice) {
    const choices = new Choices('#statusSelect', {
      placeholderValue: 'Select Style',
      searchPlaceholderValue: 'Search...',
      removeItemButton: true,
      itemSelectText: 'Press to select',
    });
  }
});

const localeEn = {
  days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
  daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
  daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
  months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
  monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  today: 'Today',
  clear: 'Clear',
  dateFormat: 'mm/dd/yyyy',
  timeFormat: 'hh:ii aa',
  firstDay: 0
}
new AirDatepicker('#releaseDate', {
  autoClose: false,
  dateFormat: 'dd/MM/yyyy',
  locale: localeEn,
});
