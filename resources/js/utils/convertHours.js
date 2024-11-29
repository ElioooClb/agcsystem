export const  floatToTime = function floatToTime(number) {
    var hours = Math.floor(number);
    var minutes = Math.floor((number - hours) * 60);
    return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
}
