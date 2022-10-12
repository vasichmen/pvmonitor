<template>
  <div>
    <yandex-map
      id="map"
      ref="map"
      style="height: 600px; width: 600px"
      :coords="coords"
      :zoom="10"
    >
      <ymap-marker
        :coords="markerCoords"
        :marker-type="'polygon'"
        marker-id="123"
        hint-content="some hint"
      />
    </yandex-map>
  </div>
</template>

<script>
  import initHeatmap from '@/heatmap.js';
  import { loadYmap, yandexMap, ymapMarker } from 'vue-yandex-maps';


  export default {
    name: 'main-page',
    components: { yandexMap, ymapMarker },
    data: () => ({
      coords: [
        37.59507, 55.576559,
      ],
      markerCoords:
        [
          [
            [
              37.59507, 55.576559,
            ],
            [
              37.493452, 55.613885,
            ],

          ],
          [
            [
              37.369853, 55.767479,
            ], [
              37.405561, 55.871082,
            ],
          ],
        ],
      settings: {
        apiKey: '',
        lang: 'ru_RU',
        coordorder: 'latlong',
        enterprise: false,
        version: '2.1',
      },
    }
    ),
    async mounted() {
      await loadYmap({ ...this.settings, debug: true });
      let HeatmapFactory = initHeatmap();
      let map = document.getElementById('map');
      var heatmapInstance = HeatmapFactory.create({
        container: map,
      });

      var dataPoint1 = {
        x: 100, // x coordinate of the datapoint, a number
        y: 100, // y coordinate of the datapoint, a number
        value: 10, // the value at datapoint(x, y)
      };
      var dataPoint2 = {
        x: 200, // x coordinate of the datapoint, a number
        y: 200, // y coordinate of the datapoint, a number
        value: 20, // the value at datapoint(x, y)
      };
      var dataPoint3 = {
        x: 300, // x coordinate of the datapoint, a number
        y: 200, // y coordinate of the datapoint, a number
        value: 30, // the value at datapoint(x, y)
      };
      var dataPoint4 = {
        x: 200, // x coordinate of the datapoint, a number
        y: 300, // y coordinate of the datapoint, a number
        value: 40, // the value at datapoint(x, y)
      };
      var data = {
        max: 50,
        min: 0,
        data: [
          dataPoint1, dataPoint2, dataPoint3, dataPoint4,
        ],
      };
      heatmapInstance.setData(data);


      // var style = new ymaps.Style('default#greenPoint');
      // style.polygonStyle = new ymaps.PolygonStyle();
      // style.polygonStyle.fill = 1;
      // style.polygonStyle.outline = 1;
      // style.polygonStyle.strokeWidth = 10;
      // style.polygonStyle.strokeColor = 'ffff0088';
      // style.polygonStyle.fillColor = 'ff000055';
      // ymaps.Styles.add('polygon#Example', style);
      //
      // var polygon = new ymaps.Polygon([
      //   new ymaps.GeoPoint(37.59507,55.576559),
      //   new ymaps.GeoPoint(37.493452,55.613885),
      //   new ymaps.GeoPoint(37.369853,55.767479),
      //   new ymaps.GeoPoint(37.405561,55.871082),
      //   new ymaps.GeoPoint(37.553873,55.911209),
      //   new ymaps.GeoPoint(37.707682,55.898866),
      //   new ymaps.GeoPoint(37.83677,55.821634),
      //   new ymaps.GeoPoint(37.834021,55.689979),
      //   new ymaps.GeoPoint(37.83677,55.646518),
      //   new ymaps.GeoPoint(37.669234,55.576559),
      // ], {
      //   style: 'polygon#Example',
      //   hasHint: 1,
      //   hasBalloon: 1,
      // });
      //
      // polygon.name = 'Москва';
      // polygon.description = 'Столица России';
      //
      // this.$refs.map.addOverlay(polygon);
    },
    methods: {},
  };
</script>

<style scoped>

</style>