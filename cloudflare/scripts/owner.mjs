import {createInterface,emitKeypressEvents} from 'node:readline';
import {spawnSync} from 'node:child_process';
import {writeFileSync,unlinkSync} from 'node:fs';
import {fileURLToPath} from 'node:url';
import {passwordHash,id} from '../src/security.js';
process.chdir(fileURLToPath(new URL('..',import.meta.url)));
if(!process.argv.includes('--local')&&!process.argv.includes('--remote'))throw Error('--local veya --remote seçin.');
const rl=createInterface({input:process.stdin,output:process.stdout});const ask=q=>new Promise(r=>rl.question(q,r));
const email=(await ask('Owner e-posta: ')).trim().toLowerCase(),name=(await ask('İsim: ')).trim();rl.close();
if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)||!name||name.length>100)throw Error('E-posta/ad geçersiz.');
async function secret(prompt){if(!process.stdin.isTTY)throw Error('Şifre için etkileşimli terminal açın.');process.stdout.write(prompt);emitKeypressEvents(process.stdin);process.stdin.setRawMode(true);process.stdin.resume();return new Promise((resolve,reject)=>{let value='';const onKey=(str,key={})=>{if(key.ctrl&&key.name==='c'){process.stdin.setRawMode(false);process.exit(130)}if(key.name==='return'){process.stdin.off('keypress',onKey);process.stdin.setRawMode(false);process.stdin.pause();process.stdout.write('\n');resolve(value)}else if(key.name==='backspace'){value=value.slice(0,-1)}else if(!key.ctrl&&!key.meta&&str)value+=str};process.stdin.on('keypress',onKey)});}
const password=await secret('Şifre (en az 12 karakter; görünmez): ');if(password.length<12||password.length>200)throw Error('Şifre 12–200 karakter olmalı.');const confirm=await secret('Şifre tekrar: ');if(password!==confirm)throw Error('Şifreler eşleşmiyor.');
const quote=s=>"'"+s.replaceAll("'","''")+"'";const hash=await passwordHash(password);const sql=`INSERT INTO users(id,email,name,password_hash,role,active) VALUES(${[id(),email,name,hash,'owner'].map(quote).join(',')},1);`;
const file='.owner-'+id()+'.sql';writeFileSync(file,sql,{mode:0o600});try{const r=spawnSync(process.execPath,['node_modules/wrangler/bin/wrangler.js','d1','execute','DB',process.argv.includes('--remote')?'--remote':'--local','--file',file],{stdio:'inherit',env:process.env});if(r.status!==0)throw Error('Owner oluşturulamadı. Aynı e-posta zaten varsa mevcut hesabı panelden yönetin.');console.log('Owner oluşturuldu. /admin adresinden giriş yapın.')}finally{unlinkSync(file)}
