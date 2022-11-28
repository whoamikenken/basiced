$(document).on('blur', "input[name='fromtime'], input[name='totime']", function() {
    var time = $(this).val();
    var parent = $(this).parent().parent().parent();
    setTimeDefault($(this).attr('name'), time, parent);
});

function setTimeDefault(type, value, parent) {
    var hms = value; // your input string
    var a = hms.split(':'); // split it at the colons
    var b = a[1] ? a[1].split(' ') : [0];
    var am = b[1] ? (b[1] == 'AM' ? true : false) : true;
    var pm = b[1] ? (b[1] == 'PM' ? true : false) : false;

    if (pm && a[0] != 12) a[0] = (+a[0]) + 12;
    if (am && a[0] == 12) a[0] = (+a[0]) - 12;

    var seconds;
    var newtime;
    var target;
    var plustardy;
    var plusabsent;
    var minusearlyd;

    if (type == 'fromtime') {
        var dow = parent.attr('dayofweek');

        if (a[0] == '0' && b[0] == '00') {
            plustardy = 0;
            plusabsent = 0;
        } else {
            // if (parent.is("tr[dayofweek=" + dow + "]:first")) plustardy = 16 * 60;
            // else plustardy = 30 * 60;
            plustardy = 320;

            plusabsent = (60 * 60 * 2);
        }


        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 + plustardy;
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='tardy_f']");
        if (newtime.length == 8) target.val(newtime);

        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 + plusabsent;
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='absent_f']");
        if (newtime.length == 8) target.val(newtime);

    } else if (type == 'totime') {
        // console.log(a[0] + ' // ' + b[0]);

        if (a[0] == '0' && b[0] == '00') {
            minusearlyd = 0;
        } else {
            minusearlyd = (60 * 60 * 2);
        }

        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 - minusearlyd;
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='early_d']");
        if (newtime.length == 8) target.val(newtime);

    }

}

function toHHMMSS(seconds) {
    var sec_num = parseInt(seconds, 10); // don't forget the second param
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);
    var ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    return hours + ':' + minutes + ' ' + ampm;
}

///< end of schedule input defaults

if (typeof schedarr === 'undefined') {
    var schedarr = [];
}

function convertTimeToNumber(time_val){
  const [time, modifier] = time_val.split(' ');

  let [hours, minutes] = time_val.split(':');

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
    hours = parseInt(hours, 10) + 12;
  }

  hours = parseInt(hours);
  minutes = parseInt(minutes) / 60;
  return hours + minutes;
}
function convertToDay(index){
  var day = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  return day[index];
}
function convertToDay1(index){
  var day = {SUN:'Sunday',M:'Monday',T:'Tuesday',W:'Wednesday',TH:'Thursday',F:'Friday',S:'Saturday'};
  return day[index];
}
// end new function for validation in time