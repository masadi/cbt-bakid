(window.webpackJsonp=window.webpackJsonp||[]).push([[23],{172:function(t,n,e){"use strict";var r={components:{BImg:e(220).a},data:function(){return{sideImg:"/img/logo/logo.png"}},computed:{imgUrl:function(){return this.sideImg}}},i=e(11),a=Object(i.a)(r,(function(){var t=this.$createElement;return(this._self._c||t)("b-img",{attrs:{src:this.imgUrl,height:"24",alt:"Logo"}})}),[],!1,null,null,null);n.a=a.exports},220:function(t,n,e){"use strict";e.d(n,"b",(function(){return b})),e.d(n,"a",(function(){return h}));var r=e(6),i=e(42),a=e(4),c=e(1),s=e(14),l=e(21),o=e(3),p=e(16),u=e(0),g=e(15);function m(t,n,e){return n in t?Object.defineProperty(t,n,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[n]=e,t}var d='<svg width="%{w}" height="%{h}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %{w} %{h}" preserveAspectRatio="none"><rect width="100%" height="100%" style="fill:%{f};"></rect></svg>',b=Object(u.d)({alt:Object(u.c)(c.u),blank:Object(u.c)(c.g,!1),blankColor:Object(u.c)(c.u,"transparent"),block:Object(u.c)(c.g,!1),center:Object(u.c)(c.g,!1),fluid:Object(u.c)(c.g,!1),fluidGrow:Object(u.c)(c.g,!1),height:Object(u.c)(c.p),left:Object(u.c)(c.g,!1),right:Object(u.c)(c.g,!1),rounded:Object(u.c)(c.j,!1),sizes:Object(u.c)(c.f),src:Object(u.c)(c.u),srcset:Object(u.c)(c.f),thumbnail:Object(u.c)(c.g,!1),width:Object(u.c)(c.p)},a.Q),h=Object(r.c)({name:a.Q,functional:!0,props:b,render:function(t,n){var e,r=n.props,a=n.data,c=r.alt,u=r.src,b=r.block,h=r.fluidGrow,f=r.rounded,w=Object(p.b)(r.width)||null,v=Object(p.b)(r.height)||null,j=null,O=Object(s.b)(r.srcset).filter(l.a).join(","),x=Object(s.b)(r.sizes).filter(l.a).join(",");return r.blank&&(!v&&w?v=w:!w&&v&&(w=v),w||v||(w=1,v=1),u=function(t,n,e){var r=encodeURIComponent(d.replace("%{w}",Object(g.g)(t)).replace("%{h}",Object(g.g)(n)).replace("%{f}",e));return"data:image/svg+xml;charset=UTF-8,".concat(r)}(w,v,r.blankColor||"transparent"),O=null,x=null),r.left?j="float-left":r.right?j="float-right":r.center&&(j="mx-auto",b=!0),t("img",Object(i.a)(a,{attrs:{src:u,alt:c,width:w?Object(g.g)(w):null,height:v?Object(g.g)(v):null,srcset:O||null,sizes:x||null},class:(e={"img-thumbnail":r.thumbnail,"img-fluid":r.fluid||h,"w-100":h,rounded:""===f||!0===f},m(e,"rounded-".concat(f),Object(o.n)(f)&&""!==f),m(e,j,j),m(e,"d-block",b),e)}))}})},253:function(t,n,e){var r=e(399);"string"==typeof r&&(r=[[t.i,r,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};e(29)(r,i);r.locals&&(t.exports=r.locals)},398:function(t,n,e){"use strict";e(253)},399:function(t,n,e){(n=e(28)(!1)).push([t.i,".misc-wrapper {\n  display: flex;\n  flex-basis: 100%;\n  min-height: 100vh;\n  width: 100%;\n  align-items: center;\n  justify-content: center;\n}\n.misc-wrapper .misc-inner {\n  position: relative;\n  max-width: 750px;\n}\n.misc-wrapper .brand-logo {\n  display: flex;\n  justify-content: center;\n  position: absolute;\n  top: 2rem;\n}\n[dir] .misc-wrapper .brand-logo {\n  margin: 0;\n}\n[dir=ltr] .misc-wrapper .brand-logo {\n  left: 2rem;\n}\n[dir=rtl] .misc-wrapper .brand-logo {\n  right: 2rem;\n}\n.misc-wrapper .brand-logo .brand-text {\n  font-weight: 600;\n}\n@media (max-height: 625px) {\n[dir] .misc-wrapper .misc-inner {\n    margin-top: 4rem;\n}\n}\n.brand-logo svg {\n  height: 28px;\n  width: 40.95px;\n}",""]),t.exports=n},438:function(t,n,e){"use strict";e.r(n);var r=e(39),i=e(85),a=e(220),c=e(172),s=e(24),l={components:{VuexyLogo:c.a,BLink:r.a,BButton:i.a,BImg:a.a},data:function(){return{downImg:"/img/pages/error.svg",app:s.a.state.appConfig.app}},computed:{imgUrl:function(){return"dark"===s.a.state.appConfig.layout.skin?(this.downImg="/img/pages/error-dark.svg",this.downImg):this.downImg}}},o=(e(398),e(11)),p=Object(o.a)(l,(function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("div",{staticClass:"misc-wrapper"},[e("b-link",{staticClass:"brand-logo"},[e("vuexy-logo"),t._v(" "),e("h2",{staticClass:"brand-text text-primary ml-1"},[t._v("\n        "+t._s(t.app.name)+" Versi "+t._s(t.app.version)+"\n      ")])],1),t._v(" "),e("div",{staticClass:"misc-inner p-2 p-sm-3"},[e("div",{staticClass:"w-100 text-center"},[e("h2",{staticClass:"mb-1"},[t._v("\n          Halaman tidak ditemukan 🕵🏻‍♀️\n        ")]),t._v(" "),e("p",{staticClass:"mb-2"},[t._v("\n          Oops! 😖 Alamat yang diminta tidak ditemukan di aplikasi ini.\n        ")]),t._v(" "),e("b-button",{staticClass:"mb-2 btn-sm-block",attrs:{variant:"primary",to:{path:"/"}}},[t._v("\n          Kembali ke Beranda\n        ")]),t._v(" "),e("b-img",{attrs:{fluid:"",src:t.imgUrl,alt:"Error page"}})],1)])],1)}),[],!1,null,null,null);n.default=p.exports}}]);