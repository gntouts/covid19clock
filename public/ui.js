function addToDatalist(item) {
    let deskList = document.getElementById('desk-counties');
    let mobList = document.getElementById('mob-counties');
    let itemNode = document.createElement('option');
    itemNode.value = item;
    deskList.appendChild(itemNode);
    mobList.appendChild(itemNode);
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