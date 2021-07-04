var realCategoryLibrary_options;!function(){"use strict";var e,t={9852:function(e,t,n){n.d(t,{l:function(){return E}});var r=n(5744),o=n(8644),i=n(4614),a=n(4799),s=n(3804),c=n(3189),u=n(2762),l=(0,o.Pi)((function(e){var t=e.feature,n=(0,i.m)().optionStore.others,r=n.isPro,o=n.proUrl,l=(0,s.useCallback)((function(){window.open("".concat(o,"&feature=").concat(t))}),[]);return r?null:React.createElement(u.Z,{icon:React.createElement(c.Z,null),color:"#2db7f5",style:{cursor:"pointer"},onClick:l},(0,a.__)("Unlock feature"))})),p=n(31),d=n(5450),f=n.n(d),h=n(5481),m=n(5415),b=n(6428),y=n(7452),v=(0,o.Pi)((function(e){var t=e.name,n=(0,i.m)().optionStore,r=n.others,o=r.postTypes,c=r.isPro,u=r.pluginCptUi,d=o[t],v=d.label,g=d.link,w=d.available,R=d.active,x=d.fastMode,Z=(0,s.useCallback)(function(){var e=(0,p.Z)(f().mark((function e(r){return f().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,n.updatePostTypeOptions(t,{active:r});case 2:h.ZP.success(r?(0,a.__)("Category tree enabled for %s.",v):(0,a.__)("Category tree disabled for %s.",v));case 3:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),[n,v]),_=(0,s.useCallback)(function(){var e=(0,p.Z)(f().mark((function e(r){return f().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,n.updatePostTypeOptions(t,{fastMode:r});case 2:h.ZP.success(r?(0,a.__)("Pagination without reloading the page enabled for %s.",v):(0,a.__)("Pagination without reloading the page disabled for %s.",v));case 3:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),[n,v]),S=(0,s.useCallback)((0,p.Z)(f().mark((function e(){return f().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:u.active?window.location.href=u.manageTaxonomiesUrl:m.Z.confirm({cancelText:(0,a.__)("Cancel"),okText:"Continue",title:(0,a.__)("Custom Post Type UI"),content:(0,a.__)("To create custom taxonomies we highly recommend to use the 3rd party plugin Custom Post Type UI. Would you like to install and activate it now?"),onOk:function(){var e=(0,p.Z)(f().mark((function e(){return f().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,n.installAndActivateCustomPostTypeUI();case 2:window.location.href=u.manageTaxonomiesUrl;case 3:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}()});case 1:case"end":return e.stop()}}),e)}))),[n,u]);return React.createElement("tr",null,React.createElement("td",null,React.createElement("strong",null,v)," •"," ",React.createElement("a",{href:g,target:"_blank",rel:"noreferrer"},(0,a.__)("Open"))),React.createElement("td",null,"post"===t||c?w?React.createElement(b.Z,{checked:R,onChange:Z}):React.createElement(y.Z,{transitionName:null,placement:"top",title:(0,a.__)("There are no taxonomies available for this post type. You need to register your own taxonomy so that you can organize your content into its categories.")},React.createElement("button",{className:"button",onClick:S},(0,a.__)("Create custom taxonomy"))):React.createElement(l,{feature:"options-active"})),React.createElement("td",null,React.createElement(b.Z,{checked:R&&x,disabled:!R||!c,onChange:_})))})),g=(0,o.Pi)((function(){var e=(0,i.m)().optionStore.others.postTypes;return React.createElement("table",{className:"wp-list-table widefat fixed striped table-view-list"},React.createElement("thead",null,React.createElement("tr",null,React.createElement("td",null,(0,a.__)("Post type")),React.createElement("td",{width:250,align:"right"},(0,a.__)("Show category tree")),React.createElement("td",{width:350,align:"right"},(0,a.__)("Pagination without page reload")," ",React.createElement(l,{feature:"options-fast-mode"})))),React.createElement("tbody",null,Object.keys(e).map((function(e){return React.createElement(v,{key:e,name:e})})),React.createElement("tr",null,React.createElement("td",null,React.createElement("strong",null,(0,a.__)("Media"))),React.createElement("td",{colSpan:2},React.createElement("a",{href:"https://devowl.io/go/real-category-management?source=rcm-lite&feature=options-rml",target:"_blank",rel:"noreferrer"},(0,a.__)("Use Real Media Library to enable folder management"))))))})),w=devowlWp_realProductManagerWpClient,R=n(994),x=n(3142),Z=n(2491),_=n(1984),S=(0,o.Pi)((function(e){var t=e.showHeader,n=void 0===t||t,o=(0,w.useStores)().pluginUpdateStore,c=o.busy,u=o.pluginUpdate,l=(0,i.m)().optionStore,p=l.slug,d=l.others,f=d.isPro,h=d.showLicenseFormImmediate,m=l.publicUrl;(0,s.useEffect)((function(){return o.setFromSlug(p),function(){o.hideLicense()}}),[]);var b=(0,s.useCallback)((function(){l.setShowLicenseFormImmediate(!1)}),[l]),y=(0,s.useCallback)((function(){l.setShowLicenseFormImmediate(!1),u.skip()}),[u,l]);return(0,s.useEffect)((function(){h&&null!=u&&u.hasInteractedWithFormOnce&&y()}),[u,h,y]),c||!u?React.createElement(r.Z,{spinning:!0}):React.createElement(React.Fragment,null,h&&React.createElement("div",{style:{maxWidth:650,textAlign:"center",margin:"0 auto 20px"}},n&&React.createElement(R.C,{src:"".concat(m,"images/logos/real-category-library.svg"),shape:"square",size:130,style:{backgroundColor:"white",padding:25,borderRadius:999}}),!f&&React.createElement("p",{style:{fontSize:15}},(0,a._i)((0,a.__)("Before we start organizing your categories, you can {{strong}}obtain your free license to enjoy all the benefits{{/strong}} of the free version of Real Category Management. Get started now!"),{strong:React.createElement("strong",null)}))),React.createElement(x.Z,{title:f||u.isLicensed?(0,a.__)("License activation"):(0,a.__)("Get your free license")},React.createElement(Z.Z,{direction:"vertical",size:"large"},!u.isLicensed&&React.createElement("p",{className:"description"},f?(0,a.__)("Activate your Real Category Management PRO license to receive regular updates and support."):(0,a._i)((0,a.__)("To use all advantages of Real Category Management {{strong}}you need a free license{{/strong}}. After license activation you will receive answers to support requests and announcements in your plugin (e.g. also notices for discount actions of the PRO version)."),{strong:React.createElement("strong",null)})),React.createElement(w.PluginUpdateEmbed,{formProps:{onSave:b,onFailure:h&&!f?y:void 0,footer:React.createElement(_.Z.Item,{style:{margin:"25px 0 0",textAlign:h?"center":void 0}},React.createElement("input",{type:"submit",className:"button button-primary",value:h?f?(0,a.__)("Activate license & continue"):(0,a.__)("Activate free license & Continue"):(0,a.__)("Save")}))},listProps:{onDeactivate:b}}))),h&&React.createElement("div",{style:{textAlign:"center",marginTop:20}},React.createElement("a",{className:"button-link",onClick:y},f?(0,a.__)("Continue without regular updates and without any support"):(0,a.__)("Continue without any support and without e.g. discount announcements"))))})),k=(0,o.Pi)((function(){return React.createElement("div",{style:{maxWidth:800,margin:"auto",padding:"20px 0"}},React.createElement(w.Provider,null,React.createElement(S,null)))})),E=(0,o.Pi)((function(){var e=(0,i.m)().optionStore,t=e.busySettings,n=e.others,o=n.licenseActivationLink;return n.showLicenseFormImmediate?React.createElement(k,null):React.createElement(React.Fragment,null,React.createElement("h1",{className:"wp-heading-inline",style:{marginBottom:10}},(0,a.__)("Category Management")),React.createElement("a",{className:"page-title-action",href:o},(0,a.__)("License settings")),React.createElement(r.Z,{spinning:t},React.createElement("p",{className:"description",style:{marginBottom:15}},(0,a.__)("You can decide for which post types the additional features of Real Category Management should apply.")),React.createElement(g,null)))}))},6983:function(e,t,n){n.r(t),n.d(t,{locationRestHierarchyPut:function(){return v.FT},locationRestNoticeLiteDelete:function(){return v.wY},locationRestOptionsPostTypePatch:function(){return v.Vh},locationRestPostsBulkMovePut:function(){return v.cg},locationRestTermsDelete:function(){return v.jw},locationRestTermsPost:function(){return v.If},locationRestTermsPut:function(){return v.GK},locationRestTreeGet:function(){return v.NG},OptionStore:function(){return g.aZ},RootStore:function(){return g.My},TreeStore:function(){return g.RT},useStores:function(){return g.mZ}});var r=n(499),o=n(31),i=n(5450),a=n.n(i),s=n(7196),c=n(4614),u=(n(1463),n(3609)),l=n.n(u),p=n(8685),d=n(3371),f=n(5481),h=n(4799),m=n(9852),b={};for(var y in p)"default"!==y&&(b[y]=function(e){return p[e]}.bind(0,y));n.d(t,b);var v=n(8488),g=n(5187);null===p.handleCorrupRestApi||void 0===p.handleCorrupRestApi||(0,p.handleCorrupRestApi)((0,r.Z)({},c.M.get.optionStore.restNamespace,(0,o.Z)(a().mark((function e(){return a().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,h.WY)({location:{path:"/plugin"}});case 2:case"end":return e.stop()}}),e)}))))),d.ZP.config({prefixCls:"rcl-antd"}),f.ZP.config({top:50});var w=document.getElementById("".concat(c.M.get.optionStore.slug,"-component"));l()((function(){w&&(0,s.render)(React.createElement(d.ZP,{prefixCls:"rcl-antd"},React.createElement(c.M.StoreProvider,null,React.createElement(m.l,null))),w)})),l()("link#dark_mode-css").length&&l()("body").addClass("aiot-wp-dark-mode")},5187:function(e,t,n){n.d(t,{My:function(){return r.M},mZ:function(){return r.m},aZ:function(){return o.a},RT:function(){return i.R}});var r=n(4614),o=n(3684),i=n(7311)},3684:function(e,t,n){n.d(t,{a:function(){return S}});var r,o,i,a=n(2835),s=n(2224),c=n(5442),u=n(5675),l=n(239),p=n(9728),d=n(8684),f=n(4450),h=n(5450),m=n.n(h),b=n(2965),y=n(8685),v=n(3536),g=n(4799),w=n(8981),R=wp,x=n.n(R),Z=n(3609),_=n.n(Z),S=(r=function(e){(0,p.Z)(n,e);var t=(0,d.Z)(n);function n(e){var r;return(0,c.Z)(this,n),r=t.call(this),(0,s.Z)(r,"busySettings",o,(0,l.Z)(r)),(0,s.Z)(r,"others",i,(0,l.Z)(r)),r.pureSlug=void 0,r.pureSlugCamelCased=void 0,r.rootStore=void 0,r.updatePostTypeOptions=(0,b.flow)(m().mark((function e(t,n){var r,o,i;return m().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return r=n.active,o=n.fastMode,this.busySettings=!0,e.prev=2,e.next=5,(0,g.WY)({location:w.V,params:{post_type:t},request:(0,a.Z)((0,a.Z)({},void 0===r?{}:{active:r}),void 0===o?{}:{fastMode:o})});case 5:return i=e.sent,void 0!==r&&(this.others.postTypes[t].active=r),void 0!==o&&(this.others.postTypes[t].fastMode=o),e.abrupt("return",i);case 11:throw e.prev=11,e.t0=e.catch(2),console.log(e.t0),e.t0;case 15:return e.prev=15,this.busySettings=!1,e.finish(15);case 18:case"end":return e.stop()}}),e,this,[[2,11,15,18]])}))),r.installAndActivateCustomPostTypeUI=(0,b.flow)(m().mark((function e(){var t,n,r,o,i,a,s,c;return m().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.busySettings=!0,n=this.others,r=n.pluginCptUi,o=n.installPluginNonce,i=r.installed,a=r.installUrl,s=r.activateUrl,c=null===x()||void 0===x()||null===(t=x().ajax)||void 0===t?void 0:t.send,!i){e.next=16;break}return e.prev=5,e.next=8,_().get(s).promise();case 8:r.active=!0,e.next=14;break;case 11:e.prev=11,e.t0=e.catch(5),window.location.href=s;case 14:e.next=31;break;case 16:if(!c){e.next=30;break}return e.prev=17,e.next=20,c({data:{action:"install-plugin",slug:"custom-post-type-ui",_ajax_nonce:o}});case 20:return r.installed=!0,e.next=23,this.installAndActivateCustomPostTypeUI();case 23:e.next=28;break;case 25:e.prev=25,e.t1=e.catch(17),window.location.href=a;case 28:e.next=31;break;case 30:window.location.href=a;case 31:case"end":return e.stop()}}),e,this,[[5,11],[17,25]])}))),r.installAndActivateRealCustomPostOrder=(0,b.flow)(m().mark((function e(){var t,n,r,o,i,a,s,c;return m().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.busySettings=!0,n=this.others,r=n.pluginRcpo,o=n.installPluginNonce,i=r.installed,a=r.installUrl,s=r.activateUrl,c=null===x()||void 0===x()||null===(t=x().ajax)||void 0===t?void 0:t.send,!i){e.next=16;break}return e.prev=5,e.next=8,_().get(s).promise();case 8:r.active=!0,e.next=14;break;case 11:e.prev=11,e.t0=e.catch(5),window.location.href=s;case 14:e.next=31;break;case 16:if(!c){e.next=30;break}return e.prev=17,e.next=20,c({data:{action:"install-plugin",slug:"real-custom-post-order",_ajax_nonce:o}});case 20:return r.installed=!0,e.next=23,this.installAndActivateCustomPostTypeUI();case 23:e.next=28;break;case 25:e.prev=25,e.t1=e.catch(17),window.location.href=a;case 28:e.next=31;break;case 30:window.location.href=a;case 31:case"end":return e.stop()}}),e,this,[[5,11],[17,25]])}))),r.rootStore=e,r.pureSlug=y.BaseOptions.getPureSlug({NODE_ENV:"production",env:"production",rootSlug:"devowl-wp",slug:"real-category-library",PLUGIN_CTX:"lite",ANTD_PREFIX:"rcl-antd"}),r.pureSlugCamelCased=y.BaseOptions.getPureSlug({NODE_ENV:"production",env:"production",rootSlug:"devowl-wp",slug:"real-category-library",PLUGIN_CTX:"lite",ANTD_PREFIX:"rcl-antd"},!0),(0,b.runInAction)((function(){return Object.assign((0,l.Z)(r),window[r.pureSlugCamelCased])})),r}return(0,u.Z)(n,[{key:"isRatable",get:function(){return(0,v.isRatable)(this.slug)}},{key:"setTaxnow",value:function(e){this.others.taxnow=e}},{key:"setShowLicenseFormImmediate",value:function(e){this.others.showLicenseFormImmediate=e}}]),n}(y.BaseOptions),o=(0,f.Z)(r.prototype,"busySettings",[b.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),i=(0,f.Z)(r.prototype,"others",[b.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),(0,f.Z)(r.prototype,"setTaxnow",[b.action],Object.getOwnPropertyDescriptor(r.prototype,"setTaxnow"),r.prototype),(0,f.Z)(r.prototype,"setShowLicenseFormImmediate",[b.action],Object.getOwnPropertyDescriptor(r.prototype,"setShowLicenseFormImmediate"),r.prototype),r)},4614:function(e,t,n){n.d(t,{M:function(){return u},m:function(){return l}});var r=n(5442),o=n(5675),i=n(2965),a=n(8685),s=n(3684),c=n(7311);(0,i.configure)({enforceActions:"always"});var u=function(){function e(){(0,r.Z)(this,e),this.optionStore=void 0,this.treeStore=void 0,this.contextMemo=void 0,this.optionStore=new s.a(this),this.treeStore=new c.R(this)}return(0,o.Z)(e,[{key:"context",get:function(){return this.contextMemo?this.contextMemo:this.contextMemo=(0,a.createContextFactory)(this)}}],[{key:"StoreProvider",get:function(){return e.get.context.StoreProvider}},{key:"get",get:function(){return e.me?e.me:e.me=new e}}]),e}();u.me=void 0;var l=function(){return u.get.context.useStores()}},7311:function(e,t,n){n.d(t,{R:function(){return Y}});var r,o,i,a,s,c,u,l,p,d,f,h,m,b,y,v,g,w,R,x,Z,_,S,k,E,P,C,T=n(4097),O=n(2224),I=n(5442),N=n(5675),M=n(4450),z=n(5450),A=n.n(z),j=n(2965),F=n(4799),L=n(1072),U=n(9482),D=n(3609),q=n.n(D),$=(r=function(){function e(t,n){var r=this;(0,I.Z)(this,e),(0,O.Z)(this,"id",o,this),(0,O.Z)(this,"hash",i,this),(0,O.Z)(this,"className",a,this),(0,O.Z)(this,"icon",s,this),(0,O.Z)(this,"iconActive",c,this),(0,O.Z)(this,"childNodes",u,this),(0,O.Z)(this,"title",l,this),(0,O.Z)(this,"count",p,this),(0,O.Z)(this,"isTreeLinkDisabled",d,this),(0,O.Z)(this,"selected",f,this),(0,O.Z)(this,"$busy",h,this),(0,O.Z)(this,"$droppable",m,this),(0,O.Z)(this,"$visible",b,this),(0,O.Z)(this,"$rename",y,this),(0,O.Z)(this,"$create",v,this),(0,O.Z)(this,"properties",g,this),(0,O.Z)(this,"isQueried",w,this),(0,O.Z)(this,"parent",R,this),this.treeStore=void 0,this.setTitle=(0,j.flow)(A().mark((function e(t){var n;return A().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return this.$busy=!0,e.prev=1,e.next=4,(0,F.WY)({location:L.G,params:{id:+this.id},request:{name:t,taxonomy:this.properties.taxonomy}});case 4:return n=e.sent,this.title=t,this.properties=q().extend({},this.properties,n),e.abrupt("return",n);case 8:return e.prev=8,this.$busy=!1,e.finish(8);case 11:case"end":return e.stop()}}),e,this,[[1,,8,11]])}))),this.trash=(0,j.flow)(A().mark((function e(){return A().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return this.$busy=!0,e.prev=1,e.next=4,(0,F.WY)({location:U.j,params:{id:+this.id,taxonomy:this.properties.taxonomy}});case 4:this.$visible=!1;case 5:return e.prev=5,this.$busy=!1,e.finish(5);case 8:case"end":return e.stop()}}),e,this,[[1,,5,8]])}))),this.treeStore=n,(0,j.runInAction)((function(){(0,j.set)(r,t),r.id&&n.refs.set(r.id,r)}))}return(0,N.Z)(e,[{key:"overwriteCompletelyFromResponse",value:function(e){var t=this;q().each(e,(function(e,n){return(0,j.set)(t,e,n)}))}},{key:"addChildNode",value:function(e){this.childNodes.push(e)}},{key:"setSelected",value:function(e){this.selected!==e&&(this.selected=e,e&&this.treeStore.setSelected(this))}},{key:"setBusy",value:function(e){this.$busy=e}},{key:"setRename",value:function(e){this.$rename=e}},{key:"setCreate",value:function(e){this.$create=e}}],[{key:"mapFromRestEndpoint",value:function(t){var n=t.term_id,r=t.name,o=t.count,i=t.childNodes,a=(0,T.Z)(t,["term_id","name","count","childNodes"]);return new e({id:n,title:r,count:o,icon:"folder",iconActive:"folder-open",childNodes:i?i.map(e.mapFromRestEndpoint.bind(this)):[],properties:a},this)}}]),e}(),o=(0,M.Z)(r.prototype,"id",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),i=(0,M.Z)(r.prototype,"hash",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return""}}),a=(0,M.Z)(r.prototype,"className",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return""}}),s=(0,M.Z)(r.prototype,"icon",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return""}}),c=(0,M.Z)(r.prototype,"iconActive",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return""}}),u=(0,M.Z)(r.prototype,"childNodes",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return[]}}),l=(0,M.Z)(r.prototype,"title",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return""}}),p=(0,M.Z)(r.prototype,"count",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return 0}}),d=(0,M.Z)(r.prototype,"isTreeLinkDisabled",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),f=(0,M.Z)(r.prototype,"selected",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),h=(0,M.Z)(r.prototype,"$busy",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),m=(0,M.Z)(r.prototype,"$droppable",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!0}}),b=(0,M.Z)(r.prototype,"$visible",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!0}}),y=(0,M.Z)(r.prototype,"$rename",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),v=(0,M.Z)(r.prototype,"$create",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),g=(0,M.Z)(r.prototype,"properties",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),w=(0,M.Z)(r.prototype,"isQueried",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!0}}),R=(0,M.Z)(r.prototype,"parent",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),(0,M.Z)(r.prototype,"overwriteCompletelyFromResponse",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"overwriteCompletelyFromResponse"),r.prototype),(0,M.Z)(r.prototype,"addChildNode",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"addChildNode"),r.prototype),(0,M.Z)(r.prototype,"setSelected",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"setSelected"),r.prototype),(0,M.Z)(r.prototype,"setBusy",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"setBusy"),r.prototype),(0,M.Z)(r.prototype,"setRename",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"setRename"),r.prototype),(0,M.Z)(r.prototype,"setCreate",[j.action],Object.getOwnPropertyDescriptor(r.prototype,"setCreate"),r.prototype),r),W=n(714),V=n(9559),G=n(134),Y=(C=P=function(){function e(t){var n=this;(0,I.Z)(this,e),(0,O.Z)(this,"staticTree",Z,this),(0,O.Z)(this,"tree",_,this),(0,O.Z)(this,"selected",S,this),(0,O.Z)(this,"busy",k,this),(0,O.Z)(this,"createRoot",E,this),this.refs=new Map,this.rootStore=void 0,this.fetchTree=(0,j.flow)(A().mark((function e(t,n){var r,o,i,a,s,c;return A().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.busy=!0,r=this.rootStore.optionStore.others,o=r.taxnow,i=r.typenow,o&&i){e.next=4;break}return e.abrupt("return");case 4:return e.next=6,(0,F.WY)({location:W.N,params:Object.assign({currentUrl:window.location.href,remember:!1,taxonomy:o,type:i},t)});case 6:a=e.sent,s=a.selectedId,c=a.tree,this.tree=c.map($.mapFromRestEndpoint.bind(this)),this.busy=!1,this.byId(s,!1).setSelected(!0),null==n||n(a);case 12:case"end":return e.stop()}}),e,this)}))),this.persist=(0,j.flow)(A().mark((function e(t){var n,r,o;return A().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,F.WY)({location:V.I,request:t});case 2:return n=e.sent,r=$.mapFromRestEndpoint.apply(this,[{category_name:n.category_name,childNodes:[],count:n.count,editableSlug:n.editableSlug,name:n.name,post_type:n.post_type,queryArgs:n.queryArgs,taxonomy:n.taxonomy,term_id:n.term_id}]),0===(o=t.parent)?this.tree.push(r):this.byId(o).addChildNode(r),e.abrupt("return",r);case 7:case"end":return e.stop()}}),e,this)}))),this.sort=(0,j.flow)(A().mark((function e(t){var n,r,o,i,a,s,c,u,l,p,d,f,h;return A().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(n=t.id,r=t.oldIndex,o=t.newIndex,i=t.parentFromId,a=t.parentToId,s=t.nextId,c=(0,T.Z)(t,["id","oldIndex","newIndex","parentFromId","parentToId","nextId"]),u=0===i?this.tree:this.byId(i).childNodes,l=0===a?this.tree:this.byId(a).childNodes,p=u[r],u.splice(r,1),l.splice(o,0,p),c.request){e.next=8;break}return e.abrupt("return",!0);case 8:return d=this.rootStore.optionStore.others,f=d.typenow,h=d.taxnow,e.prev=9,e.next=12,(0,F.WY)({location:G.F,params:{id:n},request:{nextId:s,parent:a,type:f,taxonomy:h}});case 12:return e.abrupt("return",!0);case 15:return e.prev=15,e.t0=e.catch(9),e.next=19,this.sort({id:n,oldIndex:o,newIndex:r,parentFromId:a,parentToId:i,nextId:s,request:!1});case 19:throw e.t0;case 20:case"end":return e.stop()}}),e,this,[[9,15]])}))),this.rootStore=t,(0,j.reaction)((function(){return n.rootStore.optionStore.others.taxnow}),(function(){return n.fetchTree({remember:!0})})),setTimeout(this.init.bind(this))}return(0,N.Z)(e,[{key:"selectedId",get:function(){var e;return null===(e=this.selected)||void 0===e?void 0:e.id}},{key:"setSelected",value:function(e){this.selected&&(this.selected.selected=!1),this.selected=e}},{key:"setCreateRoot",value:function(e){this.createRoot=e}},{key:"init",value:function(){this.staticTree.push(new $({id:"ALL",title:(0,F.__)("All posts"),icon:"copy",count:this.rootStore.optionStore.others.allPostCnt},this)),this.rootStore.optionStore.others.screenSettings.isActive&&this.fetchTree()}},{key:"byId",value:function(e){var t=!(arguments.length>1&&void 0!==arguments[1])||arguments[1],n=this.refs.get(e);if(!(t&&this.staticTree.indexOf(n)>-1))return n}}]),e}(),P.ID_ALL="ALL",x=C,Z=(0,M.Z)(x.prototype,"staticTree",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return[]}}),_=(0,M.Z)(x.prototype,"tree",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return[]}}),S=(0,M.Z)(x.prototype,"selected",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),k=(0,M.Z)(x.prototype,"busy",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:function(){return!1}}),E=(0,M.Z)(x.prototype,"createRoot",[j.observable],{configurable:!0,enumerable:!0,writable:!0,initializer:null}),(0,M.Z)(x.prototype,"selectedId",[j.computed],Object.getOwnPropertyDescriptor(x.prototype,"selectedId"),x.prototype),(0,M.Z)(x.prototype,"setSelected",[j.action],Object.getOwnPropertyDescriptor(x.prototype,"setSelected"),x.prototype),(0,M.Z)(x.prototype,"setCreateRoot",[j.action],Object.getOwnPropertyDescriptor(x.prototype,"setCreateRoot"),x.prototype),(0,M.Z)(x.prototype,"init",[j.action],Object.getOwnPropertyDescriptor(x.prototype,"init"),x.prototype),x)},4799:function(e,t,n){n.d(t,{WY:function(){return c},__:function(){return u},_i:function(){return l}});var r=n(5442),o=n(5675),i=n(8685),a=n(4614),s=function(){function e(){(0,r.Z)(this,e),this.requestMemo=void 0,this.localizationMemo=void 0}return(0,o.Z)(e,[{key:"request",get:function(){return this.requestMemo?this.requestMemo:this.requestMemo=(0,i.createRequestFactory)(a.M.get.optionStore)}},{key:"localization",get:function(){return this.localizationMemo?this.localizationMemo:this.localizationMemo=(0,i.createLocalizationFactory)(a.M.get.optionStore.pureSlug)}}],[{key:"get",get:function(){return e.me?e.me:e.me=new e}}]),e}();s.me=void 0;var c=function(){var e;return(e=s.get.request).request.apply(e,arguments)},u=function(){var e;return(e=s.get.localization).__.apply(e,arguments)},l=function(){var e;return(e=s.get.localization)._i.apply(e,arguments)}},134:function(e,t,n){n.d(t,{F:function(){return r}});var r={path:"/hierarchy/:id",method:n(8685).RouteHttpVerb.PUT}},8488:function(e,t,n){n.d(t,{cg:function(){return r.c},GK:function(){return o.G},jw:function(){return i.j},FT:function(){return a.F},If:function(){return s.I},NG:function(){return c.N},wY:function(){return u.w},Vh:function(){return l.V}});var r=n(2710),o=n(1072),i=n(9482),a=n(134),s=n(9559),c=n(714),u=n(637),l=n(8981)},637:function(e,t,n){n.d(t,{w:function(){return r}});var r={path:"/notice/lite",method:n(8685).RouteHttpVerb.DELETE}},8981:function(e,t,n){n.d(t,{V:function(){return r}});var r={path:"/options/:post_type",method:n(8685).RouteHttpVerb.PATCH}},2710:function(e,t,n){n.d(t,{c:function(){return r}});var r={path:"/posts/bulk/move",method:n(8685).RouteHttpVerb.PUT}},9482:function(e,t,n){n.d(t,{j:function(){return r}});var r={path:"/terms/:id",method:n(8685).RouteHttpVerb.DELETE}},9559:function(e,t,n){n.d(t,{I:function(){return r}});var r={path:"/terms",method:n(8685).RouteHttpVerb.POST}},1072:function(e,t,n){n.d(t,{G:function(){return r}});var r={path:"/terms/:id",method:n(8685).RouteHttpVerb.PUT}},714:function(e,t,n){n.d(t,{N:function(){return r}});var r={path:"/tree",method:n(8685).RouteHttpVerb.GET}},1463:function(){},3804:function(e){e.exports=React},7196:function(e){e.exports=ReactDOM},3536:function(e){e.exports=devowlWp_realUtils},8685:function(e){e.exports=devowlWp_utils},3609:function(e){e.exports=jQuery},2965:function(e){e.exports=mobx}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var i=n[e]={id:e,loaded:!1,exports:{}};return t[e](i,i.exports,r),i.loaded=!0,i.exports}r.m=t,e=[],r.O=function(t,n,o,i){if(!n){var a=1/0;for(u=0;u<e.length;u++){n=e[u][0],o=e[u][1],i=e[u][2];for(var s=!0,c=0;c<n.length;c++)(!1&i||a>=i)&&Object.keys(r.O).every((function(e){return r.O[e](n[c])}))?n.splice(c--,1):(s=!1,i<a&&(a=i));s&&(e.splice(u--,1),t=o())}return t}i=i||0;for(var u=e.length;u>0&&e[u-1][2]>i;u--)e[u]=e[u-1];e[u]=[n,o,i]},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.nmd=function(e){return e.paths=[],e.children||(e.children=[]),e},function(){var e={798:0};r.O.j=function(t){return 0===e[t]};var t=function(t,n){var o,i,a=n[0],s=n[1],c=n[2],u=0;for(o in s)r.o(s,o)&&(r.m[o]=s[o]);if(c)var l=c(r);for(t&&t(n);u<a.length;u++)i=a[u],r.o(e,i)&&e[i]&&e[i][0](),e[a[u]]=0;return r.O(l)},n=self.webpackChunkrealCategoryLibrary_name_=self.webpackChunkrealCategoryLibrary_name_||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var o=r.O(void 0,[960],(function(){return r(6983)}));o=r.O(o),realCategoryLibrary_options=o}();
//# sourceMappingURL=options.lite.js.map