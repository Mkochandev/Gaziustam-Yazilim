import {readFileSync} from 'node:fs';import {spawnSync} from 'node:child_process';
const c=JSON.parse(readFileSync('wrangler.jsonc','utf8'));if(!/^[a-f0-9-]{36}$/.test(c.d1_databases[0].database_id))throw Error('Önce npm run setup ile gerçek D1 veritabanını oluşturun.');
for(const args of [['d1','migrations','apply','DB','--remote'],['deploy']]){const r=spawnSync(process.execPath,['node_modules/wrangler/bin/wrangler.js',...args],{stdio:'inherit',env:process.env});if(r.status!==0)process.exit(r.status||1)}
