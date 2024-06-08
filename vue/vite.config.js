import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from "path";
import liveReload from 'vite-plugin-live-reload'; // Plugin thực hiện reload khi thay đổi code php

export default defineConfig({
  plugins: [
    vue(),
    liveReload(`${__dirname}/../**/*\.php`), // Cấu hình reload file php
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  base: '/banhang/wp-content/plugins/k-sale/admin/build/', // Cấu hình base url
  build: {
    sourcemap: true,
    rollupOptions: {
      output: {
        dir: path.join(__dirname, '../admin/build'), // định nghĩa đường dẫn thư mục build
        entryFileNames: 'app.js', // tên file sau khi build
        assetFileNames: (assetInfo) => {
          let extType = assetInfo.name.split('.')[1];
          // Cấu hình file css tĩnh khi được build ra
          if (extType == 'css') {
            return `app.css`;
          } else {
            return `assets/[name]-[hash][extname]`;
          }
        }
      }
    }
  }
})