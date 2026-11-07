import {spawnSync} from 'node:child_process';import {randomBytes} from 'node:crypto';
const run=(args,options={})=>{const r=spawnSync(process.execPath,args,{stdio:'inherit',env:{...process.env,WRANGLER_SEND_METRICS:'false'},...options});if(r.status!==0)process.exit(r.status||1);return r};
run(['scripts/setup.mjs']);
// The first deployment shows only a setup message until its secret is set.
run(['scripts/deploy.mjs']);
const result=run(['node_modules/wrangler/bin/wrangler.js','secret','list'],{encoding:'utf8',stdio:['ignore','pipe','inherit']});
let secrets;try{secrets=JSON.parse(result.stdout)}catch{throw Error('Secret listesi okunamadı; güvenli biçimde devam edilmiyor.')}
if(!secrets.some(s=>s.name==='SESSION_SECRET'))run(['node_modules/wrangler/bin/wrangler.js','secret','put','SESSION_SECRET'],{input:randomBytes(48).toString('hex')+'\n',stdio:['pipe','inherit','inherit']});
const existing=run(['node_modules/wrangler/bin/wrangler.js','d1','execute','DB','--remote','--command',"SELECT COUNT(*) AS n FROM users WHERE role='owner' AND active=1",'--json'],{encoding:'utf8',stdio:['ignore','pipe','inherit']});
let owners;try{owners=JSON.parse(existing.stdout)[0].results[0].n}catch{throw Error('Owner durumu doğrulanamadı.')}
if(!owners)run(['scripts/owner.mjs','--remote']);else console.log('Owner hesabı mevcut; şifresi değiştirilmedi.');
console.log('\nYayın tamamlandı. Yukarıdaki workers.dev adresini ve /admin yolunu açın.\n');
