import { HeatmapConfig } from '@/components/vue-heatmap/config';
import { Heatmap } from '@/components/vue-heatmap/constructor';

export default function () {

// core
    return {
        create: function (config) {
            return new Heatmap(config);
        },
        register: function (pluginKey, plugin) {
            HeatmapConfig.plugins[pluginKey] = plugin;
        },
    };


}
