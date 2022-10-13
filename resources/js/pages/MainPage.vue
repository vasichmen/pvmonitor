<template>
  <div class="main-page">
    <div class="main-page__container">
      <div class="main-page__header">
        Карта ресурсов солнечного излучения Кыргызстана
      </div>
      <div class="main-page__panel">
        <div class="main-page__panel-header">
          Координаты местности
        </div>
        <div class="main-page__panel-inputs">
          <el-input
            v-model="heatmapParams.latitude"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.latitude"
            placeholder="Широта. Например, 41.39956"
          />
          <el-input
            v-model="heatmapParams.longitude"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.longitude"
            placeholder="Долгота. Например, 75.39956"
          />
        </div>
        <div class="main-page__panel-header">
          Данные по солнечной радиации и ее составляющим
        </div>
        <div class="main-page__panel-inputs">
          <el-checkbox
            v-model="heatmapParams.full"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.full"
          >
            Суммарная гориз. СР
          </el-checkbox>
          <el-checkbox
            v-model="heatmapParams.diffuse"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.diffuse"
          >
            Рассеяная гориз. СР
          </el-checkbox>
          <el-checkbox
            v-model="heatmapParams.direct"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.direct"
          >
            Прямая нормальная СР
          </el-checkbox>
        </div>
      </div>
      <div
        class="map__container"
      >
        <div
          id="map"
          class="ymap"
        >
          <yandex-map
            ref="map"
            class="ymap__area"
            :coords="mapInitCoordinates"
            :zoom="mapInitZoom"
            :bounds="mapBounds"
            :controls="['zoomControl','typeSelector']"
            @boundschange="onBoundsChange"
          >
            <ymap-marker
              :coords="countryPolygonCoordinates"
              :marker-type="'polygon'"
              marker-id="country-polygon"
              :marker-fill="{color:'#eab925', opacity:0.5}"
            />
          </yandex-map>
        </div>
      </div>
      <div class="main-page__export-panel">
        <el-button
          type="success"
          @click="exportData"
        >
          Экспортировать данные
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import initHeatmap from '@/heatmap.js';
  import axios from 'axios';
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
      mapBounds: {
        type: Array,
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

      heatmapParams: {

        latitude: '',
        longitude: '',
        pixLatitude: '',
        pixLongitude: '',
        full: false,
        diffuse: false,
        direct: false,
        bounds: {
          topLeft: { lat: 0, lon: 0 },
          bottomRight: { lat: 0, lon: 0 },
        },
      },

      mapDragging:false,
      canInteractive: false,
      heatmap: null,
    }
    ),
    computed: {
      canShowHeatmap() {
        return this.canInteractive && !this.mapDragging
          && (this.heatmapParams.full || this.heatmapParams.direct || this.heatmapParams.diffuse);
      },
    },
    watch: {
      ['heatmapParams.latitude']: {
        handler() {
          this.renderHeatmap();
        },
      },
      ['heatmapParams.longitude']: {
        handler() {
          this.renderHeatmap();
        },
      },
      ['heatmapParams.full']: {
        handler() {
          this.renderHeatmap();
        },
      },
      ['heatmapParams.diffuse']: {
        handler() {
          this.renderHeatmap();
        },
      },
      ['heatmapParams.direct']: {
        handler() {
          this.renderHeatmap();
        },
      },
    },
    async mounted() {
      await loadYmap({ ...this.settings, debug: true });
      this.canInteractive = true;

      this.fillHeatmapParams(this.mapInitCoordinates[0], this.mapInitCoordinates[1], this.$refs.map.bounds);

      let HeatmapFactory = initHeatmap();
      let map = document.getElementById('map');
      this.heatmap = HeatmapFactory.create({
        container: map,
      });
    },
    methods: {
      onBoundsChange(event) {
        const coords = event.get('newCenter');
        const pixCoords = event.get('newGlobalPixelCenter');
        this.fillHeatmapParams(coords[0], coords[1], event.get('newBounds'), pixCoords[0], pixCoords[1]);
      },
      fillHeatmapParams(lat, lon, bounds, pixLat, pixLon) {
        this.heatmapParams.latitude = lat;
        this.heatmapParams.longitude = lon;
        this.heatmapParams.pixLatitude = pixLat;
        this.heatmapParams.pixLongitude = pixLon;
        this.heatmapParams.bounds = {
          topLeft: {
            lat: bounds[0][0],
            lon: bounds[0][1],
          },
          bottomRight: {
            lat: bounds[1][0],
            lon: bounds[1][1],
          },
        };
      },
      exportData() {
        //todo
      },

      /**
       * загружает данные по тепловой карте в заданных границах и обновляет тепловую карту
       * @returns {Promise<void>}
       */
      async renderHeatmap() {
        this.heatmap.setData({ max: 0, min: 0, data: [] });
        if (!this.canShowHeatmap) {
          return;
        }

        let response = await axios.get('/api/v1/solar-insolation/heatmap', {
          params: this.heatmapParams,
        });
        this.heatmap.setData(response.data);
      },
    },
  };
</script>

<style scoped lang="scss">
  .main-page {
    display: flex;
    flex-direction: column;
    align-items: center;

    &__container {
      width: 790px;
      padding: 20px;
    }

    &__header {
      font-size: 30px;
      margin-bottom: 20px;
    }

    &__panel {

      &-header {
        display: flex;
        flex-direction: column;
        align-items: center;

        font-size: 15px;
      }

      &-inputs {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;

        margin-top: 20px;
        margin-bottom: 20px;

        &-item {
          margin-left: 20px;

          &:first-child {
            margin-left: 0;
          }
        }
      }
    }

    &__export-panel {
      display: flex;
      flex-direction: row;
      justify-content: center;

      margin-top: 20px;
    }
  }

  .ymap {

    &__area {
      height: 500px;
      width: 750px;
    }
  }
</style>