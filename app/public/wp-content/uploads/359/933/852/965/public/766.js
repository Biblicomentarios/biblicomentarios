advanced_ads_check_adblocker=function(t){function e(t){(window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||function(t){return setTimeout(t,16)}).call(window,t)}var n=[],a=null;return e(function(){var t=document.createElement("div");t.innerHTML="&nbsp;",t.setAttribute("class","ad_unit ad-unit text-ad text_ad pub_300x250"),t.setAttribute("style","width: 1px !important; height: 1px !important; position: absolute !important; left: 0px !important; top: 0px !important; overflow: hidden !important;"),document.body.appendChild(t),e(function(){var e=window.getComputedStyle&&window.getComputedStyle(t),o=e&&e.getPropertyValue("-moz-binding");a=e&&"none"===e.getPropertyValue("display")||"string"==typeof o&&-1!==o.indexOf("about:");for(var i=0;i<n.length;i++)n[i](a);n=[]})}),function(t){if(null===a)return void n.push(t);t(a)}}(),function(){var t=function(t,e){this.name=t,this.UID=e,this.analyticsObject=null;var n=this,a={hitType:"event",eventCategory:"Advanced Ads",eventAction:"AdBlock",eventLabel:"Yes",nonInteraction:!0,transport:"beacon"};this.analyticsObject="string"==typeof GoogleAnalyticsObject&&"function"==typeof window[GoogleAnalyticsObject]&&window[GoogleAnalyticsObject],!1===this.analyticsObject?(!function(t,e,n,a,o,i,d){t.GoogleAnalyticsObject=o,t[o]=t[o]||function(){(t[o].q=t[o].q||[]).push(arguments)},t[o].l=1*new Date,i=e.createElement(n),d=e.getElementsByTagName(n)[0],i.async=1,i.src="https://www.google-analytics.com/analytics.js",d.parentNode.insertBefore(i,d)}(window,document,"script",0,"_advads_ga"),_advads_ga("create",n.UID,"auto",this.name),advanced_ads_ga_anonymIP&&_advads_ga("set","anonymizeIp",!0),_advads_ga(n.name+".send",a)):(window.console&&window.console.log("Advanced Ads Analytics >> using other's variable named `"+GoogleAnalyticsObject+"`"),window[GoogleAnalyticsObject]("create",n.UID,"auto",this.name),window[GoogleAnalyticsObject]("set","anonymizeIp",!0),window[GoogleAnalyticsObject](n.name+".send",a))};advanced_ads_check_adblocker(function(e){e&&"string"==typeof advanced_ads_ga_UID&&advanced_ads_ga_UID&&new t("advadsTracker",advanced_ads_ga_UID)})}();