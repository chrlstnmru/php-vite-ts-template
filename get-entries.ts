import crypto from 'node:crypto';
import { resolve } from 'node:path';
import { globSync } from 'tinyglobby';

//
export default function getEntries(dir: string, moduleDir: string = 'modules'): { [entryAlias: string]: string } {
  const entries: { [entryAlias: string]: string } = {};
  const files = globSync(`${dir}/${moduleDir}/**/*.{ts,js,css}`, {
    ignore: ['**/_*', '**/*.d.ts'],
  });

  files.push(resolve(__dirname, dir, 'main.ts'));
  files.push(resolve(__dirname, dir, 'main.css'));

  files.forEach((file) => {
    const absolutePath = resolve(__dirname, file);
    const hash = crypto.createHash('md5').update(absolutePath).digest('base64url').slice(0, 8);
    const name = `${file.replace(/^.*[\\/]/, '').split('.')[0]}-${hash}`;
    entries[name] = absolutePath;
  });

  return entries;
}
