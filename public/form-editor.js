const editor = document.querySelector('#schema-editor');
const raw = editor.querySelector('#schema');
const list = editor.querySelector('#fields');
const languages = ['de', 'en', 'tr'];
const add = editor.querySelector('#add-field');
let fields;
function sync() {
    raw.value = JSON.stringify(fields, null, 2);
    editor.dispatchEvent(new Event('input'));
}
function control(parent, title, value, changed, choices) {
    const label = document.createElement('label');
    label.textContent = title;
    const input = document.createElement(choices ? 'select' : 'input');
    if (choices) for (const item of choices) input.add(new Option(item, item));
    input.value = value ?? '';
    input.oninput = () => { changed(input.value); sync(); };
    label.append(input);
    parent.append(label);
}
function button(parent, title, run) {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = title;
    button.onclick = () => { run(); sync(); render(); };
    parent.append(button);
}
function render() {
    list.replaceChildren();
    fields.forEach((field, index) => {
        const box = document.createElement('details');
        const title = document.createElement('summary');
        title.textContent = (index + 1) + '. ' + (field.labels.tr || field.key);
        box.append(title);
        control(box,'Kalıcı anahtar',field.key,v => field.key = v);
        control(box,'Tür',field.type,v => field.type = v,['text','textarea','email','tel','select','radio','multi','date','checkbox','file']);
        control(box,'Genişlik',field.width,v => field.width = v,['half','full']);
        for (const [key, label] of [['active','Etkin'],['required','Zorunlu']]) {
            control(box,label,field[key]?'Evet':'Hayır',v => field[key] = v==='Evet',['Evet','Hayır']);
        }
        for (const lang of languages) {
            control(box,lang.toUpperCase()+' etiket',field.labels[lang],v => field.labels[lang]=v);
            for (const [prop,label] of [['placeholder','yer tutucu'],['help','yardım']]) {
                control(box,lang.toUpperCase()+' '+label,field[prop]?.[lang],v => { field[prop] ??= {}; field[prop][lang]=v; });
            }
        }
        (field.options || []).forEach((option,i) => {
            const group=document.createElement('fieldset');
            control(group,'Seçenek değeri',option.value,v => option.value=v);
            for (const lang of languages) control(group,lang+' etiket',option.labels[lang],v => option.labels[lang]=v);
            button(group,'Seçeneği kaldır',()=>field.options.splice(i,1));
            box.append(group);
        });
        button(box,'Seçenek ekle',()=>{ (field.options ??= []).push({value:'',labels:{}}); });
        if(index) button(box,'↑ Yukarı',()=>fields.splice(index-1,0,fields.splice(index,1)[0]));
        if(index+1<fields.length) button(box,'↓ Aşağı',()=>fields.splice(index+1,0,fields.splice(index,1)[0]));
        if (!['contact_name','contact_email','message'].includes(field.key)) button(box,'Alanı kaldır',()=>fields.splice(index,1));
        list.append(box);
    });
}
function load() {
    try { fields=JSON.parse(raw.value); render(); add.hidden=false; }
    catch { list.replaceChildren(); add.hidden=true; }
}
raw.addEventListener('change', load);
add.onclick = () => {
    fields.push({key:'',type:'text',width:'half',required:false,active:true,labels:{de:'',en:'',tr:''},options:[]});
    sync(); render(); list.lastChild.open=true;
};
load();
