<template>
  <div
    :id="containerId"
    class="vue-heatmap__container"
  >
    <slot name="default"></slot>
  </div>
</template>

<script>
  import { CANVAS_ID, CONTAINER_ID } from '@/components/vue-heatmap/const';
  import initHeatmap from '@/components/vue-heatmap/heatmap.js';


  export default {
    name: 'vue-heatmap',

    props: {
      pointData: {
        type: Object,
        required: true,
      },
      pointRadius: {
        type: Number,
        default: 9,
      },
      maxOpacity: {
        type: Number,
        default: .2,
      },
      minOpacity: {
        type: Number,
        default: 0,
      },
      blur: {
        type: Number,
        default: 0.9,
      },
      gradient: {
        type: Object,
        default: () => ({
          0.25: 'rgb(0,0,255)',
          0.55: 'rgb(0,255,0)',
          0.85: 'yellow',
          1.0: 'rgb(255,0,0)',
        }
        ),
      },
      xField: {
        type: String,
        default: 'x',
      },
      yField: {
        type: String,
        default: 'y',
      },
      valueField: {
        type: String,
        default: 'value',
      },
      isVisible: {
        type: Boolean,
        default: true,
      },
    },
    data: () => {
      return {
        heatmapTarget: null,
        heatmapFactory: null,
        heatmap: null,
      };
    },
    computed: {
      containerId() {
        return CONTAINER_ID;
      },
    },
    watch: {
      'pointData.data': {
        handler(data) {
          this.renderHeatmap(data);
        },
        deep: true,
      },
      'pointData.max': {
        handler(max) {
          if (this.heatmap) {
            this.heatmap.setDataMax(max);
          }
        },
      },
      'pointData.min': {
        handler(min) {
          if (this.heatmap) {
            this.heatmap.setDataMin(min);
          }
        },
      },
    },
    mounted() {
      this.heatmapTarget = document.getElementById(CONTAINER_ID);
      this.heatmapFactory = initHeatmap();
    },
    methods: {
      renderHeatmap(data) {
        //если уже была создана тепловая карта, то удаляем
        if (this.heatmap) {
          const tag = document.getElementById(CANVAS_ID);
          if (tag) {
            tag.remove();
          }
        }

        if (!data.length || !this.isVisible) {
          return;
        }

        this.heatmap = this.heatmapFactory.create({
          container: this.heatmapTarget,
          radius: this.pointRadius,
          maxOpacity: this.maxOpacity,
          minOpacity: this.minOpacity,
          blur: this.blur,
          gradient: this.gradient,
          xField: this.xField,
          yField: this.yField,
          valueField: this.valueField,
        });

        this.heatmap.setData({ ...this.pointData, data });
      },
    },
  };
</script>

<style lang="scss">
.vue-heatmap {
    &__container {
        position: relative;
    }

    &__canvas {
        position: absolute;
        left: 0;
        top: 0;
        pointer-events: none;
    }
}
</style>
