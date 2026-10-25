/* Copyright (C) 2026 Yann Gomiero. GPL-2.0-or-later. */
const {readFileSync}=require('node:fs');
const vm=require('node:vm');
const assert=require('node:assert/strict');
let options;
const box={id:'fixture',dataset:{mosparoFields:'["username"]'},closest:()=>({})};
const context={
  Joomla:{getOptions:name=>name==='plg_captcha_mosparo'?{url:'https://example.invalid',uuid:'demo',publicKey:'demo'}:false},
  document:{readyState:'complete',querySelectorAll:()=>[box]},
  mosparo:function(id,url,uuid,key,config){options=config;this.hpFieldElement={name:'spam_trap'};}
};
vm.runInNewContext(readFileSync(__dirname+'/../plugins/captcha/mosparo/media/js/integration.js','utf8'),context);
const data={fields:[{name:'username',value:'person'},{name:'password',value:'visible password'},{name:'spam_trap',value:'bot content'},{name:'other',value:'private note'}],ignoredFields:[]};
const filtered=options.onGetFormData({},data);
assert.deepEqual(Array.from(filtered.fields,f=>f.name),['username','spam_trap']);
assert.equal(filtered.fields[1].value,'bot content');
assert.deepEqual(Array.from(filtered.ignoredFields),['password','other']);
assert.equal(box.dataset.mosparoMounted,'1');
console.log('4 frontend checks passed: whitelist, visible password exclusion, honeypot preservation, mounted state.');
