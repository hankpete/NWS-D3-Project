<!DOCTYPE html>
<html lang="en">

	<head>		
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<meta http-equiv="refresh" content="3600">
		<title>hnxSA</title>
		<script type="text/javascript" src="d3/d3.v3.js"></script>
		<script type="text/javascript" src="d3/topojson.v1.min.js"></script>
		<script type="text/javascript" src="d3/togeojson.js"></script>
		<script type="text/javascript" src="d3/queue.v1.min.js"></script>
		<style type="text/css">

rect {

  pointer-events: all;
}

			body
			{	
				height: 100%;
				width: 100%;
				margin: 0;
				float: left;
				background-color:white;
				overflow:hidden;
			}

			div#map
			{
				position:absolute;
				top:0%;
				left:5%;
				height:100%;
				width:100%;
				fill:green;
 				fill-opacity:20%;
				stroke-width: .5px;
				stroke:gray;
				stroke-opacity:50%;
				z-index:-100;
				pointer-events: all;
			}

			.zoom {
				fill: #ccc;
				cursor: pointer;
			}

			.zoom.active {
				fill: gray;
			}

			.mesh {
				fill: none;
				stroke: #fff;
				stroke-linejoin: round;
			}


			div#info
			{
				position:absolute;
				top:5%;
				left:2%;
				height:100%;
				width:45%;
				fill:white;
 				fill-opacity:10%;
				stroke:black;
				stroke-opacity:100%;
				z-index:-1000;			
			}

			.infoText
			{
				stroke:black;
				stroke-width:1;
				fill:black;
				fill-opacity:100%;
				font-size:200%;
			}

			.mappedStationMarker
			{
				cursor: pointer;			
			}

			.mappedStationText
			{
				stroke:black;
				stroke-width:1;
				fill:black;
				fill-opacity:100%;
				font-size:90%;
			}

			.currentMappedStation
			{					
				fill:yellow;
				fill-opacity:50%;
				stroke:yellow;
				stroke-width:2;
			}

			.most
			{					
				
				fill-opacity:65%;
				stroke:red;
				stroke-width:2;
			}

			.currentMappedStationText
			{	
				stroke:black;
				stroke-width:0;
				fill:black;
				fill-opacity:100%;
				font-size:55%;
			}
	
		</style>

	</head>

<body> 

<div id="map"></div>
<div id="info"></div>

    <script type="text/javascript">

	//start global variables
	var width = screen.width
	var height = screen.height
	var active
	var activePoint

	var JsonToMap
	var b,s,t;
	var projection = d3.geo.mercator().scale(1).translate([0, 0])
	var cwaPath = d3.geo.path().projection(projection)	
	var scaleFactor = .9 //1=100% .5=50%, etc.
	var zoomTime = 1000

	//mainDataObject
	var mDO={"maxt":[],"mint":[],"lats":[],"lons":[]}

	var map = d3.select("#map").append("svg")	
		.attr("width", "100%")
		.attr("height", "100%")

	map.append("rect")
		.attr("width", width)
		.attr("height", height)
		.attr("fill","none")
		.on("click", reset);
	
	var mappedElems = map.append("g")
		.attr("id","mapped elements")

	var zones = mappedElems.append("g")
		.attr("id","zones")
	
	var forecasts = mappedElems.append("g")
		.attr("id","forecasts")

	//var days = mappedElems.append("g")		

	var obs = mappedElems.append("g")
		.attr("id","obs")

	var info = d3.select("#info").append("svg")	
		.attr("width", "100%")
		.attr("height", "100%")

	var infoG = info.append("g")

	var obsUrl = "ba-simple-proxy/ba-simple-proxy.php?&url=http://www.wrh.noaa.gov/hnx/JimBGmwXJList.php?extents=34.74,-121.4,38.36,-117.62&mode=native"

// newer
	var lats
	var lons
	var maxt=[]
	var mint=[]	
