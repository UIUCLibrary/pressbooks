!function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:r})},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=23)}({23:function(t,e,n){t.exports=n("ADpY")},ADpY:function(t,e){tinymce.create("tinymce.plugins.glossary",{init:function(t,e){var n=PB_GlossaryToken.glossary_terms.replace(/&quot;/g,'"'),r=jQuery.parseJSON(n),o=Object.keys(r);function a(t){return o.filter(function(e){return-1!==e.toLowerCase().indexOf(t.toLowerCase().trim())})}function s(t){var e=a(t);return void 0!==e[0]&&e[0]}function i(t){var e=a(t);if(void 0===e[0])return"";var n=e.map(function(t){return r[t].id});return Array.isArray(n)||n.length?n[0]:void 0}t.addButton("glossary_all",{title:PB_GlossaryToken.glossary_all_title,text:"Glossary",icon:!1,onclick:function(){t.selection.setContent("[pb_glossary]")}}),t.addButton("glossary",{title:PB_GlossaryToken.glossary_title,text:"GL",icon:!1,onclick:function(){var e=t.selection.getContent(),n="",r="";!1!==s(e)?n=s(e):r='Glossary term <b>"'+e+'"</b> not found.<br />Please create it, or select a term from the list below to use that definition:',tinymce.activeEditor.windowManager.open({title:"Glossary terms",width:500,height:100,buttons:[{text:"Insert",subtype:"primary",onclick:"submit"},{text:"Close",onclick:"close"}],body:[{type:"container",name:"container",html:r},{type:"listbox",name:"terms",label:"Select a Term",values:function(){var t=[],e=!0,n=!1,r=void 0;try{for(var a,s=o[Symbol.iterator]();!(e=(a=s.next()).done);e=!0){var i=a.value,l={};l.text=i,l.value=i,t.push(l)}}catch(t){n=!0,r=t}finally{try{!e&&s.return&&s.return()}finally{if(n)throw r}}return t.sort(function(t,e){return t.text>e.text?1:e.text>t.text?-1:0}),t}(),value:n}],onsubmit:function(n){""!==e?t.selection.setContent('[pb_glossary id="'+i(n.data.terms)+'"]'+e+"[/pb_glossary]"):t.selection.setContent('[pb_glossary id="'+i(n.data.terms)+'"]'+n.data.terms+"[/pb_glossary]")}})}})},createControl:function(t,e){return null}}),tinymce.PluginManager.add("glossary_all",tinymce.plugins.glossary.all),tinymce.PluginManager.add("glossary",tinymce.plugins.glossary)}});