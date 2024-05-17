

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.2/leaflet.js" integrity="sha512-KMraOVM0qMVE0U1OULTpYO4gg5MZgazwPAPyMQWfOkEshpwlLQFCHZ/0lBXyviDNVL+pBGwmeXQnuvGK8Fscvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.2/leaflet.css" integrity="sha512-UkezATkM8unVC0R/Z9Kmq4gorjNoFwLMAWR/1yZpINW08I79jEKx/c8NlLSvvimcu7SL8pgeOnynxfRpe+5QpA==" crossorigin="anonymous" referrerpolicy="no-referrer" />




<script type="text/javascript">
    var map = L.map('map', {scrollWheelZoom: false, dragging: false, tap: false}).setView([<?=$this->lat?>, <?=$this->lng?>], 6);
    L.tileLayer('//{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    var marker = L.marker([<?=$this->lat?>, <?=$this->lng?>], {
        draggable: false
    }).addTo(map);
</script>
