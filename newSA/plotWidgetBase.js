define(["components/flight/lib/component","components/flight/lib/compose","js/components/widgetOptions","js/components/plot.xy","js/components/plot.xyz","js/mixins/api/plotAPI","jquery","components/nvd3/lib/d3.v2.min","components/nvd3/nv.d3.min","components/nvd3/nv.d3","js/components/rickshaw/rickshaw"],function(a,b,c,d,e,f,g){function h(){this.rendererToDomId={},this.renderer={},this.paddingValue=20,this.allWidgets={},this.widgetTypes=["plot.xy","plot.xyz"],b.mixin(this,[f]),this.harvestWidgetsFromPage=function(){"undefined"==typeof this.rendererToDomId&&(this.rendererToDomId={});var a=this,b=0;g.each(this.widgetTypes,function(f,h){g('[data-viz-type="'+h+'"]').each(function(){var f="undefined"!=typeof g(this).attr("id")?g(this).attr("id"):"w10nWidget_"+b;g(this).attr("id",f).addClass("w10nWidget");var i=this;switch(console.log("type is",h),h){case"plot.xy":a.rendererToDomId[f]={};var j=g(i).attr("xdata"),k=g(i).attr("ydata"),l="undefined"!=typeof g(i).attr("xrange")?JSON.parse(g(i).attr("xrange")):[],m="undefined"!=typeof g(i).attr("yrange")?JSON.parse(g(i).attr("yrange")):[],n="undefined"!=typeof g(i).attr("xmask")?JSON.parse(g(i).attr("xmask")):[],o="undefined"!=typeof g(i).attr("ymask")?JSON.parse(g(i).attr("ymask")):[],p="undefined"!=typeof g(i).attr("xlabel")?g(i).attr("xlabel"):"x axis",q="undefined"!=typeof g(i).attr("ylabel")?g(i).attr("ylabel"):"y axis",r="undefined"!=typeof g(i).attr("textcolor")?g(i).attr("textcolor"):"#000",s="undefined"!=typeof g(i).attr("width")?-1==g(i).attr("width").indexOf("px")?g(i).attr("width")+"px":g(i).attr("width"):g(i).width()>0?g(i).width():"500px",t="undefined"!=typeof g(i).attr("height")?-1==g(i).attr("height").indexOf("px")?g(i).attr("height")+"px":g(i).attr("height"):g(i).height()>0?g(i).height():"220px",u="undefined"!=typeof g(i).attr("enableoptions")?"true"===g(i).attr("enableoptions"):!0,v="undefined"!=typeof g(i).attr("xy-chart-type")?g(i).attr("xy-chart-type"):"line",w={id:f,x:j,y:k,xrange:l,yrange:m,xmask:n,ymask:o,xlabel:p,ylabel:q,accentColor:r,width:s,height:t,enableOptions:u,chart_type:v};"undefined"!=typeof g(i).attr("xlimit")&&(w.xlimit=JSON.parse(g(i).attr("xlimit"))),"undefined"!=typeof g(i).attr("ylimit")&&(w.ylimit=JSON.parse(g(i).attr("ylimit"))),d.attachTo("#"+f,{config:w}),console.log("enabled?",u,typeof u),u&&c.attachTo("#"+f,{config:w,type:"xy"}),g("#"+f).height(),g("#"+f).css({border:"1px solid "+r}),console.log("eidth id",s),g("#"+f).css({position:"relative",height:t,width:s,overflow:"hidden"}),g("#"+f).trigger("go");break;case"plot.xyz":a.rendererToDomId[f]={};var j=g(i).attr("xdata"),k=g(i).attr("ydata"),x=g(i).attr("zdata"),l="undefined"!=typeof g(i).attr("xrange")?JSON.parse(g(i).attr("xrange")):[],m="undefined"!=typeof g(i).attr("yrange")?JSON.parse(g(i).attr("yrange")):[],y="undefined"!=typeof g(i).attr("zrange")?JSON.parse(g(i).attr("zrange")):[],n="undefined"!=typeof g(i).attr("xmask")?JSON.parse(g(i).attr("xmask")):[],o="undefined"!=typeof g(i).attr("ymask")?JSON.parse(g(i).attr("ymask")):[],z="undefined"!=typeof g(i).attr("zmask")?JSON.parse(g(i).attr("zmask")):[],p="undefined"!=typeof g(i).attr("xlabel")?g(i).attr("xlabel"):"x axis",q="undefined"!=typeof g(i).attr("ylabel")?g(i).attr("ylabel"):"y axis",A="undefined"!=typeof g(i).attr("zlabel")?g(i).attr("zlabel"):"z axis",B="undefined"!=typeof g(i).attr("terrain")?!0:!1,C="undefined"!=typeof g(i).attr("spherical")?!0:!1,D=!1;if(0==g(i).attr("colorbar").indexOf("http://")||0==g(i).attr("colorbar").indexOf("https://")){D=!0;var E=g(i).attr("colorbar")}else var E="undefined"!=typeof g(i).attr("colorbar")?g(i).attr("colorbar").split(","):!1;var r="undefined"!=typeof g(i).attr("textcolor")?g(i).attr("textcolor"):"#fff",F="undefined"!=typeof g(i).attr("riftview")?!0:!1,G="undefined"!=typeof g(i).attr("rotating")?!0:!1,s="undefined"!=typeof g(i).attr("width")?-1==g(i).attr("width").indexOf("px")?g(i).attr("width")+"px":g(i).attr("width"):g(i).width()>0?g(i).width():"500px",t="undefined"!=typeof g(i).attr("height")?-1==g(i).attr("height").indexOf("px")?g(i).attr("height")+"px":g(i).attr("height"):g(i).height()>0?g(i).height():"220px",u="undefined"!=typeof g(i).attr("enableoptions")?"true"===g(i).attr("enableoptions"):!0;if("undefined"!=typeof g(i).attr("format"))switch(g(i).attr("format")){case"spherical":C=!0,B=!1;break;case"sphericalterrain":C=!0,B=!0;break;case"flatterrain":case"terrain":B=!0,C=!1;break;case"flat":B=!1,C=!1}var w={id:f,x:j,y:k,z:x,xrange:l,yrange:m,zrange:y,xmask:n,ymask:o,zmask:z,xlabel:p,ylabel:q,zlabel:A,isTerrain:B,isSpherical:C,colors:E,accentColor:r,isRift:F,isRotating:G,width:s,height:t,enableOptions:u};if("undefined"!=typeof g(i).attr("xlimit")&&(w.xlimit=JSON.parse(g(i).attr("xlimit"))),"undefined"!=typeof g(i).attr("ylimit")&&(w.ylimit=JSON.parse(g(i).attr("ylimit"))),"undefined"!=typeof g(i).attr("zlimit")&&(w.zlimit=JSON.parse(g(i).attr("zlimit"))),D){var H=g.ajax({url:E,dataType:"jsonp",type:"get"});H.success(function(b){console.log("colorbar request received",b);var d=[];g.each(b.data,function(b,c){var e=a.rgbToHex(c[0],c[1],c[2]);d.push(e)}),w.colors=d,console.log("color arr adjusted to",d,b),console.log("rendering xyz with ",a.rendererToDomId[f]),e.attachTo("#"+f,{config:w,dependencyBaseUrl:a.attr.config.widgetHostUrlBase}),g("#"+f).height(),g("#"+f).css({border:"1px solid "+r}),console.log("eidth id",s),g("#"+f).css({position:"relative",height:t,width:s}),console.log("enabled?",u,typeof u),u&&c.attachTo("#"+f,{config:w,type:"xyz"}),g("#"+f).trigger("go"),console.log("widget",g("#"+f)),g("#"+f).trigger("showLoading")}),H.error(function(){w.colors=!1,console.log("rendering xyz with ",a.rendererToDomId[f]),e.attachTo("#"+f,{config:w,dependencyBaseUrl:a.attr.config.widgetHostUrlBase}),g("#"+f).height(),g("#"+f).css({border:"1px solid "+r}),console.log("eidth id",s),g("#"+f).css({position:"relative",height:t,width:s}),console.log("enabled?",u,typeof u),u&&c.attachTo("#"+f,{config:w,type:"xyz"}),g("#"+f).trigger("go"),console.log("widget",g("#"+f)),g("#"+f).trigger("showLoading")})}else console.log("rendering xyz with ",a.rendererToDomId[f]),e.attachTo("#"+f,{config:w,dependencyBaseUrl:a.attr.config.widgetHostUrlBase}),g("#"+f).height(),g("#"+f).css({border:"1px solid "+r}),console.log("eidth id",s),g("#"+f).css({position:"relative",height:t,width:s}),console.log("enabled?",u,typeof u),u&&c.attachTo("#"+f,{config:w,type:"xyz"}),g("#"+f).trigger("go"),console.log("widget",g("#"+f)),g("#"+f).trigger("showLoading")}b++;var I=0,J=setInterval(function(){20==I&&clearInterval(J),I++},500)})})},this.rgbToHex=function(a,b,c){function d(a){var b=a.toString(16);return 1==b.length?"0"+b:b}var e=d3.scale.linear().domain([0,1]).range([0,255]);return a=Math.round(e(a)),b=Math.round(e(b)),c=Math.round(e(c)),"#"+d(a)+d(b)+d(c)},this.injectCSS=function(){var a=[this.attr.config.widgetHostUrlBase+"/components/nvd3/src/nv.d3.css?cache="+new Date,this.attr.config.widgetHostUrlBase+"/js/external/jquery-ui/css/theme/jquery-ui-1.10.2.custom.css?cache="+new Date,this.attr.config.widgetHostUrlBase+"/css/plotWidgets.css?cache="+new Date,this.attr.config.widgetHostUrlBase+"/components/rickshaw/rickshaw.css?cache="+new Date];g.each(a,function(a,b){var c=document.createElement("link");c.type="text/css",c.rel="stylesheet",c.href=b,document.getElementsByTagName("head")[0].appendChild(c)})},this.nullValue=-32767,this.after("initialize",function(){this.injectCSS(),this.harvestWidgetsFromPage(),this.on("callPlotAPI",this.initPlotAPI)})}return a(h)});
