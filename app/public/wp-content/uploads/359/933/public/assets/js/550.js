advanced_ads_ready=function(){var e,t=[],n="object"==typeof document&&document,d=n&&n.documentElement.doScroll,o="DOMContentLoaded",a=n&&(d?/^loaded|^c/:/^loaded|^i|^c/).test(n.readyState);return!a&&n&&(e=function(){for(n.removeEventListener(o,e),window.removeEventListener("load",e),a=1;e=t.shift();)e()},n.addEventListener(o,e),window.addEventListener("load",e)),function(e){a?setTimeout(e,0):t.push(e)}}();