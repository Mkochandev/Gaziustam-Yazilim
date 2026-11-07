import {readFileSync,writeFileSync} from 'node:fs';
import {defaultSchema} from '../src/forms.js';
const q=s=>"'"+String(s).replaceAll("'","''")+"'";
const ls=readFileSync('../database/seeders/locales.txt','utf8').trim().split(/\r?\n/).map((line,i)=>{const [code,name]=line.split('|');return `INSERT OR IGNORE INTO locales VALUES(${q(code)},${q(name)},0,${i});`}).join('\n');
writeFileSync('migrations/0002_initial_form.sql',ls+`\nINSERT OR IGNORE INTO form_versions(id,version_number,status,schema,created_at,published_at) VALUES('initial-project-form',1,'published',${q(JSON.stringify(defaultSchema()))},'2026-11-07T00:00:00.000Z','2026-11-07T00:00:00.000Z');\n`);
