import { defineConfig, loadEnv, UserConfig } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import getEntries from './get-entries';

export default defineConfig(({ mode }) => {
  process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

  let origin = 'http://';
  origin += process.env.VITE_HOSTNAME ?? 'localhost';
  origin += process.env.VITE_PORT ? `:${process.env.VITE_PORT}` : '5173';

  const devRoot = 'src';
  const buildDir = process.env.VITE_BUILD_DIR ?? 'dist';

  const config: UserConfig = {
    root: devRoot,
    base: mode === 'development' ? '/' : `/${buildDir}`,
    build: {
      outDir: `../public/${buildDir}`,
      copyPublicDir: false,
      emptyOutDir: true,
      manifest: true,
      rollupOptions: {
        input: getEntries(devRoot),
        output: {
          manualChunks: function (id) {
            if (id.includes('node_modules')) {
              return 'vendor';
            }
          },
          assetFileNames: 'assets/[name][extname]',
          chunkFileNames: 'chunks/[name].js',
          entryFileNames: 'entries/[name].js',
        },
      },
    },
    server: {
      strictPort: true,
      origin: origin,
      host: process.env.VITE_HOSTNAME,
      port: process.env.VITE_PORT! as unknown as number,
      cors: {
        origin: '*',
      },
    },
    plugins: [liveReload([__dirname + '/public/**/*.(php|html|js|css)', __dirname + '/src/**/*.(js|css|ts)'])],
  };

  return config;
});
