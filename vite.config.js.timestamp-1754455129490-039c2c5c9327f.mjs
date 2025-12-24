// vite.config.js
import laravel from "file:///C:/laragon/www/alba/node_modules/laravel-vite-plugin/dist/index.js";
import { fileURLToPath } from "node:url";
import vue from "file:///C:/laragon/www/alba/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///C:/laragon/www/alba/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import AutoImport from "file:///C:/laragon/www/alba/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///C:/laragon/www/alba/node_modules/unplugin-vue-components/dist/vite.js";
import { VueRouterAutoImports, getPascalCaseRouteName } from "file:///C:/laragon/www/alba/node_modules/unplugin-vue-router/dist/index.mjs";
import VueRouter from "file:///C:/laragon/www/alba/node_modules/unplugin-vue-router/dist/vite.mjs";
import { defineConfig } from "file:///C:/laragon/www/alba/node_modules/vite/dist/node/index.js";
import Layouts from "file:///C:/laragon/www/alba/node_modules/vite-plugin-vue-layouts/dist/index.mjs";
import vuetify from "file:///C:/laragon/www/alba/node_modules/vite-plugin-vuetify/dist/index.mjs";
import svgLoader from "file:///C:/laragon/www/alba/node_modules/vite-svg-loader/index.js";
var __vite_injected_original_import_meta_url = "file:///C:/laragon/www/alba/vite.config.js";
var vite_config_default = defineConfig({
  base: "/",
  plugins: [
    // Docs: https://github.com/posva/unplugin-vue-router
    // ℹ️ This plugin should be placed before vue plugin
    VueRouter({
      getRouteName: (routeNode) => {
        return getPascalCaseRouteName(routeNode).replace(/([a-z\d])([A-Z])/g, "$1-$2").toLowerCase();
      },
      routesFolder: "resources/js/pages"
    }),
    vue({
      template: {
        compilerOptions: {
          isCustomElement: (tag) => tag === "swiper-container" || tag === "swiper-slide"
        },
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    laravel({
      input: ["resources/js/app.js"],
      refresh: true
    }),
    vueJsx(),
    // Docs: https://github.com/vuetifyjs/vuetify-loader/tree/master/packages/vite-plugin
    vuetify({
      styles: {
        configFile: "resources/styles/variables/_vuetify.scss"
      }
    }),
    // Docs: https://github.com/johncampionjr/vite-plugin-vue-layouts#vite-plugin-vue-layouts
    Layouts({
      layoutsDirs: "./resources/js/layouts/"
    }),
    // Docs: https://github.com/antfu/unplugin-vue-components#unplugin-vue-components
    Components({
      dirs: ["resources/js/@core/components", "resources/js/views/demos", "resources/js/components"],
      dts: true,
      resolvers: [
        (componentName) => {
          if (componentName === "VueApexCharts") {
            return { name: "default", from: "vue3-apexcharts", as: "VueApexCharts" };
          }
        }
      ]
    }),
    // Docs: https://github.com/antfu/unplugin-auto-import#unplugin-auto-import
    AutoImport({
      imports: ["vue", VueRouterAutoImports, "@vueuse/core", "@vueuse/math", "vue-i18n", "pinia"],
      dirs: [
        "./resources/js/@core/utils",
        "./resources/js/@core/composable/",
        "./resources/js/composables/",
        "./resources/js/utils/",
        "./resources/js/plugins/*/composables/*"
      ],
      vueTemplate: true,
      // ℹ️ Disabled to avoid confusion & accidental usage
      ignore: ["useCookies", "useStorage"],
      eslintrc: {
        enabled: true,
        filepath: "./.eslintrc-auto-import.json"
      }
    }),
    svgLoader()
  ],
  define: { "process.env": {} },
  resolve: {
    alias: {
      "@core-scss": fileURLToPath(new URL("./resources/styles/@core", __vite_injected_original_import_meta_url)),
      "@": fileURLToPath(new URL("./resources/js", __vite_injected_original_import_meta_url)),
      "@themeConfig": fileURLToPath(new URL("./themeConfig.js", __vite_injected_original_import_meta_url)),
      "@core": fileURLToPath(new URL("./resources/js/@core", __vite_injected_original_import_meta_url)),
      "@layouts": fileURLToPath(new URL("./resources/js/@layouts", __vite_injected_original_import_meta_url)),
      "@images": fileURLToPath(new URL("./resources/images/", __vite_injected_original_import_meta_url)),
      "@styles": fileURLToPath(new URL("./resources/styles/", __vite_injected_original_import_meta_url)),
      "@configured-variables": fileURLToPath(new URL("./resources/styles/variables/_template.scss", __vite_injected_original_import_meta_url)),
      "@db": fileURLToPath(new URL("./resources/js/plugins/fake-api/handlers/", __vite_injected_original_import_meta_url)),
      "@api-utils": fileURLToPath(new URL("./resources/js/plugins/fake-api/utils/", __vite_injected_original_import_meta_url))
    }
  },
  build: {
    chunkSizeWarningLimit: 5e3
  },
  optimizeDeps: {
    exclude: ["vuetify"],
    entries: [
      "./resources/js/**/*.vue"
    ]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxhbGJhXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxhbGJhXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9sYXJhZ29uL3d3dy9hbGJhL3ZpdGUuY29uZmlnLmpzXCI7Ly8gdml0ZS5jb25maWcuanNcclxuaW1wb3J0IGxhcmF2ZWwgZnJvbSAnbGFyYXZlbC12aXRlLXBsdWdpbidcclxuaW1wb3J0IHsgZmlsZVVSTFRvUGF0aCB9IGZyb20gJ25vZGU6dXJsJ1xyXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcclxuaW1wb3J0IHZ1ZUpzeCBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUtanN4J1xyXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJ1xyXG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tICd1bnBsdWdpbi12dWUtY29tcG9uZW50cy92aXRlJ1xyXG5pbXBvcnQgeyBWdWVSb3V0ZXJBdXRvSW1wb3J0cywgZ2V0UGFzY2FsQ2FzZVJvdXRlTmFtZSB9IGZyb20gJ3VucGx1Z2luLXZ1ZS1yb3V0ZXInXHJcbmltcG9ydCBWdWVSb3V0ZXIgZnJvbSAndW5wbHVnaW4tdnVlLXJvdXRlci92aXRlJ1xyXG5pbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJ1xyXG5pbXBvcnQgTGF5b3V0cyBmcm9tICd2aXRlLXBsdWdpbi12dWUtbGF5b3V0cydcclxuaW1wb3J0IHZ1ZXRpZnkgZnJvbSAndml0ZS1wbHVnaW4tdnVldGlmeSdcclxuaW1wb3J0IHN2Z0xvYWRlciBmcm9tICd2aXRlLXN2Zy1sb2FkZXInXHJcblxyXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xyXG4gICAgYmFzZTogXCIvXCIsXHJcbiAgICBwbHVnaW5zOiBbXHJcbiAgICAgICAgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL3Bvc3ZhL3VucGx1Z2luLXZ1ZS1yb3V0ZXJcclxuICAgICAgICAvLyBcdTIxMzlcdUZFMEYgVGhpcyBwbHVnaW4gc2hvdWxkIGJlIHBsYWNlZCBiZWZvcmUgdnVlIHBsdWdpblxyXG4gICAgICAgIFZ1ZVJvdXRlcih7XHJcbiAgICAgICAgICAgIGdldFJvdXRlTmFtZTogcm91dGVOb2RlID0+IHtcclxuICAgICAgICAgICAgICAgIC8vIENvbnZlcnQgcGFzY2FsIGNhc2UgdG8ga2ViYWIgY2FzZVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGdldFBhc2NhbENhc2VSb3V0ZU5hbWUocm91dGVOb2RlKVxyXG4gICAgICAgICAgICAgICAgICAgIC5yZXBsYWNlKC8oW2EtelxcZF0pKFtBLVpdKS9nLCAnJDEtJDInKVxyXG4gICAgICAgICAgICAgICAgICAgIC50b0xvd2VyQ2FzZSgpXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIHJvdXRlc0ZvbGRlcjogJ3Jlc291cmNlcy9qcy9wYWdlcycsXHJcbiAgICAgICAgfSksXHJcblxyXG4gICAgICAgIHZ1ZSh7XHJcbiAgICAgICAgICAgIHRlbXBsYXRlOiB7XHJcbiAgICAgICAgICAgICAgICBjb21waWxlck9wdGlvbnM6IHtcclxuICAgICAgICAgICAgICAgICAgICBpc0N1c3RvbUVsZW1lbnQ6IHRhZyA9PiB0YWcgPT09ICdzd2lwZXItY29udGFpbmVyJyB8fCB0YWcgPT09ICdzd2lwZXItc2xpZGUnLFxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXHJcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSksXHJcblxyXG4gICAgICAgIGxhcmF2ZWwoe1xyXG4gICAgICAgICAgICBpbnB1dDogWydyZXNvdXJjZXMvanMvYXBwLmpzJ10sXHJcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXHJcbiAgICAgICAgfSksXHJcblxyXG4gICAgICAgIHZ1ZUpzeCgpLFxyXG5cclxuICAgICAgICAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vdnVldGlmeWpzL3Z1ZXRpZnktbG9hZGVyL3RyZWUvbWFzdGVyL3BhY2thZ2VzL3ZpdGUtcGx1Z2luXHJcbiAgICAgICAgdnVldGlmeSh7XHJcbiAgICAgICAgICAgIHN0eWxlczoge1xyXG4gICAgICAgICAgICAgICAgY29uZmlnRmlsZTogJ3Jlc291cmNlcy9zdHlsZXMvdmFyaWFibGVzL192dWV0aWZ5LnNjc3MnLFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgIH0pLFxyXG5cclxuICAgICAgICAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vam9obmNhbXBpb25qci92aXRlLXBsdWdpbi12dWUtbGF5b3V0cyN2aXRlLXBsdWdpbi12dWUtbGF5b3V0c1xyXG4gICAgICAgIExheW91dHMoe1xyXG4gICAgICAgICAgICBsYXlvdXRzRGlyczogJy4vcmVzb3VyY2VzL2pzL2xheW91dHMvJyxcclxuICAgICAgICB9KSxcclxuXHJcbiAgICAgICAgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL2FudGZ1L3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzI3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzXHJcbiAgICAgICAgQ29tcG9uZW50cyh7XHJcbiAgICAgICAgICAgIGRpcnM6IFsncmVzb3VyY2VzL2pzL0Bjb3JlL2NvbXBvbmVudHMnLCAncmVzb3VyY2VzL2pzL3ZpZXdzL2RlbW9zJywgJ3Jlc291cmNlcy9qcy9jb21wb25lbnRzJ10sXHJcbiAgICAgICAgICAgIGR0czogdHJ1ZSxcclxuICAgICAgICAgICAgcmVzb2x2ZXJzOiBbXHJcbiAgICAgICAgICAgICAgICBjb21wb25lbnROYW1lID0+IHtcclxuICAgICAgICAgICAgICAgICAgICAvLyBBdXRvIGltcG9ydCBgVnVlQXBleENoYXJ0c2BcclxuICAgICAgICAgICAgICAgICAgICBpZiAoY29tcG9uZW50TmFtZSA9PT0gJ1Z1ZUFwZXhDaGFydHMnKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7IG5hbWU6ICdkZWZhdWx0JywgZnJvbTogJ3Z1ZTMtYXBleGNoYXJ0cycsIGFzOiAnVnVlQXBleENoYXJ0cycgfVxyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgfSksXHJcblxyXG4gICAgICAgIC8vIERvY3M6IGh0dHBzOi8vZ2l0aHViLmNvbS9hbnRmdS91bnBsdWdpbi1hdXRvLWltcG9ydCN1bnBsdWdpbi1hdXRvLWltcG9ydFxyXG4gICAgICAgIEF1dG9JbXBvcnQoe1xyXG4gICAgICAgICAgICBpbXBvcnRzOiBbJ3Z1ZScsIFZ1ZVJvdXRlckF1dG9JbXBvcnRzLCAnQHZ1ZXVzZS9jb3JlJywgJ0B2dWV1c2UvbWF0aCcsICd2dWUtaTE4bicsICdwaW5pYSddLFxyXG4gICAgICAgICAgICBkaXJzOiBbXHJcbiAgICAgICAgICAgICAgICAnLi9yZXNvdXJjZXMvanMvQGNvcmUvdXRpbHMnLFxyXG4gICAgICAgICAgICAgICAgJy4vcmVzb3VyY2VzL2pzL0Bjb3JlL2NvbXBvc2FibGUvJyxcclxuICAgICAgICAgICAgICAgICcuL3Jlc291cmNlcy9qcy9jb21wb3NhYmxlcy8nLFxyXG4gICAgICAgICAgICAgICAgJy4vcmVzb3VyY2VzL2pzL3V0aWxzLycsXHJcbiAgICAgICAgICAgICAgICAnLi9yZXNvdXJjZXMvanMvcGx1Z2lucy8qL2NvbXBvc2FibGVzLyonLFxyXG4gICAgICAgICAgICBdLFxyXG4gICAgICAgICAgICB2dWVUZW1wbGF0ZTogdHJ1ZSxcclxuXHJcbiAgICAgICAgICAgIC8vIFx1MjEzOVx1RkUwRiBEaXNhYmxlZCB0byBhdm9pZCBjb25mdXNpb24gJiBhY2NpZGVudGFsIHVzYWdlXHJcbiAgICAgICAgICAgIGlnbm9yZTogWyd1c2VDb29raWVzJywgJ3VzZVN0b3JhZ2UnXSxcclxuICAgICAgICAgICAgZXNsaW50cmM6IHtcclxuICAgICAgICAgICAgICAgIGVuYWJsZWQ6IHRydWUsXHJcbiAgICAgICAgICAgICAgICBmaWxlcGF0aDogJy4vLmVzbGludHJjLWF1dG8taW1wb3J0Lmpzb24nLFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgIH0pLFxyXG5cclxuICAgICAgICBzdmdMb2FkZXIoKSxcclxuICAgIF0sXHJcblxyXG4gICAgZGVmaW5lOiB7ICdwcm9jZXNzLmVudic6IHt9IH0sXHJcblxyXG4gICAgcmVzb2x2ZToge1xyXG4gICAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgICAgICdAY29yZS1zY3NzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9zdHlsZXMvQGNvcmUnLCBpbXBvcnQubWV0YS51cmwpKSxcclxuICAgICAgICAgICAgJ0AnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgICAgICdAdGhlbWVDb25maWcnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vdGhlbWVDb25maWcuanMnLCBpbXBvcnQubWV0YS51cmwpKSxcclxuICAgICAgICAgICAgJ0Bjb3JlJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9qcy9AY29yZScsIGltcG9ydC5tZXRhLnVybCkpLFxyXG4gICAgICAgICAgICAnQGxheW91dHMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzL0BsYXlvdXRzJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgICAgICdAaW1hZ2VzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9pbWFnZXMvJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgICAgICdAc3R5bGVzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9zdHlsZXMvJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgICAgICdAY29uZmlndXJlZC12YXJpYWJsZXMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL3N0eWxlcy92YXJpYWJsZXMvX3RlbXBsYXRlLnNjc3MnLCBpbXBvcnQubWV0YS51cmwpKSxcclxuICAgICAgICAgICAgJ0BkYic6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvanMvcGx1Z2lucy9mYWtlLWFwaS9oYW5kbGVycy8nLCBpbXBvcnQubWV0YS51cmwpKSxcclxuICAgICAgICAgICAgJ0BhcGktdXRpbHMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzL3BsdWdpbnMvZmFrZS1hcGkvdXRpbHMvJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcblxyXG4gICAgYnVpbGQ6IHtcclxuICAgICAgICBjaHVua1NpemVXYXJuaW5nTGltaXQ6IDUwMDAsXHJcbiAgICB9LFxyXG5cclxuICAgIG9wdGltaXplRGVwczoge1xyXG4gICAgICAgIGV4Y2x1ZGU6IFsndnVldGlmeSddLFxyXG4gICAgICAgIGVudHJpZXM6IFtcclxuICAgICAgICAgICAgJy4vcmVzb3VyY2VzL2pzLyoqLyoudnVlJyxcclxuICAgICAgICBdLFxyXG4gICAgfSxcclxufSlcclxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUNBLE9BQU8sYUFBYTtBQUNwQixTQUFTLHFCQUFxQjtBQUM5QixP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
