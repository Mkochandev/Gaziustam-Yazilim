const editor = document.querySelector('#schema-editor');
const raw = editor.querySelector('#schema');
const list = editor.querySelector('#fields');
const languages = ['de', 'en', 'tr'];
document.querySelector('#schema-locale').addEventListener('change', event => {
    const locale = event.target.value;
    if (locale && !languages.includes(locale)) {
        languages.push(locale);
        render();
    }
});
const add = editor.querySelector('#add-field');
let fields;
function sync() {
    raw.value = JSON.stringify(fields, null, 2);
    editor.dispatchEvent(new Event('input'));
    preview();
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
    const navigation = document.querySelector('#field-nav');
    navigation.replaceChildren();
    fields.forEach((field, index) => {
        const box = document.createElement('details');
        box.id = 'editor-field-' + index;
        box.draggable = true;
        box.addEventListener('dragstart', event => event.dataTransfer.setData('text/plain', String(index)));
        box.addEventListener('dragover', event => event.preventDefault());
        box.addEventListener('drop', event => {
            event.preventDefault();
            const source = Number(event.dataTransfer.getData('text/plain'));
            if (!Number.isInteger(source) || source < 0 || source >= fields.length || source === index) return;
            fields.splice(index, 0, fields.splice(source, 1)[0]);
            sync();
            render();
        });
        const title = document.createElement('summary');
        title.textContent = (index + 1) + '. ' + (field.labels.tr || field.key);
        const jump = document.createElement('button');
        jump.type = 'button';
        jump.textContent = title.textContent;
        jump.onclick = () => { box.open = true; title.focus(); box.scrollIntoView({block:'nearest'}); };
        navigation.append(jump);
        box.append(title);
        control(box,'Kalıcı anahtar',field.key,v => field.key = v);
        control(box,'Tür',field.type,v => field.type = v,['text','textarea','email','tel','select','radio','multi','date','checkbox','file']);
        if (['text','textarea','email','tel'].includes(field.type)) {
            control(box,'Karakter sınırı (isteğe bağlı)',field.max_length,v => {
                if (v === '') delete field.max_length;
                else field.max_length = Number(v);
            });
        }
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
    try {
        fields=JSON.parse(raw.value);
        for (const field of fields) for (const locale of Object.keys(field.labels || {})) {
            if (!languages.includes(locale)) languages.push(locale);
        }
        render(); preview(); add.hidden=false;
    }
    catch { list.replaceChildren(); add.hidden=true; }
}
raw.addEventListener('change', load);
add.onclick = () => {
    fields.push({key:'',type:'text',width:'half',required:false,active:true,labels:{de:'',en:'',tr:''},options:[]});
    sync(); render(); list.lastChild.open=true;
};
function preview() {
    const host = document.querySelector('#live-preview');
    const locale = host.dataset.locale;
    host.hidden = false;
    document.querySelector('#saved-preview').hidden = true;
    host.replaceChildren();
    for (const field of fields.filter(item => item.active)) {
        const group = document.createElement('fieldset');
        group.className = field.width === 'full' ? 'preview-wide' : '';
        const legend = document.createElement('legend');
        legend.textContent = (field.labels[locale] || field.key) + (field.required ? ' *' : '');
        group.append(legend);
        if (['select','multi'].includes(field.type)) {
            const select = document.createElement('select');
            select.setAttribute('aria-label', legend.textContent);
            select.multiple = field.type === 'multi';
            select.add(new Option('—', ''));
            for (const option of field.options || []) select.add(new Option(option.labels[locale] || option.value, option.value));
            group.append(select);
        } else if (field.type === 'radio') {
            for (const option of field.options || []) {
                const label = document.createElement('label');
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'preview-' + field.key;
                label.append(input, document.createTextNode(option.labels[locale] || option.value));
                group.append(label);
            }
        } else {
            const input = document.createElement(field.type === 'textarea' ? 'textarea' : 'input');
            if (field.type !== 'textarea') input.type = field.type;
            input.placeholder = field.placeholder?.[locale] || '';
            input.setAttribute('aria-label', legend.textContent);
            group.append(input);
        }
        const help = document.createElement('small');
        help.textContent = field.help?.[locale] || '';
        group.append(help);
        host.append(group);
    }
}
load();

