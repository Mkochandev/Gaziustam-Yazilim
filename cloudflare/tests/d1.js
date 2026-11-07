import {DatabaseSync} from 'node:sqlite';
import {readFileSync,readdirSync} from 'node:fs';
export class D1{constructor(){this.db=new DatabaseSync(':memory:');for(const f of readdirSync(new URL('../migrations/',import.meta.url)).sort())this.db.exec(readFileSync(new URL('../migrations/'+f,import.meta.url),'utf8'));}
 prepare(sql){const db=this.db;return {sql,params:[],bind(...params){this.params=params;return this},first(){const row=db.prepare(sql).get(...this.params);return Promise.resolve(row||null)},all(){return Promise.resolve({results:db.prepare(sql).all(...this.params)})},run(){const r=db.prepare(sql).run(...this.params);return Promise.resolve({meta:r})}}}
 async batch(statements){this.db.exec('BEGIN');try{const result=[];for(const s of statements)result.push(await s.run());this.db.exec('COMMIT');return result}catch(e){this.db.exec('ROLLBACK');throw e}}
}
