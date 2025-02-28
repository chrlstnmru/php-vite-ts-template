This is a starter template for working with PHP, Composer, and Vite with Typescript.

This template took an inspiration from [**andrefelipe/vite-php-setup**](https://github.com/andrefelipe/vite-php-setup) and integrate composer and typescript.

> This template is an attempt to integrate the power of vite with traditional PHP thus the template maybe unstable.

### Key features

- HMR
- CSS and JS minification
- Seamless integration of node packages with PHP

### Folder structure

- `public` - the root directory of your web server
- `public/dist` - the complied output of vite build
- `public/includes` - directory containing your server-related php files and composer vendor
- `src` - source directory
- `src/modules` - files within this folder will be emitted as an entry file

### Setting up dev environment

Install NodeJS version 18+, Composer, and PNPM (optional)

Configure you project to run on a local server. For example, [http://php-vite-ts-template.test](http://php-vite-ts-template.test). If your using XAMPP, configure your [virtual host](https://stackoverflow.com/questions/27268205/how-to-create-virtual-host-on-xampp).

It is highly recommended to configure it since the assets reference from files, for example a background image in css, `url('./bg.png')` will be complied to `url('/dist/assets/bg-sAsjks.png')`.

Duplicate the `.env.example` and rename it to `.env`, this is used to configure the vite server.

Install dependencies

```bash
composer install
pnpm install
```

Start your PHP web server and run the vite server

```bash
pnpm run dev
```
