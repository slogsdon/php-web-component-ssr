import { defineConfig } from "vite";
import { cloudflare } from "@cloudflare/vite-plugin";

export default defineConfig({
  publicDir: "dist",
  plugins: [cloudflare({ assetsOnly: true, types: { generate: false } })],
  build: { outDir: ".runtime/cloudflare", emptyOutDir: true },
});
