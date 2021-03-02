<template>
    <div id="mapbox-map">
    </div>
</template>

<script>
    import MapboxGeocoder from '@mapbox/mapbox-gl-geocoder';
    import * as turf from '@turf/turf';
    import '@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.css';

    export default {
        props : ['searchKey', 'pickupCoordinates', 'dropoffCoordinates'],
        mounted() {
            this.init();
            this.markCurrentLocation();
        },
        data() {
            return {
                map : null,
                geoCoder : null,
                draw : null,
                currentLocationMarker : null,
                pickupAddressMarker : null,
                dropoffAddressMarker : null,
                lineSource : null,
                layer : null
            };
        },
        watch : {
            searchKey(newValue, oldValue) {
                this.searchForPlaces(newValue);
            },
            pickupCoordinates(newValue, oldValue) {
                if(newValue === null) {
                    this.pickupAddressMarker.remove();
                } else {
                    this.markPickupAddress(newValue);
                }
                this.drawLine();
            },
            dropoffCoordinates(newValue, oldValue) {
                if(newValue === null) {
                    this.dropoffAddressMarker.remove();
                } else {
                    this.markDropoffAddress(newValue);
                }
                this.drawLine();
            }
        },
        methods : {
            init() {
                this.map = new mapboxgl.Map({
                    container: 'mapbox-map',
                    style: 'mapbox://styles/mapbox/streets-v11'
                });

                this.geoCoder = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl
                });
            },
            markCurrentLocation() {
                let root = this;

                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            if(position) {
                                let coordinates = [
                                    position.coords.longitude,
                                    position.coords.latitude
                                ];

                                let popup = new mapboxgl.Popup()
                                    .setText('Current location')
                                    .addTo(root.map);

                                root.currentLocationMarker = new mapboxgl.Marker()
                                    .setLngLat(coordinates)
                                    .addTo(root.map)
                                    .setPopup(popup);

                                root.map.flyTo({
                                    center : coordinates,
                                    zoom : 16
                                });
                            }
                        },
                        function(error) {
                            alert(error.message);
                        }, {
                            enableHighAccuracy : true
                        });
                } else {
                    alert('Current location can not be marked as geolocation is not supported');
                }
            },
            searchForPlaces(searchKey) {
                if(searchKey === null) {
                    return false;
                }

                let root = this;

                axios.get(`https://api.mapbox.com/geocoding/v5/mapbox.places/${searchKey}.json?access_token=${mapboxgl.accessToken}`)
                    .then((response) => {
                        this.$emit('new-places-found', response.data);
                    }, (error) => {
                        this.$emit('new-places-found', null);
                    });
            },
            markPickupAddress(coordinates) {

                if(this.pickupAddressMarker !== null) {
                    this.pickupAddressMarker.remove();
                }

                let popup = new mapboxgl.Popup()
                    .setText('Pickup')
                    .addTo(this.map);

                this.pickupAddressMarker = new mapboxgl.Marker()
                    .setLngLat(coordinates)
                    .addTo(this.map)
                    .setPopup(popup);

                if(this.dropoffCoordinates === null) {
                    this.map.flyTo({
                        center : coordinates,
                        zoom : 16
                    });
                }
            },
            markDropoffAddress(coordinates) {
                if(this.dropoffAddressMarker !== null) {
                    this.dropoffAddressMarker.remove();
                }

                let popup = new mapboxgl.Popup()
                    .setText('Dropoff')
                    .addTo(this.map);

                this.dropoffAddressMarker = new mapboxgl.Marker()
                    .setLngLat(coordinates)
                    .addTo(this.map)
                    .setPopup(popup);

                if(this.pickupCoordinates === null) {
                    this.map.flyTo({
                        center : coordinates,
                        zoom : 16
                    });
                }
            },
            drawLine() {
                let mapLayer = this.map.getLayer('route');

                if(typeof mapLayer !== 'undefined') {
                    this.map.removeLayer('route').removeSource('route');
                }

                if(this.pickupCoordinates === null || this.dropoffCoordinates === null) {
                    return;
                }

                let coordinates = [
                    [parseFloat(this.pickupCoordinates[0]), parseFloat(this.pickupCoordinates[1])],
                    [parseFloat(this.dropoffCoordinates[0]), parseFloat(this.dropoffCoordinates[1])]
                ];

                let features = turf.points(coordinates);

                let center = turf.center(features);

                this.map.flyTo({
                    center : center.geometry.coordinates
                });

                let bounds = coordinates.reduce(function (bounds, coord) {
                    return bounds.extend(coord);
                }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[1]));

                this.map.fitBounds(bounds, {
                    padding: 50
                });

                this.map.addSource('route', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'properties': {},
                        'geometry': {
                            'type': 'LineString',
                            'coordinates': coordinates
                        }
                    }
                });

                this.map.addLayer({
                    'id': 'route',
                    'type': 'line',
                    'source': 'route',
                    'layout': {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    'paint': {
                        'line-color': '#888',
                        'line-width': 8
                    }
                });
            }
        }
    }
</script>

<style scoped>
    #mapbox-map {
        width: 100%;
        height: 100vh;
    }
</style>