//                                                                                                                 day  lat   lon	
	var AllMaxTUrl= "ba-simple-proxy/ba-simple-proxy.php?&url=http://hnx-s-001:18080/data/FcstMaxT.nc/MaxT_SFC[0:7,0:159,0:133]?output=json"
	var AllMinTUrl= "ba-simple-proxy/ba-simple-proxy.php?&url=http://hnx-s-001:18080/data/FcstMinT.nc/MinT_SFC[0:7,0:159,0:133]?output=json"
	var latUrl = "ba-simple-proxy/ba-simple-proxy.php?&url=http://hnx-s-001:18080/data/FcstMaxT.nc/latitude[0:159,0:118]?output=json"
	var lonUrl = "ba-simple-proxy/ba-simple-proxy.php?&url=http://hnx-s-001:18080/data/FcstMaxT.nc/longitude[0:159,0:118]?output=json"	
	//var MaxT2Url= "ba-simple-proxy/ba-simple-proxy.php?&url=http://hnx-s-001:18080/data/FcstMaxT.nc/MaxT_SFC[1:2,0:159,0:133]?output=json"

	var latcor=(projection([[0],[33]])[1])
	var loncor=(projection([[118],[0]])[0])		

	var parm
	var parm1
	var parm2
	var coords
	var zone

	var dwellTime=1000000


	var tempColor = d3.scale.threshold()
			.domain([55,60,65,70,75,80,85,90,95,100])
			.range(["#5e4fa2","#3288bd","#66c2a5","#abdda4","#e6f598","#ffffbf","#fee08b","#fdae61","#f46d43","#d53e4f","#9e0142"]);

	var tempColor = d3.scale.linear()
			.domain([-40,0,10,20,32,40,50,60,70,80,90,100,130])
			.range(["white","#5e4fa2","#3288bd","#66c2a5","#abdda4","#e6f598","#ffffbf","#fee08b","#fdae61","#f46d43","#d53e4f","#9e0142","black"])
			.interpolate(d3.interpolateHcl);

	var windColor = d3.scale.threshold()
			.domain([0,5,30,50,100])
			.range(["white", "blue", "green", "yellow", "orange", "red"]);


	//end global variables
	function getData()
	{//data is loaded in order: lat, lon, MaxT, MinT.  Once all data is loaded, its all sent to prep data, where the 7 days of temps are d3.merged
		queue(1)
			.defer(d3.json,latUrl)
			.defer(d3.json,lonUrl)			
			.defer(d3.json,AllMaxTUrl)
			.defer(d3.json,AllMinTUrl)			
			.awaitAll(prepData)
	}

	function prepData(error,d)
	{	
		console.log("prepData d:",d)

		lats=d3.merge(d[0].contents.data)
		lons=d3.merge(d[1].contents.data)	
	
		for (i=0;i<7;i++)
		{
			maxt[i]=d3.merge(d[2].contents.data[i])
			mint[i]=d3.merge(d[3].contents.data[i])
			mDO.maxt[i]=maxt[i]
			mDO.mint[i]=mint[i]		
		}

	mDO.lats=lats
	mDO.lons=lons

	lats=[]
	lons=[]
	
	// get inidices of keeper values
	keepers=[]
	mDO.maxt[0].filter(function(value,index){if (value > -30000){keepers.push(index);return value}})
	//console.log(keepers)

	//filter maxt and mint for filler
	for(i=0;i<7;i++)
	{	
		mDO.maxt[i]=mDO.maxt[i].filter(function(value){if (value > -30000){return value}})
		mDO.mint[i]=mDO.mint[i].filter(function(value){if (value > -30000){return value}})
	}

	//filter lat and lon for keeper values of maxt and mint
	s=0
	for (t=0;t<keepers.length;t++)
	{	lats[s]=mDO.lats[keepers[t]]
		lons[s]=mDO.lons[keepers[t]]
	s++
	}
	mDO.lats=lats
	mDO.lons=lons
	console.log(mDO)


	//mapStationMarkers(data,"maxt","6")
	//mapStationMarkers(data,"mint","0")

	for (i=0;i<6;i++)
	{		
		e=["mint","maxt"]
		for(f=0;f<2;f++){
			mapStationMarkers(mDO,e[f],i)
		}
	}	

/*
	for (i=0;i<6;i++)
	{				
		mapStationMarkers(mDO,"maxt",i)
	}

*/
	} //end prepData

	function mapStationMarkers(d,elem,day)
	{		
		//console.log("d."+elem+"["+day+"] "+d[elem][day])
		//selectAll(".mappedStationMarker").remove()
		var markers=forecasts.append("g").attr("id",elem+day).style("opacity", 0).selectAll("mappedStationMarker").data(d[elem][day])		
		
		markers.enter().append("circle")			
			.attr("class","mappedStationMarker")
			.attr("id",function(d) { return d})
			.attr("cx", function(d,i) { return projection([lons[i],lats[i]])[0]})
			.attr("cy", function(d,i) { return projection([lons[i],lats[i]])[1]})
			.attr("r","3.5")
			//.attr("width", "5")
			//.attr("height","5")
			.attr("stroke-width","0")
			.attr("fill", function(d) {return tempColor(d) })
			.attr("fill-opacity", "100%")

		//markers.transition().style("opacity",1).duration(100)		
		//markers.exit().remove()
	}

