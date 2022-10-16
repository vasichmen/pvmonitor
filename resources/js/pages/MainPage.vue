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
            v-model="latitude"
            class="main-page__panel-inputs-item"
            :value="latitude"
            placeholder="Широта. Например, 41.39956"
          />
          <el-input
            v-model="longitude"
            class="main-page__panel-inputs-item"
            :value="longitude"
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
        v-loading="heatmapLoading"
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
            :zoom.sync="zoom"
            :bounds="mapBounds"
            :controls="['zoomControl','typeSelector']"
            @boundschange="onBoundsChange"
          >
            <ymap-marker
              :coords="countryPolygonCoordinates"
              marker-type="polygon"
              marker-id="country-polygon"
              :marker-fill="{color:'#eab925', opacity:0.5}"
              @click="onPolygonClick"
            />
            <ymap-marker
              v-if="cursorMarkerCoords"
              marker-type="placemark"
              marker-id="cursor-marker"
              :coords="cursorMarkerCoords"
            />
          </yandex-map>
        </div>
      </div>
      <div class="main-page__export-panel">
        <el-button
          v-loading="exportDataLoading"
          type="success"
          :disabled="exportDataButtonDisabled"
          :title="exportDataButtonTitle"
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
  import fileDownload from 'js-file-download';
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

      latitude: '',
      longitude: '',
      heatmapParams: {
        height: '',
        width: '',
        full: false,
        diffuse: false,
        direct: false,
        bounds: {
          min: { lat: 0, lon: 0 },
          max: { lat: 0, lon: 0 },
        },
      },

      canInteractive: false,
      heatmapFactory: null,
      heatmapTarget: null,
      heatmap: null,
      rendering: false,
      zoom: 8,
      cursorMarkerCoords: null,
      exportDataLoading: false,
      heatmapLoading: false,
    }
    ),
    computed: {
      /**
       * скрытие тепловой карты до готовности контейнера, отсутствия данных и на некоторых масштабах
       * @returns {boolean}
       */
      canShowHeatmap() {
        return this.canInteractive
          && (this.heatmapParams.full || this.heatmapParams.direct || this.heatmapParams.diffuse
          )
          && (this.zoom > 6 && this.zoom < 14
          );
      },
      heatmapPointsLimit() {
        return 5000;
      },
      exportDataButtonDisabled() {
        return !this.latitude || !this.longitude;
      },
      exportDataButtonTitle() {
        return this.exportDataButtonDisabled ? 'Для экспорта данных выберите точку на карте' : 'Выгрзука данных по солнечной радиации в выбранной точке';
      },
      /**
       * коэффициенты подобраны вручную для лучшей визуализации
       * @returns {number}
       */
      heatmapPointRadius() {
        switch (this.zoom) {
          case 6:
            return 3;
          case 7:
            return 6;
          case 8:
            return 8;
          case 9:
            return 9;
          case 10:
            return 11;
          case 11:
            return 20;
          case 12:
            return 40;
          case 13:
            return 80;
          default:
            throw Error('Этот масшаб не реализован');
        }
      },
    },
    watch: {
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
      latitude(lat) {
        this.cursorMarkerCoords = [lat, this.longitude];
      },
      longitude(lon) {
        this.cursorMarkerCoords = [this.latitude, lon];
      },
    },
    async mounted() {
      await loadYmap({ ...this.settings, debug: true });
      this.canInteractive = true;

      this.fillHeatmapParams(this.$refs.map.bounds);

      this.heatmapTarget = document.getElementById('map');
      this.heatmapFactory = initHeatmap();
    },
    methods: {
      onBoundsChange(event) {
        //костыль, чтоб карта успела изменить масштаб, а потом уже запускать рендер тепловой карты
        setTimeout(() => {
          this.fillHeatmapParams(event.get('newBounds'));
          this.renderHeatmap();
        }, 100);
      },
      /**
       * установка маркеры выбранных координат
       * @param event
       */
      onPolygonClick(event) {
        const coords = event.get('coords');
        this.cursorMarkerCoords = coords;
        this.latitude = coords[0];
        this.longitude = coords[1];
      },

      fillHeatmapParams(bounds) {
        this.heatmapParams.height = this.$refs.map.$el.clientHeight;
        this.heatmapParams.width = this.$refs.map.$el.clientWidth;
        this.heatmapParams.bounds = {
          min: {
            lat: bounds[0][0],
            lon: bounds[0][1],
          },
          max: {
            lat: bounds[1][0],
            lon: bounds[1][1],
          },
        };
      },
      /**
       * экспорт данных из PV Gis и загрузка результирующего файла
       */
      async exportData() {
        const fileName = `pv-output-${this.latitude}-${this.longitude}.csv`;
        this.exportDataLoading = true;
        try {
          const response = await axios.get('/api/v1/data-export/pvgis', {
            params: {
              lat: this.latitude,
              lon: this.longitude,
            },
          });
          fileDownload(response.data, fileName);
        }
        catch (e) {
          this.$message({ type: 'error', message: 'Произошла ошибка при загрузке данных' });
        }
        finally {
          this.exportDataLoading = false;
        }
      },

      /**
       * загружает данные по тепловой карте в заданных границах и обновляет тепловую карту
       * @returns {Promise<void>}
       */
      async renderHeatmap() {
        //если уже была создана тепловая карта, то удаляем
        if (this.heatmap) {
          const tag = document.getElementById('heatmap-canvas');
          if (tag) {
            tag.remove();
          }
        }

        if (!this.canShowHeatmap) {
          return;
        }

        //прерываем предыдкщий запрос
        if (this.heatmapLoading) {
          this.abortHeatmapRequestController.abort();
        }

        try {
          this.heatmapLoading = true;

          this.heatmap = this.heatmapFactory.create({
            container: this.heatmapTarget,
            radius: this.heatmapPointRadius,
            maxOpacity: .2,
            minOpacity: 0,
            blur: 0.9,
            defaultGradient: { 0.25: 'rgb(0,0,255)', 0.55: 'rgb(0,255,0)', 0.85: 'yellow', 1.0: 'rgb(255,0,0)' },
          });

          this.abortHeatmapRequestController = new AbortController();
          let response = await axios.get('/api/v1/solar-insolation/heatmap', {
            params: { ...this.heatmapParams, limit: this.heatmapPointsLimit },
            signal: this.abortHeatmapRequestController.signal,
          });
          this.heatmap.setData(response.data);
          this.heatmapLoading = false;
        }
        catch (e) {
          if (e.code !== 'ERR_CANCELED') {
            this.$message({ type: 'error', message: 'Произошла ошибка при загрузке данных' });
          }
        }
        finally {
          this.heatmapLoading = false;
        }
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