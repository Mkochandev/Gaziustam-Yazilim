import {readFileSync,writeFileSync} from 'node:fs';import {spawnSync} from 'node:child_process';import {fileURLToPath} from 'node:url';
process.chdir(fileURLToPath(new URL('..',import.meta.url)));
const invoke=(args,pipe=false)=>spawnSync(process.execPath,['node_modules/wrangler/bin/wrangler.js',...args],{stdio:pipe?'pipe':'inherit',encoding:pipe?'utf8':undefined,env:{...process.env,WRANGLER_SEND_METRICS:'false'}});
let config=JSON.parse(readFileSync('wrangler.jsonc','utf8'));
if(!/^[a-f0-9-]{36}$/.test(config.d1_databases[0].database_id)){
 const result=invoke(['d1','create','gaziustam'],true);const out=(result.stdout||'')+(result.stderr||'');
 let match=out.match(/(?:database_id["']?\s*[:=]\s*["'])([0-9a-f-]{36})/i)||out.match(/\b([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\b/i);
 if(!match){const listed=invoke(['d1','list','--json'],true);try{const dbs=JSON.parse(listed.stdout);const db=dbs.find(d=>d.name==='gaziustam');if(db)match=[null,db.uuid||db.id];}catch{}}
 if(!match||!match[1]){console.error(out);throw Error('D1 oluşturulamadı. Önce npx wrangler login ile Cloudflare hesabınıza giriş yapın. Ücretli plan açmayın.');}
 config.d1_databases[0].database_id=match[1];writeFileSync('wrangler.jsonc',JSON.stringify(config,null,2)+'\n');
}
const migration=invoke(['d1','migrations','apply','DB','--remote']);if(migration.status!==0)process.exit(migration.status||1);
console.log('D1 hazır. Ücretli servis/plan etkinleştirilmedi.');
