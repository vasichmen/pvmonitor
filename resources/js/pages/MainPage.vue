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
        <div class="main-page__panel-inputs main-page__panel-inputs--margined">
          <el-input
            v-model="latitude"
            class="main-page__panel-inputs-item main-page__panel-inputs-item--margined"
            :value="latitude"
            placeholder="Широта. Например, 41.57"
          />
          <el-input
            v-model="longitude"
            class="main-page__panel-inputs-item main-page__panel-inputs-item--margined"
            :value="longitude"
            placeholder="Долгота. Например, 75.39"
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
            v-model="heatmapParams.full_optimal"
            class="main-page__panel-inputs-item"
            :value="heatmapParams.full_optimal"
          >
            Суммарная СР для оптимального угла наклона
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
      <vue-heatmap
        v-loading="heatmapLoading"
        :is-visible="canShowHeatmap"
        :point-data="heatmapData"
        :point-radius="heatmapPointRadius"
      >
        <template #default>
          <div
            class="ymap"
          >
            <yandex-map
              ref="map"
              class="ymap__area"
              :coords="mapCoords"
              :zoom.sync="zoom"
              :bounds="mapBoundsParam"
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
                :icon="cursorMarkerIcon"
                :coords="cursorMarkerCoords"
                @click="onCursorMarkerClick"
              />
            </yandex-map>
          </div>
        </template>
      </vue-heatmap>
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
  import { CURSOR_MARKER_ICON_TEMPLATE } from '@/const';
  import renderYmapPointer from '@/utils/render-ymap-pointer';
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
        full_optimal: false,
        diffuse: false,
        direct: false,
        bounds: {
          min: { lat: 0, lon: 0 },
          max: { lat: 0, lon: 0 },
        },
      },
      heatmapData: {
        data: [],
      },

      canInteractive: false,
      heatmapFactory: null,
      heatmapTarget: null,
      heatmap: null,
      rendering: false,
      zoom: 6,
      cursorMarkerCoords: null,
      cursorMarkerData: null,
      exportDataLoading: false,
      heatmapLoading: false,
      mapCoords: [41, 75],
      mapBoundsParam: [[], []],
    }
    ),
    computed: {
      /**
       * скрытие тепловой карты до готовности контейнера, отсутствия данных и на некоторых масштабах
       * @returns {boolean}
       */
      canShowHeatmap() {
        return this.canInteractive
          && (this.heatmapParams.full || this.heatmapParams.direct || this.heatmapParams.diffuse || this.heatmapParams.full_optimal
          )
          && (this.zoom > 5 && this.zoom < 14
          );
      },
      heatmapPointsLimit() {
        return 5000;
      },
      exportDataButtonDisabled() {
        return !this.latitude || !this.longitude;
      },
      exportDataButtonTitle() {
        return this.exportDataButtonDisabled ? 'Для экспорта данных выберите точку на карте' : 'Экспорт данных по солнечной радиации в выбранной точке';
      },
      /**
       * коэффициенты подобраны вручную для лучшей визуализации
       * @returns {number}
       */
      heatmapPointRadius() {
        switch (this.zoom) {
          case 6:
            return 4;
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
            return 0;
        }
      },
      cursorMarkerIcon() {
        let content;
        if (!this.cursorMarkerData) {
          content = 'Данные загружаются...';
        }
        else {
          content = renderYmapPointer(this.cursorMarkerData);
        }
        return {
          content,
          layout: 'default#imageWithContent',
          imageSize: [0, 0],
          contentOffset: [-15, 10],
          contentLayout: CURSOR_MARKER_ICON_TEMPLATE,
        };
      },
    },
    watch: {
      ['heatmapParams.full']: {
        handler() {
          this.renderHeatmap();
        },
      },
      ['heatmapParams.full_optimal']: {
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

      //устанавливать границы надо после загрузки карты, чтоб правильно пересчитались значения
      this.mapBoundsParam = this.mapBounds;
      this.fillHeatmapParams(this.$refs.map.bounds);
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
        this.latitude = coords[0].toFixed(2);
        this.longitude = coords[1].toFixed(2);
        this.loadElevation();
      },
      onCursorMarkerClick() {
        this.cursorMarkerCoords = null;
        this.latitude = '';
        this.longitude = '';
      },
      async loadElevation() {
        this.cursorMarkerData = null;
        const response = await axios.get('/api/v1/map-data/point-data', {
          params: {
            lat: this.latitude,
            lon: this.longitude,
          },
        });
        this.cursorMarkerData = response?.data?.point_data || null;
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
        this.heatmapData.data = [];

        if (!this.canShowHeatmap) {
          return;
        }

        //прерываем предыдущий запрос
        if (this.heatmapLoading) {
          this.abortHeatmapRequestController.abort();
        }

        try {
          this.heatmapLoading = true;
          this.abortHeatmapRequestController = new AbortController();

          let response = await axios.get('/api/v1/map-data/heatmap', {
            params: { ...this.heatmapParams, limit: this.heatmapPointsLimit },
            signal: this.abortHeatmapRequestController.signal,
          });
          this.heatmapData = response.data;
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

<style lang="scss">
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
            flex-wrap: wrap;

            margin-top: 20px;
            margin-bottom: 20px;

            &-item {
                margin-right: 0;
                width: 50%;

                &--margined {
                    margin-left: 20px;

                    &:first-child {
                        margin-left: 0;
                    }
                }
            }

            &--margined {
                flex-wrap: nowrap;
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

    &__cursor-pointer {
        $background-color: #606266;

        background-color: $background-color;
        color: white;
        width: max-content;
        padding: 4px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        position: relative;

        border-radius: 0 2px 2px 2px;


        &:before {
            $height: 10px;

            content: " ";

            position: absolute;
            overflow: hidden;
            width: 30px;
            height: $height;
            line-height: 30px;

            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-bottom: $height solid $background-color;

            top: -$height;
            left: 0;
        }
    }
}
</style>
