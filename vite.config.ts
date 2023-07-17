import path from "path";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import checker from "vite-plugin-checker";

import Components from "unplugin-vue-components/vite";
import AutoImport from "unplugin-auto-import/vite";
import { ElementPlusResolver } from "unplugin-vue-components/resolvers";

const pathSrc = path.resolve(__dirname, "src");

// https://vitejs.dev/config/
export default defineConfig({
	resolve: {
		alias: {
			"@/*": `${pathSrc}/`,
			"~/*": `${pathSrc}/`,
		},
	},
	plugins: [
		vue(),
		Components({
			// allow auto load markdown components under `./src/components/`
			extensions: ["vue", "md"],
			// allow auto import and register components used in markdown
			include: [/\.vue$/, /\.vue\?vue/, /\.md$/],
			resolvers: [
				ElementPlusResolver({
					importStyle: "sass",
				}),
			],
			dts: "src/components.d.ts",
		}),

		AutoImport({
			resolvers: [ElementPlusResolver()],
		}),
		Components({
			resolvers: [ElementPlusResolver()],
		}),
		checker({
			// e.g. use TypeScript check
			typescript: true,
			vueTsc: true,
			eslint: {
				// for example, lint .ts and .tsx
				lintCommand: "eslint \"./src/**/*.{ts,tsx}\"",
			},
		}),
	]
});