function loadZoneMap()
{
	//project to bounding box http://bl.ocks.org/mbostock/4707858
	//zoom to bounding box http://bl.ocks.org/mbostock/4699541
	//d3.json("CWAs.json", function(error, us) 
	//d3.json("CAzones.json", function(error, us) 
	d3.json("hnxSaZones.json", function(error, us)
	{
		                  //.mesh/feature
		JsonToMap = topojson.mesh(us, us.objects.hnxsaZones)

		getBST(JsonToMap)
		projection.scale(s).translate(t);

		zones.selectAll("path")
			.data(topojson.feature(us, us.objects.hnxsaZones).features)
			.enter().append("path")
			.attr("id",  function(d) { return d.id; })
			.attr("zone",  function(d) { return d.properties.ZONE; })
			.attr("class", "zoom")
			.attr("d", cwaPath)
			.on("click", zoomToZone);
	}
	);
}

function zoomToPoint(d)
{
	st=getLST(d)
	zoomTo(st[0],st[1])
	if (activePoint === d) return reset();
	mappedElems.selectAll(".active").classed("activePoint", false);
	d3.select(this).classed("active", activePoint = d);
}

function getLST(d) //Boundingbox, Scale, Transform
{
	lon=projection([d.longitude, d.latitude])[0]
	lat=projection([d.longitude, d.latitude])[1]
	sf = scaleFactor / Math.max( (lon/width, lat/height))   //calculate scale based on ratio of bounding box to screen dimensions
	console.log("scale factor: ",sf)
	//s=50
	s=sf*5
	t=[width/2 -lon*s,height/2-lat*s]
	return[t,s]
}

function zoomTo(trans, scale) 
{ 	
	mappedElems.transition().duration(zoomTime).attr("transform","translate(" + trans + ")"+ "scale(" + scale + ")" )
}

function getBST(JsonToMap) //Boundingbox, Scale, Transform
{
	b = cwaPath.bounds(JsonToMap)								      //compute bounding box b of JsonToMap (d) 
	s = scaleFactor / Math.max( (b[1][0] - b[0][0]) / (width), (b[1][1] - b[0][1]) / (height) )   //calculate scale based on ratio of bounding box to screen dimensions
	t = [(width - s * (b[1][0] + b[0][0])) / 2, (height - s * (b[1][1] + b[0][1])) / 2] 	      //translate to center of bounding box
	return[t,s]
}

function zoomToZone(d)
{
	st=getBST(d)
	zoomTo(st[0],st[1])
	if (active === d) return reset();
	mappedElems.selectAll(".active").classed("active", false);
	d3.select(this).classed("active", active = d);
}

