(window.webpackJsonp=window.webpackJsonp||[]).push([[19],{113:function(t,e,n){"use strict";var r=n(150),a=n(56),c=n(35),i=n(151),s=n(36),o=n(57),u=n(45),l=n(103),b=n(114),d=n(133),f=n(66),j=n(90),p=n(190),O=n(112),v=n(325),g=n(152),h=n(48)("replace"),m=Math.max,w=Math.min,y=c([].concat),_=c([].push),k=c("".indexOf),x=c("".slice),P="$0"==="a".replace(/./,"$0"),C=!!/./[h]&&""===/./[h]("a","$0");i("replace",(function(t,e,n){var c=C?"$":"$0";return[function(t,n){var r=j(this),c=l(t)?void 0:O(t,h);return c?a(c,t,r,n):a(e,f(r),t,n)},function(t,a){var i=o(this),s=f(t);if("string"==typeof a&&-1===k(a,c)&&-1===k(a,"$<")){var l=n(e,i,s,a);if(l.done)return l.value}var j=u(a);j||(a=f(a));var O=i.global;if(O){var h=i.unicode;i.lastIndex=0}for(var P=[];;){var C=g(i,s);if(null===C)break;if(_(P,C),!O)break;""===f(C[0])&&(i.lastIndex=p(s,d(i.lastIndex),h))}for(var S,B="",D=0,$=0;$<P.length;$++){for(var A=f((C=P[$])[0]),T=m(w(b(C.index),s.length),0),E=[],U=1;U<C.length;U++)_(E,void 0===(S=C[U])?S:String(S));var z=C.groups;if(j){var I=y([A],E,T,s);void 0!==z&&_(I,z);var R=f(r(a,void 0,I))}else R=v(A,s,T,E,z,a);T>=D&&(B+=x(s,D,T)+R,D=T+A.length)}return B+x(s,D)}]}),!!s((function(){var t=/./;return t.exec=function(){var t=[];return t.groups={a:"7"},t},"7"!=="".replace(t,"$<a>")}))||!P||C)},150:function(t,e,n){var r=n(158),a=Function.prototype,c=a.apply,i=a.call;t.exports="object"==typeof Reflect&&Reflect.apply||(r?i.bind(c):function(){return i.apply(c,arguments)})},154:function(t,e,n){"use strict";n.d(e,"a",(function(){return w}));var r=n(41),a=n(4),c=n(1),i=n(26),s=n(14),o=n(46),u=n(21),l=n(3),b=n(105),d=n(9),f=n(0),j=n(15);function p(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function O(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?p(Object(n),!0).forEach((function(e){v(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):p(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}function v(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}var g=["auto","start","end","center","baseline","stretch"],h=Object(b.a)((function(t,e,n){var r=t;if(!Object(l.p)(n)&&!1!==n)return e&&(r+="-".concat(e)),"col"!==t||""!==n&&!0!==n?(r+="-".concat(n),Object(j.c)(r)):Object(j.c)(r)})),m=Object(d.c)(null),w={name:a.r,functional:!0,get props(){return delete this.props,this.props=(t=Object(o.b)().filter(u.a),e=t.reduce((function(t,e){return t[e]=Object(f.c)(c.i),t}),Object(d.c)(null)),n=t.reduce((function(t,e){return t[Object(f.g)(e,"offset")]=Object(f.c)(c.p),t}),Object(d.c)(null)),r=t.reduce((function(t,e){return t[Object(f.g)(e,"order")]=Object(f.c)(c.p),t}),Object(d.c)(null)),m=Object(d.a)(Object(d.c)(null),{col:Object(d.h)(e),offset:Object(d.h)(n),order:Object(d.h)(r)}),Object(f.d)(Object(d.m)(O(O(O(O({},e),n),r),{},{alignSelf:Object(f.c)(c.u,null,(function(t){return Object(s.a)(g,t)})),col:Object(f.c)(c.g,!1),cols:Object(f.c)(c.p),offset:Object(f.c)(c.p),order:Object(f.c)(c.p),tag:Object(f.c)(c.u,"div")})),a.r));var t,e,n,r},render:function(t,e){var n,a=e.props,c=e.data,s=e.children,o=a.cols,u=a.offset,l=a.order,b=a.alignSelf,d=[];for(var f in m)for(var j=m[f],p=0;p<j.length;p++){var O=h(f,j[p].replace(f,""),a[j[p]]);O&&d.push(O)}var g=d.some((function(t){return i.c.test(t)}));return d.push((v(n={col:a.col||!g&&!o},"col-".concat(o),o),v(n,"offset-".concat(u),u),v(n,"order-".concat(l),l),v(n,"align-self-".concat(b),b),n)),t(a.tag,Object(r.a)(c,{class:d}),s)}}},220:function(t,e,n){"use strict";n.d(e,"b",(function(){return p})),n.d(e,"a",(function(){return O}));var r=n(6),a=n(41),c=n(4),i=n(1),s=n(14),o=n(21),u=n(3),l=n(16),b=n(0),d=n(15);function f(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}var j='<svg width="%{w}" height="%{h}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %{w} %{h}" preserveAspectRatio="none"><rect width="100%" height="100%" style="fill:%{f};"></rect></svg>',p=Object(b.d)({alt:Object(b.c)(i.u),blank:Object(b.c)(i.g,!1),blankColor:Object(b.c)(i.u,"transparent"),block:Object(b.c)(i.g,!1),center:Object(b.c)(i.g,!1),fluid:Object(b.c)(i.g,!1),fluidGrow:Object(b.c)(i.g,!1),height:Object(b.c)(i.p),left:Object(b.c)(i.g,!1),right:Object(b.c)(i.g,!1),rounded:Object(b.c)(i.j,!1),sizes:Object(b.c)(i.f),src:Object(b.c)(i.u),srcset:Object(b.c)(i.f),thumbnail:Object(b.c)(i.g,!1),width:Object(b.c)(i.p)},c.Q),O=Object(r.c)({name:c.Q,functional:!0,props:p,render:function(t,e){var n,r=e.props,c=e.data,i=r.alt,b=r.src,p=r.block,O=r.fluidGrow,v=r.rounded,g=Object(l.b)(r.width)||null,h=Object(l.b)(r.height)||null,m=null,w=Object(s.b)(r.srcset).filter(o.a).join(","),y=Object(s.b)(r.sizes).filter(o.a).join(",");return r.blank&&(!h&&g?h=g:!g&&h&&(g=h),g||h||(g=1,h=1),b=function(t,e,n){var r=encodeURIComponent(j.replace("%{w}",Object(d.g)(t)).replace("%{h}",Object(d.g)(e)).replace("%{f}",n));return"data:image/svg+xml;charset=UTF-8,".concat(r)}(g,h,r.blankColor||"transparent"),w=null,y=null),r.left?m="float-left":r.right?m="float-right":r.center&&(m="mx-auto",p=!0),t("img",Object(a.a)(c,{attrs:{src:b,alt:i,width:g?Object(d.g)(g):null,height:h?Object(d.g)(h):null,srcset:w||null,sizes:y||null},class:(n={"img-thumbnail":r.thumbnail,"img-fluid":r.fluid||O,"w-100":O,rounded:""===v||!0===v},f(n,"rounded-".concat(v),Object(u.n)(v)&&""!==v),f(n,m,m),f(n,"d-block",p),n)}))}})},325:function(t,e,n){var r=n(35),a=n(91),c=Math.floor,i=r("".charAt),s=r("".replace),o=r("".slice),u=/\$([$&'`]|\d{1,2}|<[^>]*>)/g,l=/\$([$&'`]|\d{1,2})/g;t.exports=function(t,e,n,r,b,d){var f=n+t.length,j=r.length,p=l;return void 0!==b&&(b=a(b),p=u),s(d,p,(function(a,s){var u;switch(i(s,0)){case"$":return"$";case"&":return t;case"`":return o(e,0,n);case"'":return o(e,f);case"<":u=b[o(s,1,-1)];break;default:var l=+s;if(0===l)return a;if(l>j){var d=c(l/10);return 0===d?a:d<=j?void 0===r[d-1]?i(s,1):r[d-1]+i(s,1):a}u=r[l-1]}return void 0===u?"":u}))}},427:function(t,e,n){"use strict";n.r(e);n(60),n(113);var r=n(503),a=n(495),c=n(501),i=n(154),s=n(221),o=n(519),u=n(498),l=n(85),b={components:{BCard:r.a,BCardText:a.a,BRow:c.a,BCol:i.a,BSpinner:s.a,BTable:o.a,BBadge:u.a,BButton:l.a},data:function(){return{isBusy:!0,data:{},fields:[{key:"mapel",label:"Mata Pelajaran",thClass:"text-center"},{key:"status_ujian",label:"Status Ujian",thClass:"text-center",tdClass:"text-center"},{key:"status",label:"Aktif",thClass:"text-center",tdClass:"text-center"},{key:"actions",label:"Aksi",sortable:!1,thClass:"text-center",tdClass:"text-center"}],items:[]}},created:function(){var t=this;this.hasRole("siswa")?this.$http.get("/general/index").then((function(e){t.isBusy=!1,t.data=e.data,t.items=e.data.ujian})):this.$router.replace({name:"dashboard"})},methods:{ujian:function(t){this.$router.replace({name:"ujian",params:{ujian_id:t.ujian_id},query:{nomor:1}})}}},d=n(12),f=Object(d.a)(b,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[t.isBusy?n("b-row",[n("b-col",[n("b-card",{staticClass:"text-center"},[n("div",{staticClass:"text-center text-danger my-2"},[n("b-spinner",{staticClass:"align-middle"}),t._v(" "),n("div",[n("strong",[t._v("Loading...")])])],1)])],1)],1):n("b-row",[n("b-col",[n("b-card",[n("b-card-text",[n("h2",{staticClass:"fw-bolder"},[t._v("SELAMAT DATANG "+t._s(t.data.user.name))]),t._v(" "),n("p",{staticClass:"card-text"},[t._v("Anda anggota "+t._s(t.data.rombel.nama))]),t._v(" "),n("h3",{staticClass:"fw-bolder"},[t._v("Jadwal Ujian di "+t._s(t.data.rombel.nama))]),t._v(" "),n("b-table",{attrs:{responsive:"",striped:"",items:t.items,fields:t.fields,"show-empty":"",busy:t.isBusy},scopedSlots:t._u([{key:"table-busy",fn:function(){return[n("div",{staticClass:"text-center text-danger my-2"},[n("b-spinner",{staticClass:"align-middle"}),t._v(" "),n("strong",[t._v("Loading...")])],1)]},proxy:!0},{key:"cell(mapel)",fn:function(e){return[t._v("\n              "+t._s(e.item.pembelajaran.nama_mata_pelajaran)+"\n            ")]}},{key:"cell(status_ujian)",fn:function(e){return[e.item.soal_count?e.item.ujian_siswa?[e.item.ujian_siswa.status?n("b-badge",{attrs:{variant:"success"}},[t._v("Selesai")]):n("b-badge",{attrs:{variant:"warning"}},[t._v("Sedang Berjalan")])]:[e.item.soal_count===e.item.jawaban_siswa_count?n("b-badge",{attrs:{variant:"success"}},[t._v("Sudah Dilaksanakan")]):t._e(),t._v(" "),e.item.jawaban_siswa_count&&e.item.soal_count<e.item.jawaban_siswa_count?n("b-badge",{attrs:{variant:"warning"}},[t._v("Sedang Berjalan")]):t._e(),t._v(" "),e.item.jawaban_siswa_count?t._e():n("b-badge",{attrs:{variant:"danger"}},[t._v("Belum Dilaksanakan")])]:[n("b-badge",{attrs:{variant:"secondary"}},[t._v("Soal belum tersedia")])]]}},{key:"cell(status)",fn:function(e){return[e.item.soal_count?[e.item.status?n("b-badge",{attrs:{variant:"success"}},[t._v("YA")]):n("b-badge",{attrs:{variant:"danger"}},[t._v("TIDAK")])]:[n("b-badge",{attrs:{variant:"secondary"}},[t._v("TIDAK")])]]}},{key:"cell(actions)",fn:function(e){return[e.item.soal_count?e.item.ujian_siswa?[e.item.ujian_siswa.status?n("b-button",{attrs:{variant:"secondary",disabled:"",size:"sm"}},[t._v("Ujian Selesai")]):n("b-button",{attrs:{variant:"warning",size:"sm"},on:{click:function(n){return t.ujian(e.item)}}},[t._v("Lanjutkan Ujian")])]:[e.item.status?n("b-button",{attrs:{variant:"success",size:"sm"},on:{click:function(n){return t.ujian(e.item)}}},[t._v("Proses Ujian")]):n("b-button",{attrs:{variant:"secondary",disabled:"",size:"sm"}},[t._v("Proses Ujian")])]:[n("b-badge",{attrs:{variant:"secondary"}},[t._v("Proses Ujian")])]]}}])})],1)],1)],1)],1)],1)}),[],!1,null,null,null);e.default=f.exports},495:function(t,e,n){"use strict";n.d(e,"a",(function(){return u}));var r=n(6),a=n(41),c=n(4),i=n(1),s=n(0),o=Object(s.d)({textTag:Object(s.c)(i.u,"p")},c.p),u=Object(r.c)({name:c.p,functional:!0,props:o,render:function(t,e){var n=e.props,r=e.data,c=e.children;return t(n.textTag,Object(a.a)(r,{staticClass:"card-text"}),c)}})},498:function(t,e,n){"use strict";n.d(e,"a",(function(){return O}));var r=n(6),a=n(41),c=n(4),i=n(1),s=n(9),o=n(0),u=n(77),l=n(38);function b(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function d(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?b(Object(n),!0).forEach((function(e){f(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):b(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}function f(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}var j=Object(s.j)(l.b,["event","routerTag"]);delete j.href.default,delete j.to.default;var p=Object(o.d)(Object(s.m)(d(d({},j),{},{pill:Object(o.c)(i.g,!1),tag:Object(o.c)(i.u,"span"),variant:Object(o.c)(i.u,"secondary")})),c.c),O=Object(r.c)({name:c.c,functional:!0,props:p,render:function(t,e){var n=e.props,r=e.data,c=e.children,i=n.active,s=n.disabled,b=Object(u.d)(n),d=b?l.a:n.tag,f=n.variant||"secondary";return t(d,Object(a.a)(r,{staticClass:"badge",class:["badge-".concat(f),{"badge-pill":n.pill,active:i,disabled:s}],props:b?Object(o.e)(j,n):{}}),c)}})},501:function(t,e,n){"use strict";n.d(e,"a",(function(){return m}));var r=n(41),a=n(4),c=n(1),i=n(14),s=n(46),o=n(21),u=n(105),l=n(9),b=n(0),d=n(15);function f(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function j(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?f(Object(n),!0).forEach((function(e){p(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):f(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}function p(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}var O=["start","end","center"],v=Object(u.a)((function(t,e){return(e=Object(d.h)(Object(d.g)(e)))?Object(d.c)(["row-cols",t,e].filter(o.a).join("-")):null})),g=Object(u.a)((function(t){return Object(d.c)(t.replace("cols",""))})),h=[],m={name:a.kb,functional:!0,get props(){var t;return delete this.props,this.props=(t=Object(s.b)().reduce((function(t,e){return t[Object(b.g)(e,"cols")]=Object(b.c)(c.p),t}),Object(l.c)(null)),h=Object(l.h)(t),Object(b.d)(Object(l.m)(j(j({},t),{},{alignContent:Object(b.c)(c.u,null,(function(t){return Object(i.a)(Object(i.b)(O,"between","around","stretch"),t)})),alignH:Object(b.c)(c.u,null,(function(t){return Object(i.a)(Object(i.b)(O,"between","around"),t)})),alignV:Object(b.c)(c.u,null,(function(t){return Object(i.a)(Object(i.b)(O,"baseline","stretch"),t)})),noGutters:Object(b.c)(c.g,!1),tag:Object(b.c)(c.u,"div")})),a.kb)),this.props},render:function(t,e){var n,a=e.props,c=e.data,i=e.children,s=a.alignV,o=a.alignH,u=a.alignContent,l=[];return h.forEach((function(t){var e=v(g(t),a[t]);e&&l.push(e)})),l.push((p(n={"no-gutters":a.noGutters},"align-items-".concat(s),s),p(n,"justify-content-".concat(o),o),p(n,"align-content-".concat(u),u),n)),t(a.tag,Object(r.a)(c,{staticClass:"row",class:l}),i)}}}}]);