import { defineConfig } from 'vite'
import liveReload from 'vite-plugin-live-reload'
import config from './config'
import dynamicImportVars from '@rollup/plugin-dynamic-import-vars'
import { resolve } from 'path'
import reactRefresh from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    reactRefresh(),
    liveReload(config.refresh.map(path => resolve(__dirname, '../../' + path)))
  ],
  root: './' + config.rootAssetFolder,
  base: '/' + config.rootAssetFolder + '/',
  build: {
    outDir: resolve(__dirname, '../../' + config.output),
    assetsDir: '',
    manifest: true,
    rollupOptions: {
      plugins: [dynamicImportVars()],
      output: {
        manualChunks: undefined // Désactive la séparation du vendor
      },
      input: config.entry
    }
  }
})
