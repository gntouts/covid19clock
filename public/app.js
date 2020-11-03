var options = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0
};

function success() {
    console.log('a')
}
function error() {
    console.log('b')
}

navigator.geolocation.getCurrentPosition(success, error, options);

