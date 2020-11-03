function addToDatalist(item) {
    let list = document.getElementById('desk-counties');
    let itemNode = document.createElement('option');
    itemNode.value = item;
    list.appendChild(itemNode);
}

var touchDevice = ('ontouchstart' in document.documentElement);
if (touchDevice) {
    document.getElementById('mob-view').style.display = 'block';
}
else {
    document.getElementById('desk-view').style.display = 'block';
}

$.getJSON('https://covid19clock.herokuapp.com/v1/counties', function (data) {
    let counties = data.counties;
    counties.sort();
    counties.forEach(addToDatalist);
});