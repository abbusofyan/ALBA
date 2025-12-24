// vite.config.js
import laravel from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/laravel-vite-plugin/dist/index.js";
import { fileURLToPath } from "node:url";
import vue from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import AutoImport from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/unplugin-vue-components/dist/vite.js";
import { VueRouterAutoImports, getPascalCaseRouteName } from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/unplugin-vue-router/dist/index.mjs";
import VueRouter from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/unplugin-vue-router/dist/vite.mjs";
import { defineConfig } from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/vite/dist/node/index.js";
import Layouts from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/vite-plugin-vue-layouts/dist/index.mjs";
import vuetify from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/vite-plugin-vuetify/dist/index.mjs";
import svgLoader from "file:///www/wwwroot/csl_laravel_starter_kit/node_modules/vite-svg-loader/index.js";
var __vite_injected_original_import_meta_url = "file:///www/wwwroot/csl_laravel_starter_kit/vite.config.js";
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvd3d3L3d3d3Jvb3QvY3NsX2xhcmF2ZWxfc3RhcnRlcl9raXRcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi93d3cvd3d3cm9vdC9jc2xfbGFyYXZlbF9zdGFydGVyX2tpdC92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vd3d3L3d3d3Jvb3QvY3NsX2xhcmF2ZWxfc3RhcnRlcl9raXQvdml0ZS5jb25maWcuanNcIjsvLyB2aXRlLmNvbmZpZy5qc1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSAnbGFyYXZlbC12aXRlLXBsdWdpbidcbmltcG9ydCB7IGZpbGVVUkxUb1BhdGggfSBmcm9tICdub2RlOnVybCdcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJ1xuaW1wb3J0IHZ1ZUpzeCBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUtanN4J1xuaW1wb3J0IEF1dG9JbXBvcnQgZnJvbSAndW5wbHVnaW4tYXV0by1pbXBvcnQvdml0ZSdcbmltcG9ydCBDb21wb25lbnRzIGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGUnXG5pbXBvcnQgeyBWdWVSb3V0ZXJBdXRvSW1wb3J0cywgZ2V0UGFzY2FsQ2FzZVJvdXRlTmFtZSB9IGZyb20gJ3VucGx1Z2luLXZ1ZS1yb3V0ZXInXG5pbXBvcnQgVnVlUm91dGVyIGZyb20gJ3VucGx1Z2luLXZ1ZS1yb3V0ZXIvdml0ZSdcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnXG5pbXBvcnQgTGF5b3V0cyBmcm9tICd2aXRlLXBsdWdpbi12dWUtbGF5b3V0cydcbmltcG9ydCB2dWV0aWZ5IGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZXRpZnknXG5pbXBvcnQgc3ZnTG9hZGVyIGZyb20gJ3ZpdGUtc3ZnLWxvYWRlcidcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBiYXNlOiBcIi9cIixcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIC8vIERvY3M6IGh0dHBzOi8vZ2l0aHViLmNvbS9wb3N2YS91bnBsdWdpbi12dWUtcm91dGVyXG4gICAgICAgIC8vIFx1MjEzOVx1RkUwRiBUaGlzIHBsdWdpbiBzaG91bGQgYmUgcGxhY2VkIGJlZm9yZSB2dWUgcGx1Z2luXG4gICAgICAgIFZ1ZVJvdXRlcih7XG4gICAgICAgICAgICBnZXRSb3V0ZU5hbWU6IHJvdXRlTm9kZSA9PiB7XG4gICAgICAgICAgICAgICAgLy8gQ29udmVydCBwYXNjYWwgY2FzZSB0byBrZWJhYiBjYXNlXG4gICAgICAgICAgICAgICAgcmV0dXJuIGdldFBhc2NhbENhc2VSb3V0ZU5hbWUocm91dGVOb2RlKVxuICAgICAgICAgICAgICAgICAgICAucmVwbGFjZSgvKFthLXpcXGRdKShbQS1aXSkvZywgJyQxLSQyJylcbiAgICAgICAgICAgICAgICAgICAgLnRvTG93ZXJDYXNlKClcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICByb3V0ZXNGb2xkZXI6ICdyZXNvdXJjZXMvanMvcGFnZXMnLFxuICAgICAgICB9KSxcblxuICAgICAgICB2dWUoe1xuICAgICAgICAgICAgdGVtcGxhdGU6IHtcbiAgICAgICAgICAgICAgICBjb21waWxlck9wdGlvbnM6IHtcbiAgICAgICAgICAgICAgICAgICAgaXNDdXN0b21FbGVtZW50OiB0YWcgPT4gdGFnID09PSAnc3dpcGVyLWNvbnRhaW5lcicgfHwgdGFnID09PSAnc3dpcGVyLXNsaWRlJyxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xuICAgICAgICAgICAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcblxuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbJ3Jlc291cmNlcy9qcy9hcHAuanMnXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuXG4gICAgICAgIHZ1ZUpzeCgpLFxuXG4gICAgICAgIC8vIERvY3M6IGh0dHBzOi8vZ2l0aHViLmNvbS92dWV0aWZ5anMvdnVldGlmeS1sb2FkZXIvdHJlZS9tYXN0ZXIvcGFja2FnZXMvdml0ZS1wbHVnaW5cbiAgICAgICAgdnVldGlmeSh7XG4gICAgICAgICAgICBzdHlsZXM6IHtcbiAgICAgICAgICAgICAgICBjb25maWdGaWxlOiAncmVzb3VyY2VzL3N0eWxlcy92YXJpYWJsZXMvX3Z1ZXRpZnkuc2NzcycsXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcblxuICAgICAgICAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vam9obmNhbXBpb25qci92aXRlLXBsdWdpbi12dWUtbGF5b3V0cyN2aXRlLXBsdWdpbi12dWUtbGF5b3V0c1xuICAgICAgICBMYXlvdXRzKHtcbiAgICAgICAgICAgIGxheW91dHNEaXJzOiAnLi9yZXNvdXJjZXMvanMvbGF5b3V0cy8nLFxuICAgICAgICB9KSxcblxuICAgICAgICAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vYW50ZnUvdW5wbHVnaW4tdnVlLWNvbXBvbmVudHMjdW5wbHVnaW4tdnVlLWNvbXBvbmVudHNcbiAgICAgICAgQ29tcG9uZW50cyh7XG4gICAgICAgICAgICBkaXJzOiBbJ3Jlc291cmNlcy9qcy9AY29yZS9jb21wb25lbnRzJywgJ3Jlc291cmNlcy9qcy92aWV3cy9kZW1vcycsICdyZXNvdXJjZXMvanMvY29tcG9uZW50cyddLFxuICAgICAgICAgICAgZHRzOiB0cnVlLFxuICAgICAgICAgICAgcmVzb2x2ZXJzOiBbXG4gICAgICAgICAgICAgICAgY29tcG9uZW50TmFtZSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIC8vIEF1dG8gaW1wb3J0IGBWdWVBcGV4Q2hhcnRzYFxuICAgICAgICAgICAgICAgICAgICBpZiAoY29tcG9uZW50TmFtZSA9PT0gJ1Z1ZUFwZXhDaGFydHMnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4geyBuYW1lOiAnZGVmYXVsdCcsIGZyb206ICd2dWUzLWFwZXhjaGFydHMnLCBhczogJ1Z1ZUFwZXhDaGFydHMnIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBdLFxuICAgICAgICB9KSxcblxuICAgICAgICAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vYW50ZnUvdW5wbHVnaW4tYXV0by1pbXBvcnQjdW5wbHVnaW4tYXV0by1pbXBvcnRcbiAgICAgICAgQXV0b0ltcG9ydCh7XG4gICAgICAgICAgICBpbXBvcnRzOiBbJ3Z1ZScsIFZ1ZVJvdXRlckF1dG9JbXBvcnRzLCAnQHZ1ZXVzZS9jb3JlJywgJ0B2dWV1c2UvbWF0aCcsICd2dWUtaTE4bicsICdwaW5pYSddLFxuICAgICAgICAgICAgZGlyczogW1xuICAgICAgICAgICAgICAgICcuL3Jlc291cmNlcy9qcy9AY29yZS91dGlscycsXG4gICAgICAgICAgICAgICAgJy4vcmVzb3VyY2VzL2pzL0Bjb3JlL2NvbXBvc2FibGUvJyxcbiAgICAgICAgICAgICAgICAnLi9yZXNvdXJjZXMvanMvY29tcG9zYWJsZXMvJyxcbiAgICAgICAgICAgICAgICAnLi9yZXNvdXJjZXMvanMvdXRpbHMvJyxcbiAgICAgICAgICAgICAgICAnLi9yZXNvdXJjZXMvanMvcGx1Z2lucy8qL2NvbXBvc2FibGVzLyonLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIHZ1ZVRlbXBsYXRlOiB0cnVlLFxuXG4gICAgICAgICAgICAvLyBcdTIxMzlcdUZFMEYgRGlzYWJsZWQgdG8gYXZvaWQgY29uZnVzaW9uICYgYWNjaWRlbnRhbCB1c2FnZVxuICAgICAgICAgICAgaWdub3JlOiBbJ3VzZUNvb2tpZXMnLCAndXNlU3RvcmFnZSddLFxuICAgICAgICAgICAgZXNsaW50cmM6IHtcbiAgICAgICAgICAgICAgICBlbmFibGVkOiB0cnVlLFxuICAgICAgICAgICAgICAgIGZpbGVwYXRoOiAnLi8uZXNsaW50cmMtYXV0by1pbXBvcnQuanNvbicsXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcblxuICAgICAgICBzdmdMb2FkZXIoKSxcbiAgICBdLFxuXG4gICAgZGVmaW5lOiB7ICdwcm9jZXNzLmVudic6IHt9IH0sXG5cbiAgICByZXNvbHZlOiB7XG4gICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICAnQGNvcmUtc2Nzcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzL0Bjb3JlJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAgICAgICAnQCc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvanMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICAgICAgICdAdGhlbWVDb25maWcnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vdGhlbWVDb25maWcuanMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICAgICAgICdAY29yZSc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvanMvQGNvcmUnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICAgICAgICdAbGF5b3V0cyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvanMvQGxheW91dHMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICAgICAgICdAaW1hZ2VzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9pbWFnZXMvJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAgICAgICAnQHN0eWxlcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzLycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgICAgICAgJ0Bjb25maWd1cmVkLXZhcmlhYmxlcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzL3ZhcmlhYmxlcy9fdGVtcGxhdGUuc2NzcycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgICAgICAgJ0BkYic6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvanMvcGx1Z2lucy9mYWtlLWFwaS9oYW5kbGVycy8nLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICAgICAgICdAYXBpLXV0aWxzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9qcy9wbHVnaW5zL2Zha2UtYXBpL3V0aWxzLycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgICB9LFxuICAgIH0sXG5cbiAgICBidWlsZDoge1xuICAgICAgICBjaHVua1NpemVXYXJuaW5nTGltaXQ6IDUwMDAsXG4gICAgfSxcblxuICAgIG9wdGltaXplRGVwczoge1xuICAgICAgICBleGNsdWRlOiBbJ3Z1ZXRpZnknXSxcbiAgICAgICAgZW50cmllczogW1xuICAgICAgICAgICAgJy4vcmVzb3VyY2VzL2pzLyoqLyoudnVlJyxcbiAgICAgICAgXSxcbiAgICB9LFxufSlcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFDQSxPQUFPLGFBQWE7QUFDcEIsU0FBUyxxQkFBcUI7QUFDOUIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sWUFBWTtBQUNuQixPQUFPLGdCQUFnQjtBQUN2QixPQUFPLGdCQUFnQjtBQUN2QixTQUFTLHNCQUFzQiw4QkFBOEI7QUFDN0QsT0FBTyxlQUFlO0FBQ3RCLFNBQVMsb0JBQW9CO0FBQzdCLE9BQU8sYUFBYTtBQUNwQixPQUFPLGFBQWE7QUFDcEIsT0FBTyxlQUFlO0FBWjBKLElBQU0sMkNBQTJDO0FBY2pPLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLE1B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
