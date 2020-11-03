var touchDevice = ('ontouchstart' in document.documentElement);
if (touchDevice) {
    console.log('mobile');
}
else {
    console.log('desktop');
    document.getElementById('desc-view').style.display = 'block';
}