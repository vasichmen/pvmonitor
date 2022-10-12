<template>
  <div class="map__container">
    <div
      id="map"
      class="ymap"
    >
      <yandex-map
        ref="map"
        class="ymap__area"
        :coords="mapInitCoordinates"
        :zoom="mapInitZoom"
      >
        <ymap-marker
          :coords="countryPolygonCoordinates"
          :marker-type="'polygon'"
          marker-id="country-polygon"
        />
      </yandex-map>
    </div>
  </div>
</template>

<script>
  import initHeatmap from '@/heatmap.js';
  import { loadYmap, yandexMap, ymapMarker } from 'vue-yandex-maps';


  export default {
    name: 'main-page',
    components: { yandexMap, ymapMarker },
    props: {
      countryPolygonCoordinates: {
        type: Array,
        required: true,
      },
      mapInitCoordinates: {
        type: Array,
        required: true,
      },
      mapInitZoom: {
        type: Number,
        required: true,
      },
    },
    data: () => ({
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
        max: 45,
        min: 0,
        data: [
          dataPoint1, dataPoint2, dataPoint3, dataPoint4,
        ],
      };
      heatmapInstance.setData(data);

    },
  };
</script>

<style scoped lang="scss">
  .map__container {
    position: relative;
  }


  .heatmap-overlay {
    height: 200px;
    width: 200px;
    pointer-events: none;
  }

  .heatmap-canvas {
    pointer-events: none;
  }

  .ymap {
    position: absolute;
    left: 0;
    right: 0;

    &__area {
      height: 500px;
      width: 500px;
    }
  }
</style>