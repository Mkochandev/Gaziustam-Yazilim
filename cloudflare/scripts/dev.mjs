import {spawn} from 'node:child_process';
const input=process.argv.slice(2);let host='127.0.0.1',port=process.env.PORT||'8787';
for(let i=0;i<input.length;i++){const a=input[i];if(a==='--host'||a==='--ip')host=input[++i];else if(a==='--port')port=input[++i];else if(a.startsWith('--host=')||a.startsWith('--ip='))host=a.split('=')[1];else if(a.startsWith('--port='))port=a.split('=')[1];}
const child=spawn(process.execPath,['node_modules/wrangler/bin/wrangler.js','dev','--inspector-port','0','--ip',host,'--port',port],{stdio:'inherit',env:{...process.env,WRANGLER_SEND_METRICS:'false'}});
child.on('exit',code=>process.exit(code||0));for(const signal of ['SIGTERM','SIGINT'])process.on(signal,()=>child.kill(signal));