function reset() 
{
	console.log("reset")
	mappedElems.selectAll(".active").classed("active", active = false);
	mappedElems.transition().duration(zoomTime).attr("transform", "");
}


	function pointInPolygon(projLon,projLat,vs) 
	{
	// from https://github.com/substack/point-in-polygon      
        // ray-casting algorithm based on
        // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
	        
        	x = projLon,
        	y = projLat,
        	inside = false;
        	for (var i = 0, j = vs.length - 1; i < vs.length; j=i++) 
		{
		
			xi=parseFloat(vs[i].split(",")[0])
			yi=parseFloat(vs[i].split(",")[1])
			xj=parseFloat(vs[j].split(",")[0])
			yj=parseFloat(vs[j].split(",")[1])
	
			intersect = (  (yi > y) != (yj > y)) && (x<(xj-xi)*(y-yi)/(yj-yi)+xi  );

			if (intersect){inside = !inside}
		}
		return [inside,x,xi,xj,y,yi,yj,intersect]
	}
			
	function getZone()
	{
		pth = mappedElems.selectAll("[id=HNX]")  //get all the paths of the background map that are in the HNX cwa
		//console.log("jsonObs.stations.length: "+jsonObs.stations.length)
	
				ids = jsonObs.stations.map(function(e) {return e.id})

				pth.each(function(d, i)
				{
					console.log("zone: "+d3.select(this).attr("zone"))
					zone=d3.select(this).attr("zone")
					coords = [d3.select(this).attr("d")]
					coords[0]=coords[0].replace(new RegExp("[MZL]","g")," ")
					//console.log("coords: "+coords[0])
					coords=coords[0].split(" ")
					//console.log("coords after space split: "+coords)
					coords.shift()
					//console.log("coords after shift: "+coords)
					coords.pop()
					//console.log("final coords after pop: "+coords)

					console.log("ids.length: "+ids.length)
					for (k=0; k<ids.length;k++)
					{						
						j=jsonObs.stations[k]
						//console.log("j["+k+"].id: "+jsonObs.stations[k].id)
						rA=pointInPolygon( projection([j.longitude, j.latitude])[0], projection([j.longitude, j.latitude])[1], coords)
						isInside=rA[0]
						x=rA[1]
						xi=rA[2]
						xj=rA[3]
						y=rA[4]
						yi=rA[5]
						yj=rA[6]
						intersect = rA[7]

						if (isInside)
						{
							console.log("+++++>"+j.id+" is in zone: "+zone) //d3.select(this).attr("zone"))							
							pos = jsonObs.stations.map(function(e) {return e.id}).indexOf(j.id)
							//console.log("pos:"+pos)							
							jsonObs.stations[pos].zone=zone //d3.select(this).attr("zone");
							//ids.splice(k,1)    would like to optimize this by popping sites off ids array when zone is found
							//console.log("ids.length: "+ids.length)
							
	        			  	}
						else
						{
							//console.log("------------> Can't id zone for site: "+j.id+"    lat:"+j.latitude+" lon:"+j.longitude+" in zone: "+d3.select(this).attr("zone"))
						}
					}
					
				})//end pth.each function
			
	}// end getZone



	Array.prototype.sortByParms = function()	/*d.stations.sortBy('-Winds') d.stations.sortBy('-Winds','elev') */
							//http://stackoverflow.com/questions/1129216/sorting-objects-in-an-array-by-a-field-value-in-javascript	
	{
		function _sortByParms(p) 
		{
			var sortOrder = 1;
        		if (p[0] == "-")
			{
	        		sortOrder = -1
				p = p.substr(1)
		        }

        		return function(a,b) 
			{							
				a=a[p]
				b=b[p] 
				result = (a-b>0) ? 1 : (a-b<0) ? -1 : 0
				return result * sortOrder
		        }	
		}

		function _getSortFunc() 
		{
			if (arguments.length == 0)
			{
				throw "Zero length arguments not allowed for Array.sortBy()";
			}
			var args = arguments;
			return function(a, b) 
			{
				for (var result = 0, i = 0; result == 0 && i < args.length; i++) 
				{
					//console.log("sorting by: "+args[i])
					result = _sortByParms(args[i])(a, b)
				}
				return result
			}
		}
		return this.sort(_getSortFunc.apply(null, arguments))

	}

	function filterFalsyValues(element,index,arr)
	{
		if (parm1[0] == "-"){parm1 = parm1.substr(1);}else{parm1=parm1}
		//console.log(" index: "+index+" element["+parm1+"] "+element[parm1])		
		//  javascript falsy values): null undefined 0 "" (the empty string) false NaN
		return (element[parm1]) 
	}

	function mapMost(p1,p2)	
	{
		d=jsonObs.stations
		parm1=p1

		d=d.filter(filterFalsyValues) //
		d.sortByParms(p1,p2)
		d=d.slice(0,20)

		g.selectAll(".most").remove()
		g.selectAll("most").data(d).enter()
				.append("circle")
					.attr("class","most")
					.attr("id",  function(d) { return d.id; })
					.attr("cx", function(d) { return projection([d.longitude, d.latitude])[0]; })
					.attr("cy", function(d) { return projection([d.longitude, d.latitude])[1]; })
					.attr("r", "5")
					.attr("fill", function(d) {return tempColor(d.Temp)})
				/*.append("text")
					.attr("class", "currentFireText")
					.attr("x", function(d) { return projection([d.longitude, d.latitude])[0]; })
					.attr("y", function(d) { return projection([d.longitude, d.latitude])[1]; })					
					//.text(function(d, i) { return i+1+" "+": "+d.id+" "+parm1+" "+eval('d.'+parm1) })
					.text(function(d, i) { return d.zone})*/	
		
		infoG.selectAll(".currentFireText").remove();	
		infoG.selectAll("currentFireText").data(d).enter()
				.append("text")
					.attr("class", "currentFireText")
					.attr("x", "1%")
					.attr("y",function(d, i) { return 20+i*20; } )
					//.text(function(d, i) { return i+1+" "+": "+d.id+" "+parm1+" "+eval('d.'+parm1) })
					.text(function(d, i) { return i+1+" "+": "+d.id+" "+"zone: "+d.zone+" elevation"+" "+d["elev"]+"         "+parm1+"  "+d[parm1] })
		
	}


	function mapStationMarkers0(d)
	{		
		//console.log("msm d:",d)
		g.selectAll(".mappedStationMarker").remove()
	        g.selectAll("mappedStationMarker").data(d.stations).enter()
				.append("circle")
					.attr("id",  function(d) { return d.id; })
					.attr("class","mappedStationMarker")
					.attr("cx", function(d) { return projection([d.longitude, d.latitude])[0]; })
					.attr("cy", function(d) { return projection([d.longitude, d.latitude])[1]; })
		                	.attr("r", "1")
					.attr("stroke","1")
					.attr("fill", function(d) {return tempColor(d.Temp)})
					.attr("fill-opacity", "100%")
					.on('click' , function(d){ console.log(d.zone); });
	}

	function mapCurrentStation(d)	
	{		
		//console.log("d: ",d.id)
		g.selectAll(".currentMappedStation").remove();
	        g.append("circle").datum(d)
					.attr("class","currentMappedStation")
					.attr("id",  function(d) { return d.id; })					
					.attr("cx", function(d) { return projection([d.longitude, d.latitude])[0]; })
					.attr("cy", function(d) { return projection([d.longitude, d.latitude])[1]; })
		                	.attr("r", "10")
					.attr("stroke","1")
					.on('click' , function(d){ console.log(d.zone); });
		//zoomToPoint(d)
	}


	function mapStationText(d)
	{
		g.selectAll(".mappedStationText").remove();		
		g.selectAll("mappedStationText").data(d.stations).enter()
				.append("text")
					.attr("class", "mappedStationText")
					.attr("x", function(d) { return projection([d.longitude, d.latitude])[0]; })
					.attr("y", function(d) { return projection([d.longitude, d.latitude])[1]; })
					.text(function(d){return d.zone})
	}




//end functions

//main
loadZoneMap()
getData()
// every hour regrab obs and forecasts
	setInterval(function()
		{
			loadZoneMap()
			//getFireRss()
			getData()
		}, 3600000
	);


				//d3.select('#maxt'+l).transition().style("opacity",1).duration(interval*.1).transition().delay(interval*.6).style("opacity",.5).duration(interval*.3)				
interval=8000
e=["mint","maxt"]
//e=["mint"]
l=0
	setInterval(function()
			{	

				l = (l<6) ? l : 0			

					//d3.select("#"+e[i]+l).transition().style("opacity",1).duration(interval*.2).transition().delay(interval*.6).style("opacity",0).duration(interval*.2)
					d3.select("#mint"+l).transition().style("opacity",1).duration(interval*.2).transition().delay(interval*.6).style("opacity",0).duration(interval*.2)
					//d3.select("#maxt"+l).transition().style("opacity",1).duration(interval*.2).transition().delay(interval*.6).style("opacity",0).duration(interval*.2)
				
				l++		

			}, interval*2
	);





    </script>
    </body>
</html>